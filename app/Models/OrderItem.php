<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $fillable = ['order_id', 'duct_type_id', 'dimensions', 'quantity', 'quantity_delivered', 'surface_area', 'fabrication_status'];

    protected function casts(): array
    {
        return [
            'dimensions' => 'json',
            'quantity' => 'integer',
            'quantity_delivered' => 'integer',
            'surface_area' => 'decimal:3',
        ];
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
    public function ductType()
    {
        return $this->belongsTo(DuctType::class);
    }
}
