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
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('mac_address');
            $table->string('device_name');
            $table->string('device_type');
            $table->string('connected_device_type');
            $table->string('switch_name');
            $table->string('port');
            $table->string('standard_port');
            $table->string('network_theme');
            $table->string('uptime');
            $table->string('traffic_down');
            $table->string('traffic_up');
            $table->string('status');
            $table->string('siteId');
            $table->string('batch_number')->nullable();
            $table->boolean('isTrash')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
