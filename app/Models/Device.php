<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Device extends Model
{
    use HasFactory;

    protected $table = "test_observium_devices";
    protected $primaryKey = "device_name";
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'site',
        'device_name',
        'bandwidth',
        'observium_device_id',
        'observium_port_id',
        'type',
        'description'
    ];

    public static $rules = [
        'site' => 'required|string',
        'device_name' => 'required|string|unique:devices',
        'bandwidth' => 'required|numeric',
        'observium_device_id' => 'required|integer',
        'observium_port_id' => 'required|integer',
        'type' => 'required|integer',
        'description' => 'string' 
    ];
}
