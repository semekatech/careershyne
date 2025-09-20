<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Smalot\PdfParser\Parser;
use OpenAI;

class AIReviewService
{
    protected $parser;
    protected $client;

    public function __construct()
    {
        $this->parser = new Parser();
        $this->client = OpenAI::client(env('OPENAI_API_KEY'));
    }

    /**
     * Store file and extract text
     */
    public function extractText($file)
    {
        $path = $file->store('cvs', 'public');
        $pdf = $this->parser->parseFile($file->getPathname());
        $text = $pdf->getText() ?? '';

        return [$path, $text];
    }

    /**
     * Build prompt with CV snippet
     */
    protected function buildPrompt(string $text): string
    {
        $prompt = <<<PROMPT
You are an expert HR professional.

Step 1: Determine if the provided text is a CV/resume.
- A CV/resume usually includes personal details, work experience, education, skills, etc.
- If it is NOT a CV/resume, return ONLY this JSON:

{
  "is_cv": false,
  "message": "Looks like you uploaded a wrong file. Please upload a valid CV in PDF format."
}

Step 2: If it IS a CV/resume, extract key details and review it. Return ONLY this JSON:

{
  "is_cv": true,
  "name": "Full Name if available",
  "email": "Email if available",
  "phone": "Phone if available",
  "strengths": ["bullet 1", "bullet 2"],
  "weaknesses": ["bullet 1", "bullet 2"],
  "suggestions": ["bullet 1", "bullet 2"],
  "impression": "overall impression",
  "score": 0
}

Requirements:
- Each bullet point must be 1–2 sentences long.
- Do not exceed 5 bullet points per section.
- Return ONLY JSON, no markdown, no extra commentary.

CV Content:
{CV}
PROMPT;

        $cvSnippet = trim(substr($text, 0, 4000));
        return str_replace('{CV}', $cvSnippet, $prompt);
    }

    /**
     * Call OpenAI API
     */
    public function analyzeCV(string $text): array
    {
        $prompt = $this->buildPrompt($text);

        $response = $this->client->chat()->create([
            'model' => 'gpt-4o-mini',
            'messages' => [
                ['role' => 'system', 'content' => 'You are a strict CV validator and reviewer.'],
                ['role' => 'user', 'content' => $prompt],
            ],
            'temperature' => 0.0,
            'max_tokens' => 800,
        ]);

        $raw = $response->choices[0]->message->content ?? '';
        $json = json_decode($raw, true);

        if (json_last_error() !== JSON_ERROR_NONE || !is_array($json)) {
            throw new Exception('AI returned an invalid response.');
        }

        return $json;
    }

    /**
     * Normalize review result
     */
    public function normalizeResult(array $json): array
    {
        return [
            'strengths'   => array_values(array_slice($json['strengths'] ?? [], 0, 3)),
            'weaknesses'  => array_values(array_slice($json['weaknesses'] ?? [], 0, 3)),
            'suggestions' => array_values(array_slice($json['suggestions'] ?? [], 0, 3)),
            'impression'  => $json['impression'] ?? '',
            'score'       => $json['score'] ?? null,
        ];
    }

    /**
     * Save to DB
     */
    public function saveReview(array $json, string $path, array $result)
    {
        DB::table('ai_reviews')->insert([
            'name'       => $json['name'] ?? null,
            'email'      => $json['email'] ?? null,
            'phone'      => $json['phone'] ?? null,
            'file_path'  => asset('storage/' . $path),
            'score'      => $json['score'] ?? null,
            'review'     => json_encode($result),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
