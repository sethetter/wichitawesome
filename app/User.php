<?php

namespace ICT;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;

use Cache;

class User extends Model implements AuthenticatableContract, CanResetPasswordContract
{
    use Authenticatable, CanResetPassword;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'email', 'password'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['password', 'remember_token'];

    public static $rules = [
        'name' => ['required','min:3'],
        'email' => ['required','email','unique:users,email'],
        'role_id' => ['required','numeric'],
        'password'  => ['required','confirmed','min:6'],
    ];

    public function setPasswordAttribute($value)
    {   
        $this->attributes['password'] = bcrypt($value);
    }

    /**
     * Get check permissions of user.
     * 
     * @param array $permissions
     */
    public function hasPermission($check)
    {   
        //Cache::forget($this->role->slug);
        $permissions = Cache::remember($this->role->slug, 60, function() {
            return $this->role->permissions->lists('name')->toArray();
        });
        return in_array($check, $permissions);
    }

    /**
     * Get the role for user.
     */
    public function role()
    {
        return $this->belongsTo('ICT\Role');
    }
}
