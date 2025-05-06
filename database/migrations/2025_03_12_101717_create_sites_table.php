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
        Schema::create('sites', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('siteId')->nullable();
            $table->string('customerId')->nullable();
            $table->string('customerName')->nullable();
            $table->string('region')->nullable();
            $table->string('timezone')->nullable();
            $table->string('scenario')->nullable();
            $table->string('wan')->nullable();
            $table->integer('connectedApNum')->nullable();
            $table->integer('disconnectedApNum')->nullable();
            $table->integer('isolatedApNum')->nullable();
            $table->integer('connectedSwitchNum')->nullable();
            $table->integer('disconnectedSwitchNum')->nullable();
            $table->integer('type')->nullable();
            $table->boolean('isTrash')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sites');
    }
};
