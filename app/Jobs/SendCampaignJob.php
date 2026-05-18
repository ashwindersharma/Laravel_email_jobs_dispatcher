<?php

namespace App\Jobs;

use App\Models\Campaign;
use App\Models\CampaignRecipient;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
// use romanzipp\QueueMonitor\Traits\IsMonitored;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;


class SendCampaignJob implements ShouldQueue {
    use Dispatchable,
        InteractsWithQueue,
        Queueable,
        SerializesModels;
    // IsMonitored;

    public $tries = 3;

    /*
    |--------------------------------------------------------------------------
    | Retry Backoff
    |--------------------------------------------------------------------------
    */

    public function backoff(): array {
        return [60, 70, 80];
    }

    /*
    |--------------------------------------------------------------------------
    | Constructor
    |--------------------------------------------------------------------------
    */

    public function __construct(
        public Campaign $campaign
    ) {
    }

    /*
    |--------------------------------------------------------------------------
    | Queue Monitor Tags
    |--------------------------------------------------------------------------
    */

    public function tags(): array {
        return [
            'campaign',
            'campaign:' . $this->campaign->id,
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Execute Job
    |--------------------------------------------------------------------------
    */

    public function handle(): void {
        /*
        |--------------------------------------------------------------------------
        | Initial Queue Data
        |--------------------------------------------------------------------------
        */

        $totalRecipients = CampaignRecipient::where(
            'campaign_id',
            $this->campaign->id
        )
            ->where('status', 'pending')
            ->count();

        // $this->queueData([
        //     'campaign_id'       => $this->campaign->id,
        //     'campaign_name'     => $this->campaign->name,
        //     'total_recipients'  => $totalRecipients,
        //     'status'            => 'initializing',
        // ]);

        // $this->queueProgress(0);

        /*
        |--------------------------------------------------------------------------
        | Update Campaign Status
        |--------------------------------------------------------------------------
        */

        $this->campaign->update([
            'status' => 'processing'
        ]);

        /*
        |--------------------------------------------------------------------------
        | Dispatch Chunk Jobs
        |--------------------------------------------------------------------------
        */

        $processedRecipients = 0;

        CampaignRecipient::where(
            'campaign_id',
            $this->campaign->id
        )
            ->where('status', 'pending')
            ->chunk(2, function ($recipients) use (
                &$processedRecipients,
                $totalRecipients
            ) {

                /*
            |--------------------------------------------------------------------------
            | Dispatch Chunk
            |--------------------------------------------------------------------------
            */

                SendCampaignChunkJob::dispatch(
                    $recipients->pluck('id')->toArray()
                );

                /*
            |--------------------------------------------------------------------------
            | Update Progress
            |--------------------------------------------------------------------------
            */

                $processedRecipients += $recipients->count();

                $progress = intval(
                    ($processedRecipients / $totalRecipients) * 100
                );

                // $this->queueProgress($progress);

                /*
            |--------------------------------------------------------------------------
            | Update Monitor Data
            |--------------------------------------------------------------------------
            */

                // $this->queueData([
                //     'campaign_id'          => $this->campaign->id,
                //     'campaign_name'        => $this->campaign->name,
                //     'total_recipients'     => $totalRecipients,
                //     'processed_recipients' => $processedRecipients,
                //     'remaining_recipients' => max(
                //         0,
                //         $totalRecipients - $processedRecipients
                //     ),
                //     'status'               => 'dispatching_chunks',
                // ]);
            });

        /*
        |--------------------------------------------------------------------------
        | Final Progress
        |--------------------------------------------------------------------------
        */

        // $this->queueProgress(100);

        /*
        |--------------------------------------------------------------------------
        | Final Queue Data
        |--------------------------------------------------------------------------
        */

        // $this->queueData([
        //     'campaign_id'          => $this->campaign->id,
        //     'campaign_name'        => $this->campaign->name,
        //     'total_recipients'     => $totalRecipients,
        //     'processed_recipients' => $processedRecipients,
        //     'status'               => 'completed',
        // ]);

    }

    /*
    |--------------------------------------------------------------------------
    | Human Readable Name
    |--------------------------------------------------------------------------
    */

    public function displayName(): string {
        return sprintf(
            'Campaign Dispatcher [%s]',
            $this->campaign->name
        );
    }

    // public function keepMonitorOnSuccess(): bool {
    //     return true;
    // }
}
