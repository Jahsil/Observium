<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Site extends Model
{
    use HasFactory;

    protected $table = "device_sites";
    protected $primaryKey = "id";

    protected $fillable = [
        'site',
        'isActive'
    ];

    public static $rules = [
        'site' => 'required|string',
        'isActive' => 'required|boolean',
    ];
}
