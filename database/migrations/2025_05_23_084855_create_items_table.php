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
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->date('purchase_date');
            $table->text('description')->nullable();
            $table->foreignId('manager_id')->constrained('users');
            $table->foreignId('location_id')->constrained('locations');
            $table->foreignId('owner_id')->constrained('users');
            $table->enum('status', ['registered', 'normal', 'gone'])->default('registered');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
