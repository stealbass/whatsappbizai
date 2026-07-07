<?php

namespace App\Filament\Resources\ConversationResource\Pages;

use App\Filament\Resources\ConversationResource;
use App\Services\GeminiService;
use App\Services\WhatsAppService;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewConversation extends ViewRecord
{
    protected static string $resource = ConversationResource::class;
    protected static string $view     = 'filament.pages.conversation-view';

    // Livewire state
    public string  $replyText      = '';
    public ?string $suggestedReply = null;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('toggle_ai')
                ->label(fn () => $this->record->ai_enabled ? __('app.admin.pause_ai') : __('app.admin.enable_ai'))
                ->color(fn () => $this->record->ai_enabled ? 'danger' : 'success')
                ->action(function () {
                    $this->record->update(['ai_enabled' => !$this->record->ai_enabled]);
                    $status = $this->record->ai_enabled ? __('app.admin.enable_ai') : __('app.admin.ai_paused');
                    Notification::make()->title($status)->success()->send();
                    $this->refreshFormData([]);
                }),

            Action::make('summarize_header')
                ->label(__('app.admin.ai_summary'))
                ->color('info')
                ->action(function (GeminiService $gemini) {
                    $this->runSummarize($gemini);
                }),

            Action::make('close')
                ->label(__('app.admin.close_conversation'))
                ->color('gray')
                ->requiresConfirmation()
                ->visible(fn () => $this->record->status === 'open')
                ->action(fn () => $this->record->update(['status' => 'closed', 'closed_at' => now()])),
        ];
    }

    /**
     * Toggle AI on/off — called from the blade via wire:click
     */
    public function toggleAI(): void
    {
        $this->record->update(['ai_enabled' => !$this->record->ai_enabled]);
        $status = $this->record->ai_enabled ? __('app.admin.enable_ai') : __('app.admin.ai_paused');
        Notification::make()->title($status)->success()->send();
    }

    /**
     * Generate and display an AI summary of the conversation
     */
    public function summarize(): void
    {
        $gemini  = app(GeminiService::class);
        $this->runSummarize($gemini);
    }

    /**
     * Ask Gemini to suggest a reply — shown in a banner above the chat
     */
    public function suggestReply(): void
    {
        $gemini = app(GeminiService::class);
        $business = auth()->user()?->business;

        if (!$business) {
            Notification::make()->title(__('app.admin.no_business'))->warning()->send();
            return;
        }

        $suggestion = $gemini->suggestReply($business, $this->record);

        if ($suggestion) {
            $this->suggestedReply = $suggestion;
        } else {
            Notification::make()->title(__('app.admin.suggestion_failed'))->warning()->send();
        }
    }

    /**
     * Copy the suggested reply into the reply text area
     */
    public function useSuggestion(): void
    {
        $this->replyText      = $this->suggestedReply ?? '';
        $this->suggestedReply = null;
    }

    /**
     * Send a manual reply via WhatsApp and store it in the conversation
     */
    public function sendManualReply(): void
    {
        $this->validate(['replyText' => 'required|string|max:4096']);

        $business = auth()->user()?->business;
        $contact  = $this->record->contact;

        if (!$business?->whatsapp_phone_number_id) {
            Notification::make()
                ->title(__('app.admin.whatsapp_not_configured'))
                ->body(__('app.admin.whatsapp_config_desc2'))
                ->danger()->send();
            return;
        }

        $whatsapp = app(WhatsAppService::class);
        $sent = $whatsapp->sendText(
            $contact->whatsapp_number,
            $this->replyText,
            $business->whatsapp_phone_number_id,
            $business->whatsapp_access_token
        );

        if ($sent) {
            // Store the outbound message in DB
            $this->record->messages()->create([
                'direction' => 'outbound',
                'type'      => 'text',
                'content'   => $this->replyText,
                'status'    => 'sent',
                'is_ai'     => false,
                'sent_at'   => now(),
            ]);

            $this->record->update(['last_message_at' => now()]);
            $this->replyText      = '';
            $this->suggestedReply = null;

            Notification::make()->title(__('app.admin.message_sent'))->success()->send();
        } else {
            Notification::make()->title(__('app.admin.send_failed'))->danger()->send();
        }
    }

    // ── private ──────────────────────────────────────────────────────────────

    private function runSummarize(GeminiService $gemini): void
    {
        $summary = $gemini->summarizeConversation($this->record);

        if ($summary) {
            $this->record->update(['summary' => $summary]);
            Notification::make()->title(__('app.admin.summary_generated'))->success()->send();
        } else {
            Notification::make()->title(__('app.admin.summary_failed'))->warning()->send();
        }
    }
}
