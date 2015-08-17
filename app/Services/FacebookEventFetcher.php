<?php

namespace ICT\Services;

use ICT\Venue;
use ICT\Event;

use Illuminate\Http\Response;

// @TODO: clean up this class. It needs some better organization

class FacebookEventFetcher 
{
    protected $version = 'v2.4';
    protected $access_token = '1450071418617846|xH9wnEYA25GVQYGBfgHGYJfWGaA';
    protected $fields = ['name', 'description', 'start_time', 'end_time', 'updated_time'];

    protected $venues;

    public function __construct()
    {
        $this->venues = Venue::where('facebook', '!=', 0)->get();
    }

    public function storeEvents()
    {
        foreach($this->venues as $venue)
        {
            $this->storeEventsForVenue($venue);
            sleep(5);
        }
    }

    public function storeEventsForVenue($venue)
    {
        $now = time();

        $fields = implode(',', $this->fields);
        $url = "https://graph.facebook.com/{$this->version}/{$venue->facebook}/events?since={$now}&fields={$fields}&access_token={$this->access_token}";

        $response = file_get_contents($url);
        if ($response === false) {
            throw new Exception('Stream returned an empty response', 660);
        }

        $events = json_decode($response)->data;

        foreach($events as $event)
        {
            $facebookData = [
                'name' => $event->name,
                'description' => isset($event->description) ? $event->description : null,
                'start_time' => $this->castToDateTime($event->start_time),
                'end_time' => isset($event->end_time) ? $this->castToDateTime($event->end_time) : null,
                'facebook' => $event->id,
                'venue_id' => $venue->id,
                'updated_at' => $this->castToDateTime($event->updated_time)
            ];
            $newEvent = Event::where('facebook', '=', $event->id)->first();
            if(!is_null($newEvent))
            {
                // @TODO: test that this comparison works.
                if($facebookData['updated_at'] > $newEvent->updated_at)
                {
                    $newEvent->update($facebookData);
                }
            } else {
                Event::create($facebookData);
            }
        }
    }

    /**
     * Casts a date value from Graph to DateTime.
     *
     * @param int|string $value
     *
     * @return \DateTime
     */
    public function castToDateTime($value)
    {
        if (is_int($value)) {
            $dt = new \DateTime();
            $dt->setTimestamp($value)->setTimezone($tz);
        } else {
            $tz = new \DateTimeZone('America/Chicago');
            $dt = new \DateTime($value);
            $dt->setTimezone($tz);
        }

        return $dt;
    }
}

