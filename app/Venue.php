<?php

namespace ICT;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use ICT\Traits\Eventable;
use ICT\Traits\Sluggable;
use ICT\Traits\Visibility;

class Venue extends Model
{
    use SoftDeletes, Eventable, Sluggable, Visibility;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'venues';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'street', 'city', 'state', 'zip', 'longitude', 'latitude', 'image', 'facebook', 'twitter', 'website', 'email', 'phone', 'visible'];
    
    /**
     * Set Carbon dates.
     * 
     * (default value: ['deleted_at'])
     * 
     * @var string
     * @access protected
     */
    protected $dates = ['deleted_at'];

    public static $rules = [
        'name' => ['required'],
        'facebook' => ['numeric','unique:venues,facebook'],
        'street' => ['required', 'min:3'],
        'city' => ['required', 'min:3'],
        'state' => ['required', 'alpha','size:2'],
        'zip' => ['required', 'min:5'],
        'latitude' => ['required', 'numeric'],
        'longitude' => ['required', 'numeric'],
        'description' => ['min:3','max:500'],
        'image' => ['image'],   
        'hashtag' => ['alpha_dash'],
    ];

    /**
     * Ensure that Facebook is null when saving.
     * 
     * @access public
     * @return void
     */
    public function setFacebookAttribute($value)
    {
        $this->attributes['facebook'] = $value ?: null;
    }

    public function address()
    {
        return $this->street.', '.$this->city.', '.$this->state.', '.$this->zip;
    }

    /**
     * Users relationship.
     * 
     * @access public
     * @return void
     */
    public function user()
    {
        return $this->belongsTo('ICT\User');
    }

    /**
     * Tags relationship.
     * 
     * @access public
     * @return void
     */
    public function tags()
    {
        return $this->belongsToMany('ICT\Tag');
    }
}
