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
        Schema::create('collaborate', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ownerId')->constrained('users')->onDelete('cascade');
            $table->float('goal')->default(0.00);
            $table->float('currentProgress')->default(0.00);
            $table->enum('status', ['pending', 'accepted', 'rejected'])->default('pending');  
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('collaborate');
    }
};
