<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TradeReview extends Model
{
    use HasFactory;

    protected $fillable = [
        'trade_id',
        'reviewer_id',
        'reviewee_id',
        'rating',
        'comment',
    ];

    public function trade()
    {
        return $this->belongsTo(Trade::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }

    public function reviewee()
    {
        return $this->belongsTo(User::class, 'reviewee_id');
    }
}