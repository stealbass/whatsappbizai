<?php

namespace App\Services;

use App\Models\Business;
use App\Models\Conversation;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class GeminiService
{
    private string $apiKey;
    private string $model;
    private string $baseUrl = 'https://generativelanguage.googleapis.com/v1beta';

    public function __construct()
    {
        $this->apiKey = config('gemini.api_key');
        $this->model  = config('gemini.model', 'gemini-2.5-flash');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // PUBLIC API
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Generate an AI reply for an incoming WhatsApp message.
     *
     * HOW IT WORKS — no training required:
     * 1. We build a rich system prompt that contains:
     *    - The business identity (name, city, country)
     *    - The full service/product catalog WITH prices
     *    - The owner's custom instructions (free text in the Filament admin)
     *    - Behavioural rules (conciseness, honesty, language detection)
     * 2. We attach the last N messages of the conversation as context
     * 3. Gemini uses all of this to generate a coherent, on-brand reply
     *
     * Result: every business gets its own personalised AI without any model
     * fine-tuning — just prompt engineering + catalog injection.
     */
    public function generateReply(
        Business     $business,
        Conversation $conversation,
        string       $userMessage
    ): ?string {
        $systemPrompt = $this->buildSystemPrompt($business);
        $history      = $this->buildHistory($conversation, 20);

        // Append the new user message
        $history[] = [
            'role'  => 'user',
            'parts' => [['text' => $userMessage]],
        ];

        try {
            $response = Http::timeout(30)->post(
                "{$this->baseUrl}/models/{$this->model}:generateContent?key={$this->apiKey}",
                [
                    'system_instruction' => [
                        'parts' => [['text' => $systemPrompt]],
                    ],
                    'contents'         => $history,
                    'generationConfig' => [
                        'temperature'     => 0.65,
                        'maxOutputTokens' => 1200,
                        'topP'            => 0.9,
                    ],
                    'safetySettings' => $this->safetySettings(),
                ]
            );

            if ($response->failed()) {
                Log::error('Gemini API error', [
                    'status' => $response->status(),
                    'body'   => $response->body(),
                ]);
                return null;
            }

            return $response->json('candidates.0.content.parts.0.text');

        } catch (\Throwable $e) {
            Log::error('Gemini exception', ['message' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Generate a draft reply suggestion for the human agent (not auto-sent).
     * Used in the Conversation view → "Suggest reply" button.
     */
    public function suggestReply(Business $business, Conversation $conversation): ?string
    {
        $systemPrompt = $this->buildSystemPrompt($business)
            . "\n\n[MODE: Suggestion pour l'agent humain. Rédige une réponse suggérée que l'agent pourra modifier avant d'envoyer.]";

        $history = $this->buildHistory($conversation, 10);

        try {
            $response = Http::timeout(20)->post(
                "{$this->baseUrl}/models/{$this->model}:generateContent?key={$this->apiKey}",
                [
                    'system_instruction' => ['parts' => [['text' => $systemPrompt]]],
                    'contents'           => $history,
                    'generationConfig'   => ['temperature' => 0.6, 'maxOutputTokens' => 800],
                    'safetySettings'     => $this->safetySettings(),
                ]
            );

            return $response->json('candidates.0.content.parts.0.text');
        } catch (\Throwable $e) {
            Log::error('Gemini suggestReply error', ['message' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Summarise a conversation into 2-3 sentences.
     */
    public function summarizeConversation(Conversation $conversation): string
    {
        $messages = $conversation->messages()
            ->where('type', 'text')
            ->orderBy('created_at')
            ->get()
            ->map(fn($m) => ($m->direction === 'inbound' ? 'Client' : 'Agent') . ': ' . $m->content)
            ->join("\n");

        if (empty($messages)) return '';

        $prompt = <<<PROMPT
Summarise this WhatsApp conversation in 2-3 sentences. Highlight:
- The client's main request
- Current status (resolved / pending / quote requested / etc.)
- Any amount or deadline mentioned

Conversation:
{$messages}
PROMPT;

        try {
            $response = Http::timeout(20)->post(
                "{$this->baseUrl}/models/{$this->model}:generateContent?key={$this->apiKey}",
                [
                    'contents'         => [['role' => 'user', 'parts' => [['text' => $prompt]]]],
                    'generationConfig' => ['maxOutputTokens' => 300, 'temperature' => 0.3],
                ]
            );

            return $response->json('candidates.0.content.parts.0.text', '');
        } catch (\Throwable $e) {
            Log::error('Gemini summarize error', ['message' => $e->getMessage()]);
            return '';
        }
    }

    /**
     * Generic chat completion — sends a single user prompt with an optional system instruction.
     * Used by Filament pages (BroadcastPage, RetentionCampaigns) for freeform AI drafting.
     *
     * @param  Business  $business  Used for context (model selection, system prompt base)
     * @param  string    $prompt    The user message / instruction
     * @param  string    $system    Optional system instruction override
     * @return string|null
     */
    public function chat(Business $business, string $prompt, string $system = ''): ?string
    {
        $systemInstruction = $system ?: $this->buildSystemPrompt($business);

        try {
            $response = Http::timeout(25)->post(
                "{$this->baseUrl}/models/{$this->model}:generateContent?key={$this->apiKey}",
                [
                    'system_instruction' => [
                        'parts' => [['text' => $systemInstruction]],
                    ],
                    'contents'         => [['role' => 'user', 'parts' => [['text' => $prompt]]]],
                    'generationConfig' => [
                        'temperature'     => 0.75,
                        'maxOutputTokens' => 800,
                        'topP'            => 0.9,
                    ],
                    'safetySettings' => $this->safetySettings(),
                ]
            );

            if ($response->failed()) {
                Log::error('GeminiService::chat error', [
                    'status' => $response->status(),
                    'body'   => $response->body(),
                ]);
                return null;
            }

            return $response->json('candidates.0.content.parts.0.text');

        } catch (\Throwable $e) {
            Log::error('GeminiService::chat exception', ['message' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Draft a marketing broadcast message for a given audience segment.
     */
    public function draftBroadcast(Business $business, string $goal, string $audience): string
    {
        $services = $this->catalogText($business);

        $prompt = <<<PROMPT
You are a marketing copywriter for "{$business->name}".
Write a WhatsApp marketing message (max 160 words) for the following:
- Goal: {$goal}
- Audience: {$audience}
- Services offered: {$services}

Rules:
- Start with an emoji
- Be friendly and direct
- Include a clear call-to-action
- Write in French by default, but include a short English version below if relevant
- Do NOT use HTML
PROMPT;

        try {
            $response = Http::timeout(20)->post(
                "{$this->baseUrl}/models/{$this->model}:generateContent?key={$this->apiKey}",
                [
                    'contents'         => [['role' => 'user', 'parts' => [['text' => $prompt]]]],
                    'generationConfig' => ['maxOutputTokens' => 500, 'temperature' => 0.8],
                ]
            );

            return $response->json('candidates.0.content.parts.0.text', '');
        } catch (\Throwable $e) {
            Log::error('Gemini draftBroadcast error', ['message' => $e->getMessage()]);
            return '';
        }
    }

    /**
     * Generate an overdue payment reminder message.
     */
    public function draftReminder(Business $business, \App\Models\Invoice $invoice): string
    {
        $contact     = $invoice->contact;
        $daysOverdue = now()->diffInDays($invoice->due_date);
        $amount      = number_format($invoice->total - $invoice->paid_amount, 0, ',', ' ');

        $prompt = <<<PROMPT
Write a polite but firm WhatsApp payment reminder for:
- Company sending: {$business->name}
- Client name: {$contact?->name ?? 'client'}
- Invoice number: {$invoice->number}
- Amount due: {$amount} {$invoice->currency}
- Days overdue: {$daysOverdue} days

Rules:
- Max 100 words
- Polite but professional tone
- Include the invoice number
- End with a question asking when they can pay
- Write in French; add English translation if the client's name suggests an anglophone context
PROMPT;

        try {
            $response = Http::timeout(20)->post(
                "{$this->baseUrl}/models/{$this->model}:generateContent?key={$this->apiKey}",
                [
                    'contents'         => [['role' => 'user', 'parts' => [['text' => $prompt]]]],
                    'generationConfig' => ['maxOutputTokens' => 300, 'temperature' => 0.5],
                ]
            );

            return $response->json('candidates.0.content.parts.0.text', '');
        } catch (\Throwable $e) {
            Log::error('Gemini draftReminder error', ['message' => $e->getMessage()]);
            return '';
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // SYSTEM PROMPT BUILDER — The "training" mechanism
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Builds the per-business system prompt.
     *
     * This is the CORE of the AI personalisation. Think of it as the
     * "knowledge base" injected into every conversation. It contains:
     *
     * LAYER 1 — Business identity (always present)
     *   Name, city, country, language preference, currency
     *
     * LAYER 2 — Service catalog (auto-injected from the Services module)
     *   All active services with name, description, price, unit
     *   The AI can quote prices, suggest services, and answer "how much?" questions
     *
     * LAYER 3 — Behavioural rules (hardcoded best practices)
     *   Language detection, conciseness, honesty, escalation rules
     *
     * LAYER 4 — Owner custom instructions (free text in admin panel)
     *   The business owner writes their own rules in plain language:
     *   "Never give discounts without approval"
     *   "Always ask for the delivery address"
     *   "Remind clients we are closed on Sundays"
     *   etc.
     *
     * NO GEMINI FINE-TUNING OR TRAINING IS NEEDED.
     * The prompt is rebuilt fresh on every message, so updating the catalog
     * or custom instructions takes effect immediately.
     */
    public function buildSystemPrompt(Business $business): string
    {
        $catalog      = $this->catalogText($business);
        $customRules  = $business->gemini_system_prompt
            ? "\n\n## Owner-defined rules (MUST follow these)\n" .
              strip_tags(html_entity_decode($business->gemini_system_prompt, ENT_QUOTES | ENT_HTML5, 'UTF-8'))
            : '';
        $docContext   = $this->documentsText($business);

        $langNote = match($business->country) {
            'CM', 'SN', 'CI', 'MA', 'BJ', 'TG', 'NE', 'ML', 'BF', 'GN', 'CG', 'CD', 'GA'
                => 'Primary language: French. You can also reply in English if the client writes in English.',
            'FR', 'BE', 'CH', 'LU'
                => 'Primary language: French.',
            'NG', 'GH', 'KE', 'ZA', 'TZ', 'UG'
                => 'Primary language: English. You can also reply in French if needed.',
            default
                => 'Detect the language the client is writing in and reply in that language. Default to French.',
        };

        return <<<SYSTEM
# Identity
You are the AI assistant for **{$business->name}**, a business based in {$business->city}, {$business->country}.
You respond on behalf of the business via WhatsApp.
Currency used: {$business->currency}.

# Language
{$langNote}
Always mirror the client's language — if they write in English, respond in English.

# Your role
You handle customer inquiries over WhatsApp:
1. **Welcome & qualify** — Greet new contacts warmly, understand their need
2. **Inform about services** — Answer questions about services and pricing
3. **Collect details for quotes** — Ask for all info needed to prepare a quote
4. **Confirm orders/appointments** — Acknowledge and confirm bookings
5. **Invoice follow-up** — Politely remind about overdue payments
6. **After-sales support** — Handle complaints and feedback with empathy

# Service catalog
{$catalog}
{$docContext}
# Strict rules
- **Be concise**: WhatsApp messages should be short (2-3 paragraphs max). No long essays.
- **Be honest**: Never invent prices or timelines not listed above. If unsure, say "I'll check and get back to you shortly."
- **Never make promises** you cannot guarantee.
- **Escalate when needed**: If the request is complex or the client is upset, say "I'll have our team contact you within 24 hours."
- **No HTML**: Plain text only. Use emojis sparingly and professionally.
- **Never confirm a deal**: Always end complex negotiations with "the team will finalise this with you."
- **Privacy**: Never share other clients' information.{$customRules}
SYSTEM;
    }

    // ─────────────────────────────────────────────────────────────────────────
    // PRIVATE HELPERS
    // ─────────────────────────────────────────────────────────────────────────

    private function catalogText(Business $business): string
    {
        $services = $business->services()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        if ($services->isEmpty()) {
            return "(No services configured yet — the business owner should add their services in the admin panel.)";
        }

        return $services->map(function ($s) {
            $price = $s->unit_price > 0
                ? number_format($s->unit_price, 0, ',', ' ') . ' ' . $s->currency . ' / ' . $s->unit
                : 'Price on request';
            $desc = $s->description ? " — {$s->description}" : '';
            return "• **{$s->name}**: {$price}{$desc}";
        })->join("\n");
    }

    private function documentsText(Business $business): string
    {
        $docs = $business->ai_documents ?? [];
        if (empty($docs)) return '';

        $sections = [];
        foreach ($docs as $doc) {
            $path = $doc['path'] ?? '';
            $name = $doc['name'] ?? 'document';
            $content = $this->extractDocumentContent($path);
            if ($content) {
                $sections[] = "### Document: {$name}\n{$content}";
            } else {
                $sections[] = "### Document: {$name}\n(Contenu non extractible — le document existe mais le texte n'a pas pu être lu automatiquement. Mentionne-le si pertinent.)";
            }
        }

        return "\n# Reference documents\n" . implode("\n\n", $sections) . "\n";
    }

    private function extractDocumentContent(string $path): ?string
    {
        try {
            $fullPath = Storage::disk('public')->path($path);
            if (!file_exists($fullPath)) return null;

            $ext = strtolower(pathinfo($fullPath, PATHINFO_EXTENSION));

            return match ($ext) {
                'txt', 'csv', 'md' => $this->readTextFile($fullPath),
                'pdf' => $this->readPdf($fullPath),
                'doc', 'docx' => $this->readWord($fullPath),
                'xls', 'xlsx' => $this->readExcel($fullPath),
                'ppt', 'pptx' => $this->readPowerPoint($fullPath),
                default => null,
            };
        } catch (\Throwable $e) {
            Log::warning('Document extraction failed', ['path' => $path, 'error' => $e->getMessage()]);
            return null;
        }
    }

    private function readTextFile(string $path): string
    {
        $content = file_get_contents($path);
        return mb_strlen($content) > 8000 ? mb_substr($content, 0, 8000) . "\n[...tronqué]" : $content;
    }

    private function readPdf(string $path): ?string
    {
        if (!class_exists('Smalot\PdfParser\Parser')) return null;
        $parser = new \Smalot\PdfParser\Parser();
        $pdf = $parser->parseFile($path);
        $text = $pdf->getText();
        return mb_strlen($text) > 8000 ? mb_substr($text, 0, 8000) . "\n[...tronqué]" : $text;
    }

    private function readWord(string $path): ?string
    {
        if (!class_exists('PhpOffice\PhpWord\IOFactory')) return null;
        $phpWord = \PhpOffice\PhpWord\IOFactory::load($path);
        $text = [];
        foreach ($phpWord->getSections() as $section) {
            foreach ($section->getElements() as $element) {
                if (method_exists($element, 'getText')) {
                    $text[] = $element->getText();
                }
            }
        }
        $content = implode("\n", $text);
        return mb_strlen($content) > 8000 ? mb_substr($content, 0, 8000) . "\n[...tronqué]" : $content;
    }

    private function readExcel(string $path): ?string
    {
        if (!class_exists('PhpOffice\PhpSpreadsheet\IOFactory')) return null;
        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($path);
        $text = [];
        foreach ($spreadsheet->getAllSheets() as $sheet) {
            foreach ($sheet->getRowIterator() as $row) {
                $cells = [];
                foreach ($row->getCellIterator() as $cell) {
                    $cells[] = $cell->getValue();
                }
                $text[] = implode(' | ', $cells);
            }
        }
        $content = implode("\n", $text);
        return mb_strlen($content) > 8000 ? mb_substr($content, 0, 8000) . "\n[...tronqué]" : $content;
    }

    private function readPowerPoint(string $path): ?string
    {
        if (!class_exists('PhpOffice\PhpPresentation\IOFactory')) return null;
        $ppt = \PhpOffice\PhpPresentation\IOFactory::load($path);
        $text = [];
        foreach ($ppt->getAllSlides() as $slide) {
            foreach ($slide->getShapeCollection() as $shape) {
                if (method_exists($shape, 'getText')) {
                    $text[] = $shape->getText();
                }
            }
        }
        $content = implode("\n", $text);
        return mb_strlen($content) > 8000 ? mb_substr($content, 0, 8000) . "\n[...tronqué]" : $content;
    }

    private function buildHistory(Conversation $conversation, int $limit): array
    {
        return $conversation->messages()
            ->where('type', 'text')
            ->whereNotNull('content')
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get()
            ->reverse()
            ->map(fn($m) => [
                'role'  => $m->direction === 'inbound' ? 'user' : 'model',
                'parts' => [['text' => $m->content]],
            ])
            ->values()
            ->toArray();
    }

    private function safetySettings(): array
    {
        return [
            ['category' => 'HARM_CATEGORY_HARASSMENT',       'threshold' => 'BLOCK_MEDIUM_AND_ABOVE'],
            ['category' => 'HARM_CATEGORY_HATE_SPEECH',       'threshold' => 'BLOCK_MEDIUM_AND_ABOVE'],
            ['category' => 'HARM_CATEGORY_SEXUALLY_EXPLICIT', 'threshold' => 'BLOCK_MEDIUM_AND_ABOVE'],
            ['category' => 'HARM_CATEGORY_DANGEROUS_CONTENT', 'threshold' => 'BLOCK_MEDIUM_AND_ABOVE'],
        ];
    }
}
