<?php

namespace App\Support;

use Illuminate\Support\Facades\Http;

class GoogleTranslate
{
    private static string $endpoint = 'https://translate.googleapis.com/translate_a/single';

    public static function translate(string $text, string $source, string $target): string
    {
        if ($source === $target || empty(trim($text))) {
            return $text;
        }

        $maxRetries = 2;
        $retryDelay = 1000; // 1 second

        for ($attempt = 0; $attempt <= $maxRetries; $attempt++) {
            try {
                \Log::debug('GoogleTranslate request', [
                    'text' => $text,
                    'source' => $source,
                    'target' => $target,
                    'attempt' => $attempt + 1,
                ]);

                $response = Http::timeout(15)
                    ->withHeaders([
                        'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                    ])
                    ->get(self::$endpoint, [
                        'client' => 'gtx',
                        'sl'     => $source,
                        'tl'     => $target,
                        'dt'     => 't',
                        'q'      => $text,
                    ]);

                \Log::debug('GoogleTranslate response', [
                    'status' => $response->status(),
                    'body_length' => strlen($response->body()),
                    'first_100_chars' => mb_substr($response->body(), 0, 100),
                ]);

                if ($response->successful()) {
                    $data = $response->json();

                    // Extract translation from the response array
                    $translated = '';
                    if (is_array($data) && isset($data[0]) && is_array($data[0])) {
                        foreach ($data[0] as $segment) {
                            if (is_array($segment) && isset($segment[0])) {
                                $translated .= $segment[0];
                            }
                        }
                    }

                    $result = trim($translated) ?: $text;
                    \Log::debug('GoogleTranslate success', [
                        'input' => $text,
                        'translated' => $result,
                        'raw_response' => $data,
                    ]);
                    return $result;
                } else if ($response->status() === 429) {
                    // Rate limited, retry with backoff
                    if ($attempt < $maxRetries) {
                        \Log::warning('GoogleTranslate rate limited (429), retrying...', [
                            'attempt' => $attempt + 1,
                            'body' => $response->body(),
                        ]);
                        usleep($retryDelay * 1000);
                        $retryDelay *= 2; // exponential backoff
                        continue;
                    }
                    \Log::warning('GoogleTranslate rate limited after retries', [
                        'status' => 429,
                        'text' => $text,
                        'body' => $response->body(),
                    ]);
                    return $text;
                } else {
                    \Log::warning('GoogleTranslate API error', [
                        'status' => $response->status(),
                        'body' => $response->body(),
                        'text' => $text,
                    ]);
                    return $text;
                }
            } catch (\Illuminate\Http\Client\ConnectionException $e) {
                if ($attempt < $maxRetries) {
                    \Log::warning('GoogleTranslate connection error, retrying...', [
                        'attempt' => $attempt + 1,
                        'error' => $e->getMessage(),
                    ]);
                    usleep($retryDelay * 1000);
                    $retryDelay *= 2;
                    continue;
                }
                \Log::error('GoogleTranslate connection failed after retries', [
                    'error' => $e->getMessage(),
                    'text' => $text,
                ]);
                return $text;
            } catch (\Exception $e) {
                \Log::error('GoogleTranslate exception', [
                    'error' => $e->getMessage(),
                    'text' => $text,
                    'attempt' => $attempt + 1,
                    'trace' => $e->getTraceAsString(),
                ]);
                if ($attempt < $maxRetries) {
                    usleep($retryDelay * 1000);
                    $retryDelay *= 2;
                    continue;
                }
                return $text;
            }
        }

        return $text;
    }

    public static function translateBatch(array $texts, string $source, string $target, int $delayMs = 300): array
    {
        $results = [];
        foreach ($texts as $key => $text) {
            $results[$key] = self::translate($text, $source, $target);
            if ($delayMs > 0) {
                usleep($delayMs * 1000);
            }
        }
        return $results;
    }
}
