<?php

namespace ICT;

use Illuminate\Database\Eloquent\Model;

use ICT\Traits\Eventable;
use ICT\Traits\Sluggable;

class Tag extends Model
{
    use Eventable, Sluggable;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'tags';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name'];

    public static $rules = [
        'name' => ['required']
    ];

    public function artists()
    {
        return $this->belongsToMany('ICT\Artist');
    }

    public function bands()
    {
        return $this->belongsToMany('ICT\Band');
    }

    public function events()
    {
        return $this->belongsToMany('ICT\Event');
    }

    public function organizations()
    {
        return $this->belongsToMany('ICT\Organization');
    }
}
