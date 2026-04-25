<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            
            // Бизнес-ключи (индекс объявляется ОДИН РАЗ)
            $table->string('g_number', 64);
            $table->string('odid', 32)->nullable();
            
            // Даты
            $table->dateTime('date');
            $table->date('last_change_date');
            
            // Товар
            $table->string('supplier_article', 64);
            $table->string('tech_size', 64);
            $table->bigInteger('barcode')->nullable();
            $table->bigInteger('nm_id')->nullable();
            
            // Цены
            $table->decimal('total_price', 12, 2);
            $table->decimal('discount_percent', 5, 2);
            
            // Гео/склад
            $table->string('warehouse_name', 128);
            $table->string('oblast', 128);
            
            // Связи
            $table->bigInteger('income_id')->default(0);
            
            // Категории
            $table->string('subject', 64)->nullable();
            $table->string('category', 64)->nullable();
            $table->string('brand', 64)->nullable();
            
            // Статус отмены
            $table->boolean('is_cancel')->default(false);
            $table->dateTime('cancel_dt')->nullable();
            
            // Сырой ответ
            $table->json('raw_data')->nullable();
            
            $table->timestamps();
            
            // Индексы (объявлены отдельно, без дублей)
            $table->index(['date', 'last_change_date']);
            $table->index('warehouse_name');
            $table->index('g_number'); // Теперь он здесь один
        });
    }
    

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
}