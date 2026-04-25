<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $table = 'orders';
    
    protected $fillable = [
        'g_number', 'odid', 'date', 'last_change_date',
        'supplier_article', 'tech_size', 'barcode', 'nm_id',
        'total_price', 'discount_percent',
        'warehouse_name', 'oblast', 'income_id',
        'subject', 'category', 'brand',
        'is_cancel', 'cancel_dt',
        'raw_data',
    ];
    
    protected $casts = [
        'date' => 'datetime:Y-m-d H:i:s',
        'last_change_date' => 'date:Y-m-d',
        'cancel_dt' => 'datetime:Y-m-d H:i:s',
        'is_cancel' => 'boolean',
        'raw_data' => 'array',
        'total_price' => 'decimal:2',
        'discount_percent' => 'decimal:2',
    ];
}