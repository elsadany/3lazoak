<?php

namespace App\Http\Controllers\apis;

use Elsayednofal\BackendLanguages\Models\Languages;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Validator;
use App\Models\Wishlist;
use App\Http\Resources\ProductsResource;

class WishlistApi extends Controller {

    function index(Request $request) {
        $wishlists = Wishlist::where('user_id', $request->user()->id)->pluck('product_id')->toArray();
        $products = \App\Models\Product::whereIn('id', $wishlists)->orderBy('id', 'desc')->get();
        return response()->json(['status' => 200, 'data' => $products->toArray()]);
    }

    function add(Request $request) {
        $validator = \Validator::make($request->all(), [
                    'product_id' => 'required|exists:products,id',
        ]);
        if ($validator->fails())
            return response()->json(['status' => 500, 'message' => 'Invalide Data', 'errors' => $validator->errors()->all()]);

        $wishlist = Wishlist::where('user_id', $request->user()->id)->where('product_id', $request->product_id)->first();
        if (!is_object($wishlist))
            $wishlist = new Wishlist();
        $wishlist->user_id = $request->user()->id;
        $wishlist->product_id = $request->product_id;
        $wishlist->save();
        return response()->json(['status' => 200, 'message' => 'success']);
    }
    function delete(Request $request){
       $validator = \Validator::make($request->all(), [
                    'product_id' => 'required|exists:products,id',
        ]);
        if ($validator->fails())
            return response()->json(['status' => 500, 'message' => 'Invalide Data', 'errors' => $validator->errors()->all()]);
   $wishlist = Wishlist::where('user_id', $request->user()->id)->where('product_id', $request->product_id)->first();
        if (!is_object($wishlist))
            return response()->json(['status' => 500, 'message' => 'Invalide Data', 'errors' => ['not in wishlist']]);
        Wishlist::where('user_id', $request->user()->id)->where('product_id', $request->product_id)->delete();
          return response()->json(['status' => 200, 'message' =>'success' ]);
    }

}
