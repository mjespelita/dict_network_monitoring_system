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
            $table->string('region')->nullable();
            $table->string('timezone')->nullable();
            $table->string('scenario')->nullable();
            $table->integer('type')->nullable();
            $table->string('supportES')->nullable();
            $table->string('supportL2')->nullable();
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
        Schema::dropIfExists('sites');
    }
};
