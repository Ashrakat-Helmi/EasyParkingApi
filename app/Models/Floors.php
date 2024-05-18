<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Floors extends Model
{
    use HasFactory;
    protected $table = "floors";
    protected $fillable = [
        'code' ,
        'garageId' ,
    ];
    protected $casts = [
        'garageId' => 'integer',
    ];

}
