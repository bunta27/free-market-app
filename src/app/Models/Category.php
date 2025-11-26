<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;
    protected $fillable = [
        'category',
    ];

    public function items()
    {
        return $this->belongsToMany(Item::class, 'category_items', 'category_id', 'item_id');
    }


    public function categoryItems()
    {
        return $this->hasMany(CategoryItem::class);
    }
}
