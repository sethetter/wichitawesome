<?php

namespace ICT\Console\Commands;

use Illuminate\Console\Command;

use ICT\Services\facebookEventFetcher;

class FetchFacebookEvents extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fetch:facebook-events';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetches new facebook events for all venues.';

    /**
     * The event fetcher service.
     *
     * @var DripEmailer
     */
    protected $fetch;


    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(facebookEventFetcher $fetch)
    {
        parent::__construct();

        $this->fetch = $fetch;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $start = new \DateTime('NOW');
        $this->info("[{$start->format('Y-m-d g:i:s')}] Started fetching new Facebook events.");
        $this->fetch->storeEvents();
        $finish = new \DateTime('NOW');
        $this->info("[{$finish->format('Y-m-d g:i:s')}] Finished fetching new Facebook events.");
    }
}
