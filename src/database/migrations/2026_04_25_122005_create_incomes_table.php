<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIncomesTable extends Migration
{
    public function up(): void
    {
        Schema::create('incomes', function (Blueprint $table) {
            $table->id();
            
            // Бизнес-ключи (без unique(), так как income_id повторяется для разных позиций)
            $table->unsignedBigInteger('income_id')->index();
            $table->string('number', 64)->nullable();
            
            // Даты
            $table->date('date');
            $table->date('last_change_date');
            $table->date('date_close')->nullable();
            
            // Товар
            $table->string('supplier_article', 64);
            $table->string('tech_size', 64);
            $table->bigInteger('barcode')->nullable()->index();
            $table->bigInteger('nm_id')->nullable()->index();
            
            // Количество и цена
            $table->unsignedInteger('quantity')->default(0);
            $table->decimal('total_price', 12, 2)->default(0);
            
            // Склад
            $table->string('warehouse_name', 128)->index();
            
            // Сырой ответ
            $table->json('raw_data')->nullable();
            
            $table->timestamps();
            
            // Составной индекс для частых выборок
            $table->index(['income_id', 'barcode']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('incomes');
    }
}