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
        Schema::create('observium_devices', function (Blueprint $table) {
            $table->string('site');
            $table->string('device_name')->primary();
            $table->double('bandwidth');
            $table->bigInteger('observium_device_id')->nullable();
            $table->bigInteger('observium_port_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('observium_devices');
    }
};
