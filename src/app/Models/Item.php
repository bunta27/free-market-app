<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use App\Models\Category;
use App\Models\CategoryItem;
use App\Models\Like;
use App\Models\Comment;
use App\Models\SoldItem;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'price',
        'description',
        'img_url',
        'user_id',
        'condition_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function condition()
    {
        return $this->belongsTo(Condition::class);
    }

    public function categoryItems()
    {
        return $this->hasMany(CategoryItem::class);
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'category_items');
    }

    public function likes()
    {
        return $this->hasMany(Like::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function soldItem()
    {
        return $this->hasOne(SoldItem::class);
    }

    public function liked(): bool
    {
        if (!Auth::check()) return false;
        return Like::where([
            'item_id' => $this->id,
            'user_id' => Auth::id(),
        ])->exists();
    }

    public function likeCount(): int
    {
        return $this->likes()->count();
    }

    public function getComments()
    {
        return $this->comments()->latest()->get();
    }

    public function sold(): bool
    {
        return $this->soldItem()->exists();
    }

    public function mine(): bool
    {
        return Auth::check() && $this->user_id === Auth::id();
    }
}
