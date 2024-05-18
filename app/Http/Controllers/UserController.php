<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\User;

class UserController extends Controller
{
    public function completeUserData(Request $request)
    {
        $request->validate([
            "phone"=>"required|numeric|unique:users,phone",
            "car_number"=>"string",
            "license"=>"string",
            'user_img' => 'required|image|mimes:jpeg,png,jpg,gif,svg',
        ]);
        $id = $request->query('id');
        $user = User::find($id);
        if ($request->hasFile('user_img')) {
            $image = $request->file('user_img');
            $imageName = time() . '.' . $image->extension();
            $image->move(public_path('images'), $imageName);
            $user->user_img = $imageName;
        }
        $user->update([
            'phone' => $request->phone,
            'car_number' => $request->car_number,
            'license'=> $request->license,
            'user_img' => $imageName, 
        ]);

        $response = [
            'user'=>$user,
        ];

        return Response()->json(['status'=>'success','data'=>$response], 200 );
    }

    public function update(Request $request)
    {
        $request->validate([
            "name"=>"required|string",
            "email"=>"required|email|string",
            "phone"=>"required|numeric",
            "car_number"=>"string",
            "license"=>"string",
            'user_img' => 'required|image|mimes:jpeg,png,jpg,gif,svg',
        ]);
        $id = $request->query('id');
        $user = User::find($id);
        if ($request->hasFile('user_img')) {
            $image = $request->file('user_img');
            $imageName = time() . '.' . $image->extension();
            $image->move(public_path('images'), $imageName);
            $user->user_img = $imageName;
        }
        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'car_number' => $request->car_number,
            'license'=> $request->license,
        ]);

        $response = [
            'user'=>$user,
        ];
        
        return Response()->json(['status'=>'success','data'=>$response], 200 );
    }

    public function getImage(Request $request)
    {
        $id = $request->query('id');
        $user = User::find($id);
        $imageName = $user->user_img;
        $imageUrl = asset("images/$imageName"); 
        
        $response = [
        'img'=> $imageUrl,
        ];
        return Response()->json(['status'=>'success','data'=>$response], 200 );
    }
}
