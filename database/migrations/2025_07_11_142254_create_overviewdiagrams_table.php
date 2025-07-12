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
        Schema::create('overviewdiagrams', function (Blueprint $table) {
            $table->id();
            $table->string('totalGatewayNum');
            $table->string('connectedGatewayNum');
            $table->string('disconnectedGatewayNum');
            $table->string('totalSwitchNum');
            $table->string('connectedSwitchNum');
            $table->string('disconnectedSwitchNum');
            $table->string('totalPorts');
            $table->string('availablePorts');
            $table->string('powerConsumption');
            $table->string('totalApNum');
            $table->string('connectedApNum');
            $table->string('isolatedApNum');
            $table->string('disconnectedApNum');
            $table->string('totalClientNum');
            $table->string('wiredClientNum');
            $table->string('wirelessClientNum');
            $table->string('guestNum');
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
        Schema::dropIfExists('overviewdiagrams');
    }
};
