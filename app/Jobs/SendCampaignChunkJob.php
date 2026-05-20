<?php

namespace App\Jobs;

use App\Mail\CampaignMail;
use App\Models\Campaign;
use App\Models\CampaignRecipient;
use App\Services\TemplateParserService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use romanzipp\QueueMonitor\Traits\IsMonitored;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;


class SendCampaignChunkJob implements ShouldQueue {
    use Dispatchable,
        InteractsWithQueue,
        Queueable,
        SerializesModels,
        IsMonitored;

    public $tries = 3;
    public int $timeout = 300;
    /*
    |--------------------------------------------------------------------------
    | Retry Backoff
    |--------------------------------------------------------------------------
    */

    public function backoff(): array {
        return [60, 300, 900];
    }

    /*
    |--------------------------------------------------------------------------
    | Constructor
    |--------------------------------------------------------------------------
    */

    public function __construct(
        public array $recipientIds
    ) {

        Log::info('SEND CAMPAIGN CHUNK JOB CREATED', [
            'recipient_ids' => $this->recipientIds,
            'count' => count($this->recipientIds),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Queue Tags
    |--------------------------------------------------------------------------
    */

    public function tags(): array {
        return [
            'campaign-chunk',
            'emails',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Human Readable Name
    |--------------------------------------------------------------------------
    */

    public function displayName(): string {
        return sprintf(
            'Campaign Email Chunk [%s recipients]',
            count($this->recipientIds)
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Execute Job
    |--------------------------------------------------------------------------
    */

    public function handle(): void {

        Log::info('CHUNK JOB STARTED', [
            'recipient_ids' => $this->recipientIds,
            'count' => count($this->recipientIds),
            'attempts' => optional($this->job)->attempts(),
        ]);

        $parser = app(TemplateParserService::class);

        /*
        |--------------------------------------------------------------------------
        | Initial Monitor State
        |--------------------------------------------------------------------------
        */

        $this->queueProgress(0);

        $this->queueData([
            'chunk_size' => count($this->recipientIds),
            'status'     => 'initializing',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Load Recipients
        |--------------------------------------------------------------------------
        */

        $recipients = CampaignRecipient::with([
            'contact.meta',
            'campaign.template'
        ])
            ->whereIn('id', $this->recipientIds)
            ->get();

        Log::info('RECIPIENTS LOADED', [
            'loaded_count' => $recipients->count(),
        ]);

        $total = $recipients->count();

        $processed = 0;

        $success = 0;

        $failed = 0;

        /*
        |--------------------------------------------------------------------------
        | Process Emails
        |--------------------------------------------------------------------------
        */

        foreach ($recipients as $recipient) {

            try {

                Log::info('PROCESSING RECIPIENT', [
                    'recipient_id' => $recipient->id,
                    'email' => $recipient->contact->email ?? null,
                    'campaign_id' => $recipient->campaign_id,
                ]);

                /*
                |--------------------------------------------------------------------------
                | Mark Processing
                |--------------------------------------------------------------------------
                */

                sleep(3);

                $recipient->update([
                    'status' => 'processing'
                ]);

                $contact = $recipient->contact;

                $template = $recipient
                    ->campaign
                    ->template;

                /*
                |--------------------------------------------------------------------------
                | Build Placeholder Data
                |--------------------------------------------------------------------------
                */

                $data = [
                    'first_name' => $contact->first_name,
                    'last_name'  => $contact->last_name,
                    'email'      => $contact->email,
                ];

                /*
                |--------------------------------------------------------------------------
                | Dynamic Meta Fields
                |--------------------------------------------------------------------------
                */

                foreach ($contact->meta as $meta) {

                    $data[$meta->key] = $meta->value;
                }

                /*
                |--------------------------------------------------------------------------
                | Parse Template
                |--------------------------------------------------------------------------
                */

                Log::info('PARSING TEMPLATE', [
                    'email' => $contact->email,
                ]);

                $subject = $parser->parse(
                    $template->subject,
                    $data
                );

                $body = $parser->parse(
                    $template->body,
                    $data
                );

                /*
                |--------------------------------------------------------------------------
                | Send Email
                |--------------------------------------------------------------------------
                */

                Log::info('ATTEMPTING EMAIL SEND', [
                    'email' => $contact->email,
                    'subject' => $subject,
                ]);

                Mail::to($contact->email)
                    ->send(
                        new CampaignMail(
                            $subject,
                            $body
                        )
                    );

                Log::info('EMAIL SENT SUCCESSFULLY', [
                    'email' => $contact->email,
                ]);

                /*
                |--------------------------------------------------------------------------
                | Mark Success
                |--------------------------------------------------------------------------
                */

                $recipient->update([
                    'status'        => 'sent',
                    'sent_at'       => now(),
                    'error_message' => null,
                ]);

                $success++;

                /*
                |--------------------------------------------------------------------------
                | Update Campaign Status
                |--------------------------------------------------------------------------
                */

                $this->updateCampaignStatus(
                    $recipient->campaign_id
                );
            } catch (\Throwable $e) {

                Log::error('EMAIL SEND FAILED', [
                    'recipient_id' => $recipient->id ?? null,
                    'email' => $contact->email ?? null,
                    'campaign_id' => $recipient->campaign_id ?? null,
                    'message' => $e->getMessage(),
                    'attempts' => optional($this->job)->attempts(),
                    'trace' => $e->getTraceAsString(),
                ]);

                /*
                |--------------------------------------------------------------------------
                | Increment Retry Counter
                |--------------------------------------------------------------------------
                */

                $recipient->increment(
                    'retry_count'
                );

                /*
                |--------------------------------------------------------------------------
                | Mark Failure
                |--------------------------------------------------------------------------
                */

                $recipient->update([
                    'status'        => 'failed',
                    'error_message' => $e->getMessage(),
                ]);

                $failed++;

                /*
                |--------------------------------------------------------------------------
                | Update Monitor Data
                |--------------------------------------------------------------------------
                */

                $this->queueData([
                    'last_failed_email' => $contact->email ?? null,
                    'last_error'        => $e->getMessage(),
                ]);

                /*
                |--------------------------------------------------------------------------
                | Update Campaign Status
                |--------------------------------------------------------------------------
                */

                $this->updateCampaignStatus(
                    $recipient->campaign_id
                );
            }

            /*
            |--------------------------------------------------------------------------
            | Update Progress
            |--------------------------------------------------------------------------
            */

            $processed++;

            $progress = intval(
                ($processed / $total) * 100
            );

            $this->queueProgress($progress);

            /*
            |--------------------------------------------------------------------------
            | Update Queue Monitor Data
            |--------------------------------------------------------------------------
            */

            $this->queueData([
                'campaign_id'   => $recipient->campaign_id,
                'campaign_name' => $recipient->campaign->name ?? null,

                'chunk_size'    => $total,

                'processed'     => $processed,
                'remaining'     => max(
                    0,
                    $total - $processed
                ),

                'success'       => $success,
                'failed'        => $failed,

                'current_email' => $contact->email,

                'status'        => 'sending_emails',
            ]);
        }

        Log::info('CHUNK JOB COMPLETED', [
            'processed' => $processed,
            'success' => $success,
            'failed' => $failed,
        ]);

        /*
        |--------------------------------------------------------------------------
        | Final Progress
        |--------------------------------------------------------------------------
        */

        $this->queueProgress(100);

        /*
        |--------------------------------------------------------------------------
        | Final Queue Data
        |--------------------------------------------------------------------------
        */

        $this->queueData([
            'chunk_size' => $total,
            'processed'  => $processed,
            'success'    => $success,
            'failed'     => $failed,
            'status'     => 'completed',
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Update Campaign Status
    |--------------------------------------------------------------------------
    */

    protected function updateCampaignStatus(
        int $campaignId
    ): void {

        Log::info('UPDATING CAMPAIGN STATUS', [
            'campaign_id' => $campaignId,
        ]);

        /*
        |--------------------------------------------------------------------------
        | Check Remaining Recipients
        |--------------------------------------------------------------------------
        */

        $remaining = CampaignRecipient::where(
            'campaign_id',
            $campaignId
        )
            ->whereIn('status', [
                'pending',
                'processing'
            ])
            ->exists();

        /*
        |--------------------------------------------------------------------------
        | Campaign Still Running
        |--------------------------------------------------------------------------
        */

        if ($remaining) {

            Log::info('CAMPAIGN STILL RUNNING', [
                'campaign_id' => $campaignId,
            ]);

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | Determine Final Status
        |--------------------------------------------------------------------------
        */

        $hasFailures = CampaignRecipient::where(
            'campaign_id',
            $campaignId
        )
            ->where('status', 'failed')
            ->exists();

        $status = $hasFailures
            ? 'completed_with_failures'
            : 'completed';

        Log::info('FINAL CAMPAIGN STATUS DETERMINED', [
            'campaign_id' => $campaignId,
            'status' => $status,
        ]);

        /*
        |--------------------------------------------------------------------------
        | Update Campaign
        |--------------------------------------------------------------------------
        */

        Campaign::where(
            'id',
            $campaignId
        )->update([
            'status' => $status
        ]);
    }

    public function keepMonitorOnSuccess(): bool {
        return true;
    }
}
