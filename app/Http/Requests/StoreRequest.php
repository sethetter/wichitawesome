<?php

namespace ICT\Http\Requests;

use Carbon\Carbon;
use ICT\Event;
use ICT\Permission;
use ICT\Role;
use ICT\Tag;
use ICT\User;
use ICT\Venue;
use ICT\Organization;
use ICT\Http\Requests\Request;

class StoreRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->hasPermission($this->segment(1).'.admin');
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
            break;
            case 'permissions':
                $rules = Permission::$rules;
            break;
            case 'roles':
                $rules = Role::$rules;
            break;
            case 'tags':
                $rules = Tag::$rules;
            break;
            case 'users':
                $rules = User::$rules;
            break;
            case 'venues':
                $rules = Venue::$rules;
            break;
            case 'organizations':
                $rules = Organization::$rules;
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
