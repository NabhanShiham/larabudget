<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
            Schema::create('receipts', function (Blueprint $table) {
            $table->id();
            $table->string('image_path');
            $table->float('amount')->default(0.00);
            $table->date('transaction_date');

            $table->foreignId('category_id')
                ->constrained()
                ->cascadeOnDelete();
            
            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
