<?php

namespace ICT\Services;

use ICT\Event;
use ICT\Venue;

use Illuminate\Http\Response;
use ICT\Services\GoogleGeocoder;


// @TODO: clean up this class. It needs some better organization

class FacebookEventFetcher 
{
    protected $version = 'v2.4';
    protected $access_token = 'CAAUm1QZCOPZCYBAEDiWXtB3jwyZAFfCKgNzTN4c8U9qA5ZBBQgnwVzUJye6P64Frbn04aRZAFkFZB4a8tR86gNoPCukFCBq8MmbMD7PzSE2WpiSjZBI6lnsHqCAvkW0uj4BoIt2H0QOqhIERyZBtvZCZCyafddl9Q4BUGZAFrNztiN9uHOthZCLBoWIa';
    protected $fields = ['name', 'description', 'start_time', 'end_time', 'updated_time','place'];
    protected $response;

    public function storeEvents($resource)
    {
        $events = $this->getPageEvents($resource->facebook);
        $venue = $resource;

        foreach($events as $event)
        {
            $venueId = isset($event->place->id) ? $event->place->id : null;
            if(!$venueId || $venueId != $resource->facebook) {
                $venue = Venue::where('name', 'like', $event->place->name)->first();
                if( !$venue ) {
                    echo 'No venue found in our database...';
                    if( isset($event->place->location) ) {
                        echo "getting location info from Facebook\n";
                        $venueData = [
                            'name' => $event->place->name,
                            'street' => $event->place->location->street,
                            'city' => $event->place->location->city,
                            'state' => $event->place->location->state,
                            'zip' => $event->place->location->zip,
                            'longitude' => $event->place->location->longitude,
                            'latitude' => $event->place->location->latitude,
                            'facebook' => $event->place->id,
                            'visible' => true
                        ];
                    } else {
                        echo "getting location info from Google Maps\n";
                        $geocoder = new GoogleGeocoder($event->place->name);
                        $components = $geocoder->getComponents();
                        $venueData = [
                            'name' => $event->place->name,
                            'street' => $components['street_number'].' '.$components['route'],
                            'city' => $components['locality'],
                            'state' => $components['administrative_area_level_1'],
                            'zip' => $components['postal_code'],
                            'longitude' => $components['longitude'],
                            'latitude' => $components['latitude'],
                            'visible' => true
                        ];
                    }
                    $venue = Venue::firstOrCreate($venueData);
                    echo "Venue created: {$venue->name} ({$venue->id})\n";
                }
            }

            $eventData = [
                'name' => $event->name,
                'description' => isset($event->description) ? $event->description : null,
                'start_time' => $this->castToDateTime($event->start_time),
                'end_time' => isset($event->end_time) ? $this->castToDateTime($event->end_time) : null,
                'facebook' => $event->id,
                'venue_id' => $venue->id,
                'updated_at' => $this->castToDateTime($event->updated_time),
            ];

            $this->updateOrCreate($eventData);
        }
    }

    /**
     * Get Facebook events for page.
     *
     * @param int $pageId
     *
     */
    public function getPageEvents($pageId) {
        $now = time();

        $fields = implode(',', $this->fields);
        $url = "https://graph.facebook.com/{$this->version}/{$pageId}/events?since={$now}&fields={$fields}&access_token={$this->access_token}";

        if ( ( $this->response = file_get_contents($url) ) === false ) {
            throw new \Exception('Unable to fetch '. $url);
        }
        $this->response = json_decode( $this->response );
        if ( is_null( $this->response ) ) {
            throw new \Exception('Unable to decode JSON response from Facebook.');
        }

        return $this->response->data;
    }

    /**
     * Update or Creates a new event based on Facebook id.
     *
     * @param array $attributes
     *
     */
    public function updateOrCreate($attributes)
    {
        $event = Event::where('facebook', '=', $attributes['facebook'])->withHidden()->first();
        if(!is_null($event)) {
            if($attributes['updated_at'] > $event->updated_at) {
                $event->update($attributes);
                echo "Event updated: {$event->name} ({$event->id})\n";
            }
        } else {
            $attributes['visible'] = true;
            $event = Event::create($attributes);
            echo "Event created: {$event->name} ({$event->id})\n";
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

