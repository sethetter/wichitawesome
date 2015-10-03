<?php

namespace ICT;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use Carbon\Carbon;
use ICT\Traits\Eventable;
use ICT\Traits\Visibility;

class Event extends Model
{
    use SoftDeletes, Eventable, Visibility;

    /**
     * The attributes that are converted to Carbon instances.
     *
     * @var string
     */
    protected $dates = ['start_time', 'end_time'];

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'events';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'start_time', 'end_time', 'venue_id', 'image', 'facebook', 'hashtag', 'description', 'user_id', 'visible', 'updated_at'];

    public static $rules = [
        'name' => ['required','min:3'],
        'facebook' => ['numeric','unique:events,facebook'],
        'venue_id' => ['numeric'],
        'venue.name' => ['required_without:venue_id'],
        'venue.street' => ['min:3','required_without:venue_id'],
        'venue.city' => ['min:3','required_without:venue_id'],
        'venue.state' => ['alpha','size:2','required_without:venue_id'],
        'venue.zip' => ['min:5','required_without:venue_id'],
        'venue.latitude' => ['numeric','required_without:venue_id'],
        'venue.longitude' => ['numeric','required_without:venue_id'],
        'start_time' => ['required','date'],
        'end_time' => ['date','after:start_time'],
        's_date' => ['required'],
        'e_date' => ['required_with:e_time'],
        's_time' => ['required'],
        'e_time' => ['required_with:e_date'],
        'description' => ['min:3', 'max:1000'],
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

    public static function boot()
    {
        parent::boot();

        static::saving(function($event) {
            if (!is_null($event->start_time) && is_null($event->end_time)) {
                $event->end_time = $event->start_time->copy()->setTime(23, 59, 59);
            }
            return true;

        });
    }

    public function displayDesc() {
        // Escape HTML tags
        $value = htmlspecialchars($this->description);
        // The Regular Expression filter
        $rex = '/(http|https)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?/';
        // Check if there is a url in the text
        if(preg_match($rex, $value, $url)) {
            $url = htmlspecialchars($url[0]);
            $value = preg_replace($rex, '<a target="_blank" href="'.$url.'">'.$url.'</a> ', $value);
        }
        return nl2br($value);
    }

    public function displayTime()
    {
        // ends same day
        if($this->start_time->format('jn') === $this->end_time->format('jn'))
        {
            // not happening now
            if($this->start_time > new \DateTime) {
                // has specific end
                if( $this->end_time->format('gi') != '1159' ) {
                    return $this->start_time->format('g:i A').' - '.$this->end_time->format('g:i A');
                } else {
                    return $this->start_time->format('g:i A');
                }
            } else {
                // has specific end
                if( $this->end_time->format('gi') != '1159' ) {
                    return 'Until '.$this->end_time->format('g:i A');
                } else {
                    return 'Happening Now';
                }
            }
        }
        else
        {
            return  $this->start_time->format('g:i A').' - '.$this->end_time->format('g:i A \o\n M d');
        }
    }

    /**
     * Artists relationship.
     * 
     * @access public
     * @return void
     */
    public function artists()
    {
        return $this->belongsToMany('ICT\Artist');
    }

    /**
     * Bands relationship.
     * 
     * @access public
     * @return void
     */
    public function bands()
    {
        return $this->belongsToMany('ICT\Band');
    }

    /**
     * Users relationship.
     * 
     * @access public
     * @return void
     */
    public function venue()
    {
        return $this->belongsTo('ICT\Venue');
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
