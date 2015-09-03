<?php

namespace ICT\Services;

use ICT\Venue;

class GoogleGeocoder {

    const API_URL = 'https://maps.google.com/maps/api/geocode/json?';
    const API_KEY = 'AIzaSyBfusPeEkmONUSeSDFTXg1YuJD7brTbbM8';
    const API_VERSION = '3';

    private $params = [];
    private $response;

    public function __construct( $address ) {
        $this->params['key'] = self::API_KEY;
        $this->params['address'] = $address;
    }

    public function getComponents() {
        $url = self::API_URL . http_build_query( $this->params );
        if ( ( $this->response = file_get_contents( $url ) ) === false ) {
            throw new \Exception("Unable to call API: {$url}");
        }
        $this->response = json_decode( $this->response );
        if ( is_null( $this->response ) ) {
            throw new \Exception('Unable to decode JSON response from geocoder.');
        }
        if ( $this->response->status !== 'OK' ) {
            throw new \Exception(sprintf( 'Geocoder failed: %s',( isset( $this->response->error_message ) ? $this->response->error_message : 'No error message returned' ) ));
        }
        $location = $this->response->results[0]->geometry->location;
        $components = [];
        $venue = new Venue();
        foreach($this->response->results[0]->address_components as $component) {
            foreach($component->types as $type) {
                // Grab the short name for states
                $components[$type] = ($type !== 'administrative_area_level_1') ? $component->long_name : $component->short_name;
            }
        }
        $components['latitude'] = $location->lat;
        $components['longitude'] = $location->lng;

        return $components;
    }

}