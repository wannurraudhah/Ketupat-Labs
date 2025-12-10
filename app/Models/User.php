<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'level',
        'xp',
        'points' // make sure points exist
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    // Relationship to badges via badge_code
    public function badges()
    {
        return $this->belongsToMany(
            Badge::class,
            'user_badges',
            'user_id',
            'badge_code', // pivot column matches Badge code
            'id',
            'code'
        )
        ->withPivot('status', 'obtained_at', 'given_by', 'evidence_url', 'notes')
        ->withTimestamps();
    }

    public function approvedBadges()
    {
        return $this->badges()->wherePivot('status', 'approved');
    }

    public function pendingBadges()
    {
        return $this->badges()->wherePivot('status', 'pending');
    }

    public function getBadgeStats()
    {
        return [
            'total' => $this->badges()->count(),
            'approved' => $this->approvedBadges()->count(),
            'pending' => $this->pendingBadges()->count(),
            'xp_total' => $this->xp
        ];
    }
}