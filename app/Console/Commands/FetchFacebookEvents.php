<?php

namespace ICT\Console\Commands;

use Illuminate\Console\Command;
use Schema;

use ICT\Services\FacebookEventFetcher;
use ICT\Venue;
use ICT\Organization;

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
    protected $organizations = [];


    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(facebookEventFetcher $fetcher)
    {

        parent::__construct();

        $this->fetcher = $fetcher;

        if (!Schema::hasTable('venues')) return;

        $this->venues = Venue::whereNotNull('facebook')->get();
        $this->organizations = Organization::whereNotNull('facebook')->get();

    }

    public function fetchEvents($resources)
    {
        $start = new \DateTime('NOW');
        $this->info("[{$start->format('Y-m-d g:i:s')}] Started fetching new Facebook events.");
        foreach($resources as $resource)
        {
            try {
                $this->info("Fetching events for {$resource->name} ({$resource->id})...");
                $this->fetcher->storeEvents($resource);
            } catch (\Exception $e) {
                $this->error("Error fetching events for {$resource->name}: {$e->getMessage()} ({$e->getLine()})");
            }
            sleep(1);
        }
        $finish = new \DateTime('NOW');
        $this->info("[{$finish->format('Y-m-d g:i:s')}] Finished fetching new Facebook events.");
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->fetchEvents($this->venues);
        $this->fetchEvents($this->organizations);
    }
}
