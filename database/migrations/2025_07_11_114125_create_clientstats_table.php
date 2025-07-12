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
        Schema::create('clientstats', function (Blueprint $table) {
            $table->id();
            $table->integer('total');
            $table->integer('wireless');
            $table->integer('wired');
            $table->integer('num2g');
            $table->integer('num5g');
            $table->integer('num6g');
            $table->integer('numUser');
            $table->integer('numGuest');
            $table->integer('numWirelessUser');
            $table->integer('numWirelessGuest');
            $table->integer('num2gUser');
            $table->integer('num5gUser');
            $table->integer('num6gUser');
            $table->integer('num2gGuest');
            $table->integer('num5gGuest');
            $table->integer('num6gGuest');
            $table->integer('poor');
            $table->integer('fair');
            $table->integer('noData');
            $table->integer('good');
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
        Schema::dropIfExists('clientstats');
    }
};
