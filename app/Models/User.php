<?php

namespace App\Models;

use Carbon\Carbon;
use EloquentFilter\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;


/**
 * Class User
 *
 * @property string $name
 * @property string $email
 * @property string $password
 * @property Role[] $roles
 *
 * @method static User create(array $user)
 * @package App
 */
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes, HasRoles, Filterable;

    /**
     * Set permissions guard to API by default
     * @var string
     */
    protected $guard_name = 'api';

    /**
     * Sex map
     * */
    const sexMap = [
        0 => 'Male',
        1 => 'Female'
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at', 'birthday'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name', 'email', 'password', 'status', 'sex', 'birthday', 'description'
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    public $appends = ['age', 'sex_format'];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'updated_at',
        'deleted_at'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Get the avatar and return the default avatar if the avatar is null.
     *
     * @param string $value
     * @return string
     */
    public function getAvatarAttribute($value)
    {
        return !empty($value) ? $value : config('content.default_avatar');
    }

    public function getSexFormatAttribute()
    {
        return self::sexMap[$this->sex];
    }

    public function getAgeAttribute()
    {
        if (empty($this->birthday)) {
            return 0;
        }
        return Carbon::now()->diffInYears($this->birthday);
    }

    /**
     * Check if user has a permission
     * @param String
     * @return bool
     */
    public function hasPermission($permission): bool
    {
        foreach ($this->roles as $role) {
            if (in_array($permission, $role->permissions->pluck('name')->toArray())) {
                return true;
            }
        }
        return false;
    }

    /**
     * @return bool
     */
    public function isAdmin(): bool
    {
        foreach ($this->roles as $role) {
            if ($role->isAdmin()) {
                return true;
            }
        }

        return false;
    }
}
