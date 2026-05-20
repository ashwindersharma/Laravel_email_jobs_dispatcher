<?php

namespace App\Jobs;

use App\Mail\CampaignMail;
use App\Models\Campaign;
use App\Models\Contact;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendCampaignChunkJob implements ShouldQueue {
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 300;

    public int $tries = 3;

    protected Campaign $campaign;

    protected array $contacts;

    /**
     * Create a new job instance.
     */
    public function __construct(Campaign $campaign, array $contacts) {
        $this->campaign = $campaign;
        $this->contacts = $contacts;
    }

    /**
     * Execute the job.
     */
    public function handle(): void {
        Log::info('CHUNK JOB STARTED', [
            'campaign_id' => $this->campaign->id,
            'contacts_count' => count($this->contacts),
            'job_id' => optional($this->job)->getJobId(),
            'attempts' => optional($this->job)->attempts(),
        ]);

        foreach ($this->contacts as $contact) {

            // If contact IDs are passed instead of full models
            if (is_numeric($contact)) {
                $contact = Contact::find($contact);

                if (!$contact) {
                    Log::warning('CONTACT NOT FOUND', [
                        'contact_id' => $contact,
                    ]);

                    continue;
                }
            }

            Log::info('ATTEMPTING EMAIL SEND', [
                'email' => $contact->email,
                'campaign_id' => $this->campaign->id,
            ]);

            try {

                Mail::to($contact->email)
                    ->send(new CampaignMail(
                        $this->campaign,
                        $contact
                    ));

                Log::info('EMAIL SENT SUCCESSFULLY', [
                    'email' => $contact->email,
                    'campaign_id' => $this->campaign->id,
                ]);
            } catch (\Throwable $e) {

                Log::error('EMAIL SEND FAILED', [
                    'email' => $contact->email,
                    'campaign_id' => $this->campaign->id,
                    'message' => $e->getMessage(),
                    'attempts' => optional($this->job)->attempts(),
                    'trace' => $e->getTraceAsString(),
                ]);

                throw $e;
            }
        }

        Log::info('CHUNK JOB COMPLETED', [
            'campaign_id' => $this->campaign->id,
        ]);
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void {
        Log::error('CHUNK JOB FAILED PERMANENTLY', [
            'campaign_id' => $this->campaign->id,
            'message' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString(),
        ]);
    }
}
