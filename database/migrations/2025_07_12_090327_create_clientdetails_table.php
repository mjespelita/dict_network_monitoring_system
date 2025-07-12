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
            $table->string('mac');
            $table->string('name');
            $table->string('deviceType');
            $table->string('switchName');
            $table->string('switchMac');
            $table->string('port');
            $table->string('standardPort');
            $table->string('trafficDown');
            $table->string('trafficUp');
            $table->string('uptime');
            $table->string('guest');
            $table->string('blocked');
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
        Schema::dropIfExists('clientdetails');
    }
};
