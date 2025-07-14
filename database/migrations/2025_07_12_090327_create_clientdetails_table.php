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
        Schema::create('clientdetails', function (Blueprint $table) {
            $table->id();
            $table->string('mac')->nullable();
            $table->string('name')->nullable();
            $table->string('deviceType')->nullable();
            $table->string('switchName')->nullable();
            $table->string('switchMac')->nullable();
            $table->string('port')->nullable();
            $table->string('standardPort')->nullable();
            $table->string('trafficDown')->nullable();
            $table->string('trafficUp')->nullable();
            $table->string('uptime')->nullable();
            $table->string('guest')->nullable();
            $table->string('blocked')->nullable();
            $table->string('siteId')->nullable();
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
        Schema::dropIfExists('clientdetails');
    }
};
