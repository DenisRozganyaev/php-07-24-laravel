<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class RunQueueWorker extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:run-queue-worker';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run queues';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->callSilent('queue:work', [
            '--stop-when-empty' => true,
            '--queue' => 'default,wishlist,wishlist-notifications,listeners,admin-mail,admin-telegram',
            '--sleep' => 3,
            '--tries' => 3,
            '--max-time' => 60
        ]);
    }
}
