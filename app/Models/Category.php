<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Category extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug'];
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    /** Nama ikon (lihat components/icon) berdasarkan slug kategori. */
    public function iconName(): string
    {
        return match ($this->slug) {
            'elektronik' => 'bolt',
            'fashion' => 'bag',
            'kopi' => 'cup',
            'rumah-tangga' => 'home',
            'skincare' => 'sparkles',
            'sneakers' => 'shoe',
            default => 'tag',
        };
    }
}
