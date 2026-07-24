<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Order extends Model
{
    use LogsActivity;

    protected $fillable = ['order_number', 'site_id', 'created_by', 'approved_by', 'confirmed_by', 'status', 'priority', 'notes', 'requested_delivery_date', 'rejection_reason', 'revision_of', 'submitted_at', 'approved_at', 'confirmed_at'];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['status'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    protected function casts(): array
    {
        return [
            'submitted_at'             => 'datetime',
            'approved_at'              => 'datetime',
            'confirmed_at'             => 'datetime',
            'requested_delivery_date'  => 'date',
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
    public function confirmer()
    {
        return $this->belongsTo(User::class, 'confirmed_by');
    }
    public function originalOrder()
    {
        return $this->belongsTo(Order::class, 'revision_of');
    }
    public function comments()
    {
        return $this->hasMany(Comment::class)->latest();
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
