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
        Schema::create('test_observium_devices', function (Blueprint $table) {
            $table->bigInteger('site_id');
            $table->string('site');
            $table->string('device_name')->primary();
            $table->double('bandwidth');
            $table->bigInteger('observium_device_id')->nullable();
            $table->bigInteger('observium_port_id')->nullable();
            $table->timestamps();
        });
        Schema::table('test_observium_devices', function (Blueprint $table) {
            $table->index(['device_name', 'observium_port_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('test_observium_devices');
    }
};
