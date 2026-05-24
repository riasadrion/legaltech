<?php

namespace App\Domain\Contract\Services;

use App\Domain\Contract\Models\Contract;

class ContractAiService
{
    /**
     * Build a contract template body from an admin description.
     * The AI produces full HTML with {{placeholders}} embedded
     * for every client-fillable value.
     */
    public function buildTemplate(string $description): string
    {
        $apiKey = setting('ai.openai_key');

        if (!$apiKey) {
            throw new \RuntimeException('OpenAI API key is not configured. Go to Admin → Settings → AI Setup.');
        }

        $systemPrompt = "You are a senior commercial contract lawyer with 20+ years of experience drafting enforceable agreements across multiple jurisdictions. "
            . "You write in formal legal English with precise terminology, clearly numbered clauses and sub-clauses, and unambiguous obligations. "
            . "Your drafts are professional, ready-to-sign, and structured for clarity. "
            . "You always include standard protective clauses (governing law, dispute resolution, severability, entire agreement, notices) appropriate to the contract type.";

        $systemPrompt .= "\n\nOUTPUT RULES:"
            . "\n- Return only HTML. No markdown, no code fences, no commentary."
            . "\n- Use <h1> for the contract title, <h2> for section headings, <p> for paragraphs, <ol>/<li> for numbered clauses."
            . "\n- For every value a client needs to provide (names, addresses, dates, amounts, jurisdictions, percentages, terms, etc.), insert a placeholder in the form {{snake_case_key}}."
            . "\n- Use descriptive keys (e.g. {{party_a_name}}, {{agreement_date}}, {{jurisdiction}}, {{party_a_profit_share}})."
            . "\n- Do NOT wrap placeholders in quotes or brackets — write them inline as natural sentence fragments."
            . "\n- Write every clause in full legal language. Do not leave sections empty or marked as TBD."
            . "\n- Include signature blocks at the end with placeholders for each party.";

        $client = \OpenAI::factory()
            ->withApiKey($apiKey)
            ->make();

        $response = $client->chat()->create([
            'model'       => setting('ai.openai_model', 'gpt-4o'),
            'messages'    => [
                ['role' => 'system', 'content' => $systemPrompt],
                ['role' => 'user',   'content' => "Generate a complete contract template for: {$description}"],
            ],
            'temperature' => (float) setting('ai.openai_temperature', 0.3),
            'max_tokens'  => (int) setting('ai.openai_max_tokens', 4000),
        ]);

        $html = trim($response->choices[0]->message->content ?? '');

        // Strip code fences if the model added them anyway
        $html = preg_replace('/^```(?:html)?\s*|\s*```$/m', '', $html);

        return trim($html);
    }

    /**
     * Draft a fully-written, ready-to-sign contract from the user's
     * own prompt. No placeholders — values are inlined directly.
     *
     * @param array<int, array{name:string,email:string,role:string}> $parties
     */
    public function draftFromUserBrief(string $brief, array $parties = []): array
    {
        $apiKey = setting('ai.openai_key');

        if (!$apiKey) {
            throw new \RuntimeException('AI contract generation is not configured. Please contact support.');
        }

        $partyLines = [];
        foreach ($parties as $p) {
            $partyLines[] = "- {$p['name']} <{$p['email']}> ({$p['role']})";
        }
        $partiesBlock = $partyLines ? "\n\nPARTIES TO INCLUDE:\n" . implode("\n", $partyLines) : '';

        $systemPrompt = "You are a senior commercial contract lawyer drafting an enforceable agreement for a paying client. "
            . "Write in formal legal English with precise terminology, clearly numbered clauses and sub-clauses, and unambiguous obligations. "
            . "Always include the standard protective clauses appropriate to the contract type (governing law, dispute resolution, severability, entire agreement, notices, signatures)."
            . "\n\nOUTPUT RULES:"
            . "\n- Return a JSON object with exactly two keys: \"title\" (a short, descriptive contract title) and \"body\" (the full contract HTML)."
            . "\n- The body must be complete HTML. Use <h1> for the contract title at the top, <h2> for sections, <p> for paragraphs, <ol>/<li> for numbered clauses."
            . "\n- Do NOT use any {{placeholders}} or [brackets] — every value must be written in full using the brief and party details below."
            . "\n- Include a signature block at the end with each party's name and a line for their signature and date."
            . "\n- No markdown, no code fences, no commentary outside the JSON object.";

        $client = \OpenAI::factory()
            ->withApiKey($apiKey)
            ->make();

        $response = $client->chat()->create([
            'model'           => setting('ai.openai_model', 'gpt-4o'),
            'response_format' => ['type' => 'json_object'],
            'messages'        => [
                ['role' => 'system', 'content' => $systemPrompt],
                ['role' => 'user',   'content' => "Draft the following contract:\n\n{$brief}{$partiesBlock}"],
            ],
            'temperature'     => (float) setting('ai.openai_temperature', 0.3),
            'max_tokens'      => (int) setting('ai.openai_max_tokens', 4000),
        ]);

        $raw  = $response->choices[0]->message->content ?? '';
        $json = json_decode($raw, true);

        $title = is_array($json) && !empty($json['title']) ? trim($json['title']) : 'Custom Contract';
        $body  = is_array($json) && !empty($json['body'])  ? trim($json['body'])  : trim($raw);

        // Strip stray code fences if any
        $body = preg_replace('/^```(?:html)?\s*|\s*```$/m', '', $body);

        return [
            'title' => $title,
            'body'  => trim($body),
        ];
    }

    /**
     * Final contract body = template body with {{placeholders}}
     * replaced by the client's wizard input.
     */
    public function generate(Contract $contract): string
    {
        $template  = $contract->template;
        $fieldData = $contract->field_data ?? [];

        $body = $template->body ?? '';

        foreach ($fieldData as $key => $value) {
            $body = str_replace('{{' . $key . '}}', e($value), $body);
        }

        return $body;
    }
}
