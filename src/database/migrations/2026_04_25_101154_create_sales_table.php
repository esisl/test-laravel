<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSalesTable extends Migration
{
    public function up(): void
    {
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            
            // Уникальные идентификаторы из API
            $table->string('sale_id', 32)->unique(); // "S17613661853"
            $table->string('g_number', 64)->index();
            
            // Даты
            $table->date('date');
            $table->date('last_change_date');
            
            // Товар/артикул
            $table->string('supplier_article', 64);
            $table->string('tech_size', 64);
            $table->bigInteger('barcode')->nullable();
            $table->bigInteger('nm_id')->nullable();
            
            // Цены и скидки (храним как decimal для точности)
            $table->decimal('total_price', 12, 2);
            $table->decimal('discount_percent', 5, 2);
            $table->decimal('for_pay', 12, 2);
            $table->decimal('finished_price', 12, 2);
            $table->decimal('price_with_disc', 12, 2);
            $table->string('spp', 10)->nullable();
            
            // Флаги
            $table->boolean('is_supply');
            $table->boolean('is_realization');
            $table->boolean('is_storno')->nullable();
            
            // Гео/склад
            $table->string('warehouse_name', 128);
            $table->string('country_name', 64);
            $table->string('oblast_okrug_name', 128);
            $table->string('region_name', 128);
            
            // Связи
            $table->bigInteger('income_id')->default(0);
            $table->string('promo_code_discount', 64)->nullable();
            
            // Категории (хэши)
            $table->string('subject', 64)->nullable();
            $table->string('category', 64)->nullable();
            $table->string('brand', 64)->nullable();
            
            // Сырой ответ для отладки/дополнительных полей
            $table->json('raw_data')->nullable();
            
            $table->timestamps();
            
            // Индексы для частых выборок
            $table->index(['date', 'last_change_date']);
            $table->index('warehouse_name');
            $table->index('category');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
}