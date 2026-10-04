<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    protected $fillable = [
        'slug',
        'name',
        'type',
        'account_number',
        'account_name',
        'qr_image',
        'is_active',
    ];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function isQris(): bool
    {
        return $this->type === 'qris';
    }
}
