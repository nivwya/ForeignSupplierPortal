<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasRoles;
    use HasApiTokens, HasFactory, Notifiable;
    protected $guard_name = 'web'; // KEEP THIS AS 'web'

//changes made by niveditha

    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    /**
     * The attributes that are mass assignable (for security).
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'company_code',
        'emp_id',
        'po',
        'mobile',
        'status',
        // Add other fields if you have them (e.g., 'role', 'status')
    ];

    /**
     * The attributes that should be hidden for arrays/JSON.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    // Example: If you want to define relationships, add them here.
    // public function vendors()
    // {
    //     return $this->hasMany(Vendor::class);
    // }
//changes end

    // In User model
        public function vendor()
        {
            return $this->belongsTo(Vendor::class);
        }
        public function isSuperAdmin()
        {
            return $this->is_superadmin === 1;
        }

}

