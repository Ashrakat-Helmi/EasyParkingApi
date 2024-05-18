<?php

namespace App\Http\Controllers;

use App\Models\Garages;
use App\Models\Floors;
use App\Models\Places;
use Illuminate\Http\Request;


class PlacesController extends Controller
{
   
    public function index()
    {
        return Places::all();
    }


    public function show(Places $places)
    {
        //
    }

    public function update(Request $request, Places $places)
    {
        //
    }

    public function destroy(Places $places)
    {
        //
    }

    // Extracted method for the common query
    private function getPlacesByGarageId($garageId)
    {
        return Places::join('floors', 'floors.id', '=', 'places.floorId')
            ->join('garages', 'garages.id', '=', 'floors.garageId')
            ->select([
                'places.id as pId',
                'places.num',
                'floors.id as fId',
                'floors.code',
                'garages.num_spaces',
                'garages.id as gId',
                'garages.name as garageName',
                'garages.num_floors',
                'garages.location',
                'garages.rate',
                'garages.garage_img',
                'garages.desc',
                'garages.price',
            ])
            ->where('garages.id', $garageId);
    }

    public function showByGarage(Request $request)
    {
        $garageId = $request->query('garageId');
        $places = $this->getPlacesByGarageId($garageId)->get();

        return Response()->json(['status' => 'success', 'data' => ['places' => $places]], 200);
    }

   
    public function getNotBookedPlaces(Request $request)
    {
        $garageId = $request->query('garageId');
        $allPlaces = $this->getPlacesByGarageId($garageId)->get();

        if (!$request->query('date') || !$request->query('timeFrom') || !$request->query('timeTo')) {
            $date = date('Y-m-d');
            $timeFrom = date('H:i:s', time());
            $timeTo = date('H:i:s', strtotime($timeFrom . ' + 2 hours'));            
        } else {
            $date = $request->query('date');
            $timeFrom = $request->query('timeFrom');
            $timeTo = $request->query('timeTo');
        }

        $notBookedPlaces = $this->getPlacesByGarageId($garageId)
            ->whereNotIn('places.id', function ($query) use ($date, $timeFrom, $timeTo) {
                $query->select('places.id as place_id')
                    ->from('places')
                    ->leftJoin('bookings', 'places.id', '=', 'bookings.placeId')
                    ->where('bookings.date', '=', $date)
                    ->when($timeTo, function ($query) use ($timeTo) {
                        $query->whereTime('bookings.timeFrom', '<', $timeTo);
                    })
                    ->when($timeFrom, function ($query) use ($timeFrom) {
                        $query->whereTime('bookings.timeTo', '>', $timeFrom);
                    })
                    ->where('bookings.status', '=', 'ongoing');
            })
            ->get()
            ->groupBy('fId');

        $bookedPlaces= $this->getPlacesByGarageId($garageId)   
            ->whereIn('places.id', function ($query) use ($date, $timeFrom, $timeTo) {
                $query->select('places.id as place_id') 
                ->from('places')
                ->leftJoin('bookings', 'places.id', '=', 'bookings.placeId')
                ->where('bookings.date', '=', $date)
                ->when($timeTo, function ($query) use ($timeTo) {
                    $query->whereTime('bookings.timeFrom', '<', $timeTo);
                })
                ->when($timeFrom, function ($query) use ($timeFrom) {
                    $query->whereTime('bookings.timeTo', '>', $timeFrom);
                })
                ->where('bookings.status', '=', 'ongoing');
            })
            ->get()
            ->groupBy('fId');

        foreach ($bookedPlaces as &$object) {
            $object->placeStatus = 'occupied';
        }            
        foreach ($notBookedPlaces as &$object) {
            $object->placeStatus = 'available';
        }          
        
        
        $mergedPlaces = $notBookedPlaces->concat($bookedPlaces);
        
        
        return Response()->json(['status' => 'success', 'data' => [
            'places' => $mergedPlaces ,
        ]], 200);
    }

}
