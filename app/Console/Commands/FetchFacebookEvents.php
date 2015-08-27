<?php

namespace ICT\Console\Commands;

use Illuminate\Console\Command;

use ICT\Services\FacebookEventFetcher;
use ICT\Venue;

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
     * @var fetcher
     */
    protected $fetcher;

    /**
     * The venues with faceobook profiles.
     *
     * @var venues
     */
    protected $venues = [];


    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(facebookEventFetcher $fetcher)
    {
        parent::__construct();

        $this->fetcher = $fetcher;

        if(\Schema::hasTable('venues')) {
            $this->venues = Venue::whereNotNull('facebook')->get();
        }

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
        foreach($this->venues as $index => $venue)
        {
            try {
                $this->info("Fetching events for {$venue->name} ({$venue->id})...");
                $this->fetcher->storeEventsForVenue($venue);
            } catch (\Exception $e) {
                $this->error("Error fetching events for {$venue->name}: {$e->getMessage()}");
            }
            sleep(1);
        }
        $finish = new \DateTime('NOW');
        $this->info("[{$finish->format('Y-m-d g:i:s')}] Finished fetching new Facebook events.");
    }
}
