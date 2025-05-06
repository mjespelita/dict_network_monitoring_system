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
        Schema::create('auditlogs', function (Blueprint $table) {
            $table->id();
            $table->string('time')->nullable();
            $table->string('operator')->nullable();
            $table->string('resource')->nullable();
            $table->string('ip')->nullable();
            $table->string('auditType')->nullable();
            $table->string('level')->nullable();
            $table->string('result')->nullable();
            $table->string('content')->nullable();
            $table->string('label')->nullable();
            $table->string('oldValue')->nullable();
            $table->string('newValue')->nullable();
            $table->boolean('isTrash')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('auditlogs');
    }
};
