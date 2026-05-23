<?php

namespace App\Domain\Contract\Services;

use App\Domain\Contract\Models\Contract;
use App\Domain\Contract\Models\ContractTemplate;
use OpenAI\Laravel\Facades\OpenAI;

class ContractAiService
{
    public function generate(Contract $contract): string
    {
        $template  = $contract->template;
        $fieldData = $contract->field_data ?? [];
        $body      = $template->body;

        // Interpolate {{placeholders}} from field data
        foreach ($fieldData as $key => $value) {
            $body = str_replace('{{' . $key . '}}', $value, $body);
        }

        // Find and replace [AI_SECTION: ...] blocks
        $body = preg_replace_callback(
            '/\[AI_SECTION:\s*(.*?)\]/s',
            function (array $matches) use ($template, $fieldData) {
                $sectionPrompt = $matches[1];

                // Interpolate placeholders inside the AI section prompt too
                foreach ($fieldData as $key => $value) {
                    $sectionPrompt = str_replace('{{' . $key . '}}', $value, $sectionPrompt);
                }

                return $this->callOpenAI($sectionPrompt, $template);
            },
            $body
        );

        return $body;
    }

    private function callOpenAI(string $sectionPrompt, ContractTemplate $template): string
    {
        $apiKey = setting('ai.openai_key');

        if (!$apiKey) {
            throw new \RuntimeException('OpenAI API key is not configured. Go to Admin → Settings → AI Setup.');
        }

        $systemPrompt = $template->ai_system_prompt
            ?: 'You are a professional legal document drafter. Write clear, formal, and legally sound contract clauses in plain HTML paragraphs. Use <p> tags only. Do not include explanations or preamble — return only the clause text.';

        $client = \OpenAI::factory()
            ->withApiKey($apiKey)
            ->make();

        $response = $client->chat()->create([
            'model'    => setting('ai.openai_model', 'gpt-4o'),
            'messages' => [
                ['role' => 'system', 'content' => $systemPrompt],
                ['role' => 'user',   'content' => $sectionPrompt],
            ],
            'temperature' => (float) setting('ai.openai_temperature', 0.3),
            'max_tokens'  => (int) setting('ai.openai_max_tokens', 800),
        ]);

        return trim($response->choices[0]->message->content ?? '');
    }
}
