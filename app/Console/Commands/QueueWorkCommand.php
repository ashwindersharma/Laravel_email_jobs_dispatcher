<?php

namespace App\Console\Commands;

use Illuminate\Queue\Console\WorkCommand;
use Illuminate\Queue\WorkerOptions;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'queue:work')]
class QueueWorkCommand extends WorkCommand
{
    /**
     * Gather queue worker options with integer casts for pcntl_alarm (PHP 8+).
     */
    protected function gatherWorkerOptions(): WorkerOptions
    {
        return new WorkerOptions(
            $this->option('name'),
            max((int) $this->option('backoff'), (int) $this->option('delay')),
            (int) $this->option('memory'),
            (int) $this->option('timeout'),
            (int) $this->option('sleep'),
            (int) $this->option('tries'),
            (bool) $this->option('force'),
            (bool) $this->option('stop-when-empty'),
            (int) $this->option('max-jobs'),
            (int) $this->option('max-time'),
            (int) $this->option('rest'),
        );
    }
}
