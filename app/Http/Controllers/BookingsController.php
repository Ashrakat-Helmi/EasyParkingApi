<?php

namespace App\Http\Controllers;

use App\Models\Bookings;
use App\Models\Garages;
use App\Models\Floors;
use App\Models\Places;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;


class BookingsController extends Controller
{
    public function index()
    {
        return Bookings::all();
    }


    public function store(Request $request)
    {
        $request->validate([
            'userId' => "required|exists:users,id", 
            'placeId' => "required|exists:places,id",
            'timeFrom' =>'required',
            'timeTo' => 'required',
            'date' => 'required|date',
        ]);

        $duration = intval(strtotime($request->timeTo) / 60 ) - intval( strtotime($request->timeFrom) / 60);
        $floorId = Places::find($request->placeId)->floorId;
        $floor  = Floors::find($floorId);
        $garageId = $floor->garageId;
        $garage = Garages::find($garageId);
        $price = $garage->price;
        $totalPrice =  ($duration/60) * $price;

        $user = User::find($request->userId);

        $booking= Bookings::create([
            'userId' => $request->userId, 
            'placeId' => $request->placeId,
            'timeFrom' =>$request->timeFrom,
            'timeTo' => $request->timeTo,
            'date' => $request->date,
            'totalPrice' => $totalPrice ,
            'status' => 'ongoing'
        ]);

        $response = [
            'booking'=>$booking,
            'floor' => $floor,
            'garage' => $garage,
            'user' => $user
        ];

        return Response()->json(['status'=>'success','data'=>$response], 200 );
    }

    public function show($id)
    {
        return Bookings::find($id);
    }

    public function updateToCompleted(Request $request)
    {
        $id = $request->query('id');
        $booking = Bookings::find($id);
        $booking->update([
            'status' => 'completed',
        ]);
        $response = [
            'booking'=>$booking,
        ];
        return Response()->json(['status'=>'success','data'=>$response], 200 );
    }
    public function updateToOccupied(Request $request)
    {
        $id = $request->query('id');
        $booking = Bookings::find($id);
        $booking->update([
            'status' => 'occupied',
        ]);
        $response = [
            'booking'=>$booking,
        ];
        return Response()->json(['status'=>'success','data'=>$response], 200 );
    }

    public function destroy(Request $request)
    {
        $id = $request->query('id');
        $booking = Bookings::find($id);
        $booking->update([
            'status' => 'canceled',
        ]);
        // Bookings::update("update bookings set status = 'canceled' where id = ?", [$id]);
        $response = [
            'booking'=>$booking,
        ];
        return Response()->json(['status'=>'success','data'=>$response], 200 );
    }
    public function showByStatus(Request $request)
    {
        if ($request->query('userId')){
            $userId = $request->query('userId');
            $status = $request->query('status');
            $bookings = Bookings::join('users', 'users.id' , '=','bookings.userId')
            ->join('places', 'places.id' , '=', 'bookings.placeId')
            ->join('floors', 'floors.id', '=', 'places.floorId')
            ->join('garages', 'garages.id', '=', 'floors.garageId')
            ->select('bookings.id as bookId',
                    'bookings.timeFrom' , 
                    'bookings.timeTo', 
                    'bookings.date',
                    'bookings.totalPrice as price',
                    'places.num' ,
                    'floors.code' ,
                    'garages.name' , 
                    'garages.location')
            ->where('bookings.userId' ,'=', $userId)
            ->where('bookings.status' ,'=' , $status)
            ->get();
        }
        elseif ($request->query('garageId')) {
            $garageId = $request->query('garageId');
            $status = $request->query('status');
            $bookings = Bookings::join('users', 'users.id' , '=','bookings.userId')
            ->join('places', 'places.id' , '=', 'bookings.placeId')
            ->join('floors', 'floors.id', '=', 'places.floorId')
            ->join('garages', 'garages.id', '=', 'floors.garageId')
            ->select('bookings.id as bookId',
                    'bookings.timeFrom' , 
                    'bookings.timeTo', 
                    'bookings.date',
                    'bookings.totalPrice as price',
                    'places.num' ,
                    'floors.code' ,
                    'garages.name' , 
                    'garages.location')
            ->where('garages.id' ,'=', $garageId)
            ->where('bookings.status' ,'=' , $status)
            ->get();
        }


        $response = [
            'bookings'=>$bookings,
        ];
        return Response()->json(['status'=>'success','data'=>$response], 200 );
    }
}
