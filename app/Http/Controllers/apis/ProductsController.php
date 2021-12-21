<?php

namespace App\Http\Controllers\apis;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Validator;

class ProductsController extends Controller {

    function index(Request $request) {
        $products = \App\Models\Product::where('user_id', $request->user()->id);
        if ($request->category_id)
            $products = $products->where('category_id', $request->category_id);
        $products = $products->orderBy('id', 'desc')->get();
        return response()->json(['status' => 200, 'data' => $products->toArray()]);
    }
     function active(Request $request) {
        $rules = ['product_id' => 'required|exists:products,id',
            'active' => 'required|boolean'
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails())
            return response()->json(['status' => 500, 'message' => 'Invalide Data', 'errors' => $validator->errors()->all()]);

        $product = \App\Models\Product::find($request->product_id);
        $product->active = $request->active;
        $product->save();
        $arr = ['status' => 200, 'message' => 'success'];
        return response()->json($arr);
    }


    function create(Request $request) {
        $rules = [
            'name' => 'required',
            'price' => 'required|min:1',
            'category_id' => 'required|numeric|exists:categories,id',
            'description' => 'required',
            'image' => 'required|image'
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails())
            return response()->json(['status' => 500, 'message' => 'Invalide Data', 'errors' => $validator->errors()->all()]);
        $product = new \App\Models\Product();
        $product->name = $request->name;
        $product->price = $request->price;

        $product->category_id = $request->category_id;
        $product->image = $this->uploadfile($request->image);
        $product->description = $request->description;
        $product->user_id = $request->user()->id;
        $product->save();
        return response()->json(['status' => 200, 'message' => 'added']);
    }

    function display(Request $request) {
        $rules = [
            'product_id' => 'required|exists:products,id'
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails())
            return response()->json(['status' => 500, 'message' => 'Invalide Data', 'errors' => $validator->errors()->all()]);
        $product = \App\Models\Product::where('id', $request->product_id)->where('user_id', $request->user()->id)->first();
        if (!is_object($product))
            return response()->json(['status' => 500, 'message' => 'Invalide Data', 'errors' => ['product Not Found']]);
        return response()->json(['status' => 200, 'data' => $product->toArray()]);
    }

    function update(Request $request) {
        $rules = [
            'name' => 'required',
            'price' => 'required|min:1',
            'category_id' => 'required|numeric|exists:categories,id',
            'description' => 'required',
            'product_id' => 'required|exists:products,id'
        ];
        if ($request->has('image'))
            $rules['image'] = 'required|image';
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails())
            return response()->json(['status' => 500, 'message' => 'Invalide Data', 'errors' => $validator->errors()->all()]);
        $product = \App\Models\Product::where('id', $request->product_id)->where('user_id', $request->user()->id)->first();
        if (!is_object($product))
            return response()->json(['status' => 500, 'message' => 'Invalide Data', 'errors' => ['product Not Found']]);
        $product->name = $request->name;
        $product->price = $request->price;
        $product->category_id = $request->category_id;
         if ($request->has('image'))
        $product->image = $this->uploadfile($request->image);
        $product->description = $request->description;
        $product->user_id = $request->user()->id;
        $product->save();
        return response()->json(['status' => 200, 'message' => 'updated']);
    }

    function delete(Request $request) {
        $rules = [
            'product_id' => 'required|exists:products,id'
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails())
            return response()->json(['status' => 500, 'message' => 'Invalide Data', 'errors' => $validator->errors()->all()]);
        $product = \App\Models\Product::where('id', $request->product_id)->where('user_id', $request->user()->id)->first();
        if (!is_object($product))
            return response()->json(['status' => 500, 'message' => 'Invalide Data', 'errors' => ['product Not Found']]);
        $product = \App\Models\Product::where('id', $request->product_id)->where('user_id', $request->user()->id)->delete();
        return response()->json(['status' => 200, 'message' => 'deleted']);
    }

    function all(Request $request) {
        $products = \App\Models\Product::orderBy('id', 'desc');
        if($request->lat!=''&&$request->lng!=''){
            $ids=$this->getclosetDelivery ($request->lat, $request->lng);
            $ids_ar=implode(',', $ids);
             $products =$products->whereIn('user_id', $ids)->orderByRaw('FIELD(id,' . $ids_ar . ')');
        }
        if ($request->family_id!='')
            $products = $products->where('user_id', $request->family_id);
        if ($request->category_id!='')
            $products = $products->where('category_id', $request->category_id);
              if ($request->active != '')
            $products = $products->where('active', $request->active);
        $products = $products->get();
        
        $productsarr = [];
        foreach ($products as $key => $product) {
            $productsarr[$key] = $product->toArray();
            $productsarr[$key]['is_fav'] = 0;
            if (auth()->guard('api')->check()) {
                $wishlist = \App\Models\Wishlist::where('user_id', auth()->guard('api')->user()->id)->where('product_id', $product->id)->first();
                if (is_object($wishlist))
                    $productsarr[$key]['is_fav'] = 1;
            }
        }
        return response()->json(['status' => 200, 'data' => $productsarr]);
    }

    function show(Request $request) {
        $rules = [
            'product_id' => 'required|exists:products,id'
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails())
            return response()->json(['status' => 500, 'message' => 'Invalide Data', 'errors' => $validator->errors()->all()]);
        $productsarr = [];
        $product = \App\Models\Product::where('id', $request->product_id)->first();

        $productsarr = $product->toArray();
        $productsarr['is_fav'] = 0;
        if (auth()->guard('api')->check()) {
            $wishlist = \App\Models\Wishlist::where('user_id', auth()->guard('api')->user()->id)->where('product_id', $product->id)->first();
            if (is_object($wishlist))
                $productsarr['is_fav'] = 1;
        }
        $related= \App\Models\Product::where('id','!=',$product->id)->where('user_id',$product->user_id)->orderBy('id','desc')->get();
        
        return response()->json(['status' => 200, 'data' => $productsarr,'related'=>$related->toArray()]);
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
      private function getclosetDelivery($lat, $lng) {
        $breanches = \App\Models\Family::all();
        $locations = [];
        foreach ($breanches as $key => $branch) {
            $locations[$key]['user_id'] = $branch->user_id;
            $locations[$key]['lat'] = $branch->lat;
            $locations[$key]['lng'] = $branch->lng;
        }



        $distances = array();

        foreach ($locations as $key => $location) {
            $a = $lat - $location['lat'];
            $b = $lng - $location['lng'];
            $theta = $lng - $location['lng'];
            $dist = sin(deg2rad($lat)) * sin(deg2rad($location['lat'])) + cos(deg2rad($lat)) * cos(deg2rad($location['lat'])) * cos(deg2rad($theta));
            $dist = acos($dist);
            $dist = rad2deg($dist);
            $distance = $dist * 60 * 1.1515 * 1.609344;

//            $distance = sqrt(($a ** 2) + ($b ** 2));
//            $distance=$distance * 60 * 1.1515 * 1.609344;
            $distances[$location['user_id']] = $distance;
        }

        asort($distances);
       

        return array_keys ($distances);
        return false;
    }

}
