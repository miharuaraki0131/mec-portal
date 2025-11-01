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
        Schema::create('divisions', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('部署名');
            $table->foreignId('manager_id')->nullable()->constrained('users')->onDelete('set null')->comment('部署責任者ID');
            $table->foreignId('parent_id')->nullable()->constrained('divisions')->onDelete('set null')->comment('親部署ID');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('divisions');
    }
};

