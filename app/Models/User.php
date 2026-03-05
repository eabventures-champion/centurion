<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'contact',
        'password',
        'is_default',
        'gender',
        'birth_day',
        'occupation',
        'marital_status',
        'profile_picture',
        'title',
        'plain_password',
        'is_approved',
        'pending_deletion',
    ];

    /**
     * Get the PCFs associated with this official.
     */
    public function pcfs()
    {
        return $this->hasMany(Pcf::class, 'official_id');
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_approved' => 'boolean',
            'pending_deletion' => 'boolean',
        ];
    }

    /**
     * Get the church associated with this admin/pastor.
     */
    public function church()
    {
        return Church::where('leader_contact', $this->contact)->first();
    }
}
