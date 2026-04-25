<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    protected $table = 'sales';
    
    protected $fillable = [
        'sale_id', 'g_number', 'date', 'last_change_date',
        'supplier_article', 'tech_size', 'barcode', 'nm_id',
        'total_price', 'discount_percent', 'for_pay', 'finished_price', 'price_with_disc', 'spp',
        'is_supply', 'is_realization', 'is_storno',
        'warehouse_name', 'country_name', 'oblast_okrug_name', 'region_name',
        'income_id', 'promo_code_discount',
        'subject', 'category', 'brand',
        'raw_data',
    ];
    
    protected $casts = [
        'date' => 'date:Y-m-d',
        'last_change_date' => 'date:Y-m-d',
        'is_supply' => 'boolean',
        'is_realization' => 'boolean',
        'is_storno' => 'boolean',
        'raw_data' => 'array',
        'total_price' => 'decimal:2',
        'discount_percent' => 'decimal:2',
        'for_pay' => 'decimal:2',
        'finished_price' => 'decimal:2',
        'price_with_disc' => 'decimal:2',
    ];
    
    // Ключ для upsert()
    public static function getUniqueKeys(): array
    {
        return ['sale_id'];
    }
}