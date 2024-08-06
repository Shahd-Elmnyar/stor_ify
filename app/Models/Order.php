<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'status',
        'total',
        'payment_id',
        'delivery_date',
        'delivery_time',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<int, string>
     */

    protected $casts = [
        'delivery_date' => 'date:Y-m-d',
        'delivery_time' => 'datetime:H:i:s', // Correct cast for time
        'total' => 'float',
    ];

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }
}
