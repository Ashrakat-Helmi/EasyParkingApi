<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bookings extends Model
{
    use HasFactory;
    protected $table = "bookings";
    protected $fillable = [
        'userId' , 
        'placeId',
        'timeFrom' ,
        'timeTo' ,
        'date',
        'totalPrice' ,
        'status',
    ];
    protected $casts = [
        'timeFrom' => 'datetime:H:i:s',
        'timeTo' => 'datetime:H:i:s',
        'date'=> 'datetime:Y-m-d',
        'totalPrice' => 'float',
    ];

}
