<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trade extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_id',
        'seller_id',
        'buyer_id',
        'status',
        'buyer_completed_at',
        'completed_at',
    ];

    protected $dates = [
        'buyer_completed_at',
        'completed_at',
    ];

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function buyer()
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    public function messages()
    {
        return $this->hasMany(TradeMessage::class)->orderBy('created_at', 'asc');
    }

    public function reviews()
    {
        return $this->hasMany(TradeReview::class);
    }

    public function isParticipant($userId): bool
    {
        return $this->seller_id === $userId || $this->buyer_id === $userId;
    }

    public function isBuyer($userId): bool
    {
        return $this->buyer_id === $userId;
    }

    public function isSeller($userId): bool
    {
        return $this->seller_id === $userId;
    }
}