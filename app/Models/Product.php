<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'description',
        'price',
        'discount',
        'store_id',
        'sub_category_id',
    ];
    public function colors()
    {
        return $this->belongsToMany(Color::class, 'product_colors', 'product_id', 'color_id')
                    ->withTimestamps();
    }
    public function sizes()
    {
        return $this->belongsToMany(Size::class, 'product_sizes', 'product_id', 'size_id')
                    ->withTimestamps();
    }
    public function images()
    {
        return $this->belongsToMany(Image::class, 'product_images', 'product_id', 'image_id')
                    ->withTimestamps();
    }
    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }

    public function subCategory()
    {
        return $this->belongsTo(SubCategory::class);
    }
    public function store()
    {
        return $this->belongsTo(Store::class);
    }
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    public function scopeFilter($query, array $filters)
    {

        $query->when($filters['search'] ?? false, function ($query, $search) {

            $query->where(
                fn ($query) => $query
                    ->where('name', 'like', '%' . $search . '%')
                    ->orWhere('description', 'like', '%' . $search . '%')
            );
        });
    }
}
