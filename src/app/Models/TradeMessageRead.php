<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TradeMessageRead extends Model
{
    use HasFactory;

    protected $fillable = [
        'trade_message_id',
        'user_id',
        'read_at',
    ];

    protected $dates = [
        'read_at',
    ];

    public function tradeMessage()
    {
        return $this->belongsTo(TradeMessage::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}