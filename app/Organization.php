<?php

namespace ICT;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use ICT\Traits\Eventable;
use ICT\Traits\Sluggable;
use ICT\Traits\Visibility;

class Organization extends Model
{
    use SoftDeletes, Eventable, Sluggable, Visibility;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'organizations';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'image', 'facebook', 'twitter', 'website', 'email', 'phone', 'visible'];
    
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
        'facebook' => ['numeric','unique:organizations,facebook'],
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
}
