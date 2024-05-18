<?php

namespace App\Http\Controllers;

use App\Models\Garages;
use App\Models\Floors;
use App\Models\Places;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\File;

class GaragesController extends Controller
{
    public function index()
    {
        return Garages::all();
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => "required|string",
            'ownerId' => "required|exists:users,id",
            'num_floors' => 'required',
            'location' => "required|string",
            'lat' => "required",
            'longt' => "required",
            'desc' => "required",
            'price' => "required",
            'garage_img'=> 'required|image|mimes:jpeg,png,jpg,gif,svg',
            'num_spaces' =>'required',
        ]);
        
        $image = $request->file('garage_img');
        $imageName = time() . '.' . $image->extension();
        $image->move(public_path('images'), $imageName);

        $garage= Garages::create([
            'name' => $request->name,
            'ownerId' => $request->ownerId,
            'num_floors' => $request->num_floors,
            'location' => $request->location,
            'lat' => $request->lat,
            'longt' => $request->longt,
            'rate' => 0,
            'garage_img' => $imageName,
            'desc' => $request->desc,
            'price' => $request->price,
            'num_spaces'=>$request->num_spaces,
            'support' => $request->has("support") ? true : false, //
            'security_camera' => $request->has("security_camera") ? true : false, //
            'online_payment' => $request->has("online_payment") ? true : false, //
            'emergency_exit' => $request->has("emergency_exit") ? true : false, //
        ]);

        $garageId =$garage->id;

        $response = [
            'garage'=>$garage,
            'floors' => $this->addFloors($garageId,$request->num_floors,$request->num_spaces)
        ];

        return Response()->json(['status'=>'success','data'=>$response], 200 );
    }

    public function addFloors($id,$num_floors,$num_spaces)
    {
        $placesNo =$num_spaces/$num_floors;
        if ($num_floors > 0) {
            for ($i = 1; $i <= $num_floors; $i++) {
                $floor = new Floors;
                $floor->code = chr($i+64);
                $floor->garageId = $id;
                $floor->save();

                for ($j=1 ; $j<=$placesNo ; $j++){  
                    $place = new Places;
                    $place->num = $j;
                    $place->floorId = $floor->id;
                    $place->save();
                }
            }
            return ['status' => 'Success ,Floors and Places have been added successfully.'];
        } else {
            return ['status' => 'Error ,Invalid number of floors or Places.'];
        }
      
    }

    public function show(Request $request)
    {
        $id = $request->query('id');
        $garage =Garages::find($id);
        $imageName = $garage->garage_img;
        $imageUrl = asset("images/$imageName"); 
        $garage->garage_img = $imageUrl;
        $response = [
            'garage'=>$garage,
        ];

        return Response()->json(['status'=>'success','data'=>$response], 200 ); 
    }

    public function update(Request $request)
    {
        $id = $request->query('id');
        $garage = Garages::find($id);
        $garage->update($request->all());

        $response = [
            'garage'=>$garage,
            //'error' => $garage->getChanges()
        ];
        return Response()->json(['status'=>'success','data'=>$response], 200 );
    }

    public function destroy($id)
    {
        if(Garages::destroy($id)){
            return  ["status"=>"Deleted Successfully"];
        }else{
            return   ["status"=>"Failed to Delete"];
        }
    }

    public function getImage(Request $request)
    {
        $id = $request->query('id');
        $garage = Garages::find($id);
        $imageName = $garage->garage_img;
        $imageUrl = asset("images/$imageName"); 
        
        $response = [
        'img'=> $imageUrl,
        ];
        return Response()->json(['status'=>'success','data'=>$response], 200 );
    }

    public function search(Request $request)
    {
        $searchInput = $request->query('searchInput');
        if(! $searchInput){
            $response =[
                'error' =>'Not found',
            ];
        }
        $garages=Garages::where('name','LIKE','%'.$searchInput."%")
                        ->orwhere('location','LIKE','%'.$searchInput."%")
                        ->get();

        $response = [
        'garages'=>$garages,
        ];
        return Response()->json(['status'=>'success','data'=>$response], 200 );
    }

    public function rate(Request $request)
    {
        $garageId = $request->query('garageId');
        $number = $request->query('rate');
        $garage = Garages::find($garageId);
        $garage_rate = $garage->rate;
        $new_rate = ($garage_rate + $number -1) % 5 + 1;
        $garage->update([
            "rate"=>$new_rate
        ]);

        $response = [
            'garage'=>$garage,
        ];
        return Response()->json(['status'=>'success','data'=>$response], 200 );
    }

}