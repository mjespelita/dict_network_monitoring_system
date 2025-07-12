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
        Schema::create('disconnecteddevices', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('siteId');
            $table->string('device_name');
            $table->string('device_mac');
            $table->string('device_type');
            $table->string('status');
            $table->boolean('isTrash')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('disconnecteddevices');
    }
};
