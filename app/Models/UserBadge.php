<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class UserBadge extends Model
{
    protected $table = 'user_badges';
    
    
    public $timestamps = true;
    

    use HasFactory;

    protected $fillable = [
        'user_id', 
        'badge_id', 
        'progress', 
        'status', 
        'redeemed', 
        'earned_at', 
        'redeemed_at'
    ];

    protected $casts = [
        'redeemed' => 'boolean',
        'earned_at' => 'datetime',
        'redeemed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function badge()
    {
        return $this->belongsTo(Badge::class);
    }

    public function isRedeemable()
    {
        return $this->status === 'earned' && !$this->redeemed;
    }
    
    public function isLocked()
    {
        return $this->status === 'locked';
    }
    
    public function isRedeemed()
    {
        return $this->status === 'redeemed' && $this->redeemed;
    }

}