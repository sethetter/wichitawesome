<?php

namespace ICT;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'roles';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name'];

    /**
     * Disable timestamps.
     *
     * @var string
     */
    public $timestamps = false;

    public static $rules = [
        'name' => ['required']
    ];

    /**
     * Get the all users with role.
     */
    public function users()
    {
        return $this->hasMany('ICT\User');
    }

    public function permissions()
    {
        return $this->belongsToMany('ICT\Permission');
    }
}
