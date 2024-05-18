<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Garages extends Model
{
    use HasFactory;
    protected $table = "garages";
    protected $fillable = [
        'name', 
        'ownerId',
        'num_floors',
        'location',
        'lat',
        'longt',
        'rate',
        'garage_img',
        'desc',
        'price',
        'num_spaces',
        'support',
        'security_camera',
        'online_payment',
        'emergency_exit',
    ];
    protected $casts = [
        'ownerId' => 'integer',
        'num_floors' => 'integer',
        'lat' => 'float',
        'longt' => 'float',
        'rate' => 'float',
        'price'=> 'float',
        'num_spaces' =>  'integer',
        'support' => 'boolean',
        'security_camera' => 'boolean',
        'online_payment' => 'boolean',
        'emergency_exit' => 'boolean',
    ];


}
