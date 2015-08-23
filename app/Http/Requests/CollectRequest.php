<?php

namespace ICT\Http\Requests;

use Carbon\Carbon;
use ICT\Event;
use ICT\Venue;
use ICT\Http\Requests\Request;

class CollectRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Sanitize input before validation.
     *
     * @return array
     */
    public function sanitize()
    {
        $rules = [];
        switch ($this->segment(1))
        {
            case 'events':
                $start_time = Carbon::createFromTimeStamp( strtotime($this->s_date.' '.$this->s_time) );
                $end_time = null;
                if ($this->e_date && $this->e_time)
                {
                    $end_time = Carbon::createFromTimeStamp( strtotime($this->e_date.' '.$this->e_time) );
                }

                $this->merge(['start_time' => $start_time, 'end_time' => $end_time]);
            break;
        }
        return $this->all();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [];
        switch ($this->segment(1))
        {
            case 'events':
                $rules = Event::$rules;
                // Make sure the facebook id is unique
                array_push($rules['facebook'], 'unique:events');
            break;
            case 'venues':
                $rules = Venue::$rules;
                // Make sure the facebook id is unique
                array_push($rules['facebook'], 'unique:venues');
            break;
        }
        return $rules;
    }

    public function attributes()
    {
        return [
            's_date' => 'start date',
            's_time' => 'start time',
            'e_date' => 'end date',
            'e_time' => 'end time',
        ];
    }

    public function messages()
    {
        return [
            'facebook.unique' => 'Good news: That event is already listed! Bad news: You just wasted your time. We\'ll let you now when we\'ve worked out the quirks in our time machine so you can get it back.',
            'street.required' => 'The location is invalid. There is missing street data.',
            'city.required' => 'The location is invalid. There is missing city data.',
            'state.required' => 'The location is invalid. There is missing state data.',
            'zip.required' => 'The location is invalid. There is missing zip code data.',
            'latitude.required' => 'The location is invalid. There is missing latitude data.',
            'longitude.required' => 'The location is invalid. There is missing longitude data.',
            'venue.name.required_without' => 'The location is invalid. There is missing name data.',
            'venue.street.required_without' => 'The location is invalid. There is missing street data.',
            'venue.city.required_without' => 'The location is invalid. There is missing city data.',
            'venue.state.required_without' => 'The location is invalid. There is missing state data.',
            'venue.zip.required_without' => 'The location is invalid. There is missing zip code data.',
            'venue.latitude.required_without' => 'The location is invalid. There is missing latitude data.',
            'venue.longitude.required_without' => 'The location is invalid. There is missing longitude data.'
        ];
    }
}
