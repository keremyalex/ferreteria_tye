<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QrTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'payment_number',
        'transaction_id',
        'order_id',
        'client_name',
        'client_email', 
        'client_phone',
        'client_document_id',
        'document_type',
        'client_code',
        'amount',
        'currency',
        'payment_method',
        'status',
        'access_token',
        'expires_at',
        'generate_response',
        'verify_response',
        'callback_data',
        'paid_at'
    ];    protected $casts = [
        'generate_response' => 'array',
        'verify_response' => 'array',
        'callback_data' => 'array',
        'expires_at' => 'datetime',
        'paid_at' => 'datetime',
        'amount' => 'decimal:2'
    ];

    // Relación con la orden
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    public function scopeExpired($query)
    {
        return $query->where('status', 'expired');
    }

    // Métodos auxiliares
    public function isPaid(): bool
    {
        return $this->status === 'paid';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isExpired(): bool
    {
        return $this->status === 'expired' || 
               ($this->expires_at && $this->expires_at->isPast());
    }

    public function markAsPaid(): void
    {
        $this->update([
            'status' => 'paid',
            'paid_at' => now()
        ]);
    }

    public function markAsExpired(): void
    {
        $this->update(['status' => 'expired']);
    }

    public function markAsFailed(): void
    {
        $this->update(['status' => 'failed']);
    }

    // Generar payment_number único
    public static function generatePaymentNumber(): string
    {
        $prefix = 'ferreteria_tye_';
        $timestamp = now()->format('YmdHis');
        $random = str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT);
        
        return $prefix . $timestamp . '_' . $random;
    }
}
