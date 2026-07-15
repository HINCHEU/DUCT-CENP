<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = ['order_number', 'site_id', 'created_by', 'approved_by', 'status', 'priority', 'notes', 'requested_delivery_date', 'rejection_reason', 'revision_of', 'submitted_at', 'approved_at'];

    protected function casts(): array
    {
        return [
            'submitted_at' => 'datetime',
            'approved_at' => 'datetime',
        ];
    }

    public function site()
    {
        return $this->belongsTo(Site::class);
    }
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    public function user()
    {
        return $this->creator();
    }
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
    public function originalOrder()
    {
        return $this->belongsTo(Order::class, 'revision_of');
    }
    public function revisions()
    {
        return $this->hasMany(Order::class, 'revision_of');
    }
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
}
