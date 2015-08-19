<?php

namespace ICT\Http\Requests;

use Carbon\Carbon;
use ICT\Event;
use ICT\Permission;
use ICT\Role;
use ICT\User;
use ICT\Venue;
use ICT\Http\Requests\Request;

class UpdateRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->hasPermission($this->segment(1).'.update');
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
            case 'users':
                $rules = User::$rules;
                $id = $this->route('users');
                $rules['email'] = ['required','email','unique:users,email,'.$id];
                $rules['password']  = ['confirmed','min:6'];
            break;
            case 'events':
                $rules = Event::$rules;
                $id = $this->route('id');
                $rules['facebook'] = ['numeric','unique:events,facebook,'.$id];
            break;
            case 'permissions':
                $rules = Permission::$rules;
            break;
            case 'roles':
                $rules = Role::$rules;
            break;
            case 'venues':
                $rules = Venue::$rules;
                $id = $this->route('id');
                $rules['facebook'] = ['numeric','unique:venues,facebook,'.$id];
                $rules['visible'] = ['required','boolean'];
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
}
