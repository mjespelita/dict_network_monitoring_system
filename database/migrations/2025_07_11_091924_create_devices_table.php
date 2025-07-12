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
        Schema::create('devices', function (Blueprint $table) {
            $table->id();
            $table->string('device_name');
            $table->string('ip_address');
            $table->string('status');
            $table->string('model');
            $table->string('version');
            $table->string('uptime');
            $table->string('cpu');
            $table->string('memory');
            $table->string('public_ip');
            $table->string('link_speed');
            $table->string('duplex');
            $table->string('siteId');
            $table->string('batch_number')->nullable();
            $table->string('isTrash')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('devices');
    }
};
