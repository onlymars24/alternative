<?php

namespace App\Models;


use App\Models\Order;
use App\Models\Passenger;
use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'role_id',
        'bonuses',
        'confirmed'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'remember_token',
        'role_id',
        'password'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function bonuses()
    {
        return $this->hasMany(Bonus::class);
    }

    public function passengers()
    {
        return $this->hasMany(Passenger::class);
    }
    
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function otps()
    {
        return $this->hasMany(OtpMember::class);
    }
}
