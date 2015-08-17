<?php

namespace ICT;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'permissions';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name'];

    public static $rules = [
        'name' => ['required']
    ];

    public function roles()
    {
        return $this->belongsToMany('ICT\Role');
    }
}
