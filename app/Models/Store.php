<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'img',
        'category_id',
    ];
    public function categories()
    {
        return $this->belongsToMany(Category::class , 'category_store', 'store_id', 'category_id')
        ->withTimestamps();
    }
    public function products()
    {
        return $this->hasMany(Product::class);
    }
    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }
    public function rates()
    {
        return $this->hasMany(Rate::class);
    }
    public function branches()
    {
        return $this->hasMany(Branch::class);
    }

}
