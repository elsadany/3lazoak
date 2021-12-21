<?php

namespace App\Http\Controllers\apis;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Validator;

class CitiesController extends Controller {

    function index(Request $request) {
        $cities = \App\Models\City::OrderBy('id','desc');
       
        $cities = $cities->orderBy('id', 'desc')->get();
        return response()->json(['status' => 200, 'data' => $cities->toArray()]);
    }

    function add(Request $request) {
        $rules = [
            'name' => 'required',
           
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails())
            return response()->json(['status' => 500, 'message' => 'Invalide Data', 'errors' => $validator->errors()->all()]);
        $city = new \App\Models\City();
        $city->name = $request->name;
   
        $city->save();
        return response()->json(['status' => 200, 'message' => 'added']);
    }

    function display(Request $request) {
        $rules = [
            'city_id' => 'required|exists:cities,id'
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails())
            return response()->json(['status' => 500, 'message' => 'Invalide Data', 'errors' => $validator->errors()->all()]);
        $city = \App\Models\City::where('id', $request->city_id)->first();
        if (!is_object($city))
            return response()->json(['status' => 500, 'message' => 'Invalide Data', 'errors' => [' Not Found']]);
        return response()->json(['status' => 200, 'data' => $city->toArray()]);
    }

    function edit(Request $request) {
        $rules = [
            'name' => 'required',
            'city_id' => 'required|exists:cities,id'
        ];
      
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails())
            return response()->json(['status' => 500, 'message' => 'Invalide Data', 'errors' => $validator->errors()->all()]);
        $city = \App\Models\City::where('id', $request->city_id)->first();
        if (!is_object($city))
            return response()->json(['status' => 500, 'message' => 'Invalide Data', 'errors' => [' Not Found']]);
        $city->name = $request->name;
        
        $city->save();
        return response()->json(['status' => 200, 'message' => 'updated']);
    }

    function delete(Request $request) {
        $rules = [
            'city_id' => 'required|exists:cities,id'
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails())
            return response()->json(['status' => 500, 'message' => 'Invalide Data', 'errors' => $validator->errors()->all()]);
        $city = \App\Models\City::where('id', $request->city_id)->first();
        if (!is_object($city))
            return response()->json(['status' => 500, 'message' => 'Invalide Data', 'errors' => ['product Not Found']]);
        $city = \App\Models\City::where('id', $request->city_id)->delete();
        return response()->json(['status' => 200, 'message' => 'deleted']);
    }

    function all(Request $request) {
        $citys = \App\Models\Product::orderBy('id', 'desc');
        if ($request->family_id!='')
            $citys = $citys->where('user_id', $request->family_id);
        if ($request->city_id!='')
            $citys = $citys->where('city_id', $request->city_id);
        $citys = $citys->get();
        $citysarr = [];
        foreach ($citys as $key => $city) {
            $citysarr[$key] = $city->toArray();
            $citysarr[$key]['is_fav'] = 0;
            if (auth()->guard('api')->check()) {
                $wishlist = \App\Models\Wishlist::where('user_id', $request->user()->id)->where('product_id', $city->id)->first();
                if (is_object($wishlist))
                    $citysarr[$key]['is_fav'] = 1;
            }
        }
        return response()->json(['status' => 200, 'data' => $citysarr]);
    }

    function show(Request $request) {
        $rules = [
            'product_id' => 'required|exists:products,id'
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails())
            return response()->json(['status' => 500, 'message' => 'Invalide Data', 'errors' => $validator->errors()->all()]);
        $citysarr = [];
        $city = \App\Models\Product::where('id', $request->product_id)->first();

        $citysarr = $city->toArray();
        $citysarr['is_fav'] = 0;
        if (auth()->guard('api')->check()) {
            $wishlist = \App\Models\Wishlist::where('user_id', $request->user()->id)->where('product_id', $city->id)->first();
            if (is_object($wishlist))
                $citysarr['is_fav'] = 1;
        }
        $related= \App\Models\Product::where('id','!=',$city->id)->where('user_id',$city->user_id)->orderBy('id','desc')->get();
        
        return response()->json(['status' => 200, 'data' => $citysarr,'related'=>$related->toArray()]);
    }

    private function uploadfile($file) {
        $path = 'uploads/products';
        if (!file_exists($path)) {
            mkdir($path, 0775);
        }
        $datepath = date('m-Y', strtotime(\Carbon\Carbon::now()));
        if (!file_exists($path . '/' . $datepath)) {
            mkdir($path . '/' . $datepath, 0775);
        }
        $newdir = $path . '/' . $datepath;
        $exten = $file->getClientOriginalExtension();
        $filename = $this->generateRandom($length = 15);
        $filename = $filename . '.' . $exten;
        $file->move($newdir, $filename);
        return $newdir . '/' . $filename;
    }

    private function generateRandom($length = 11) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = time();
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }

        return $randomString;
    }

    private function uploadbasfile($file) {
        $path = 'uploads/products';
        if (!file_exists($path)) {
            mkdir($path, 0775);
        }
        $datepath = date('m-Y', strtotime(\Carbon\Carbon::now()));
        if (!file_exists($path . '/' . $datepath)) {
            mkdir($path . '/' . $datepath, 0775);
        }
        $newdir = $path . '/' . $datepath;
        $exten = 'png';
        $filename = $this->generateRandom($length = 15);
        $filename = $filename . '.' . $exten;
        $filedate = base64_decode($file);

        file_put_contents($newdir . '/' . $filename, $filedate);

        return $newdir . '/' . $filename;
    }

}
