<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TradeMessage extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'trade_id',
        'user_id',
        'message',
        'image_path',
        'edited_at',
    ];

    protected $dates = [
        'edited_at',
        'deleted_at',
    ];

    public function trade()
    {
        return $this->belongsTo(Trade::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}