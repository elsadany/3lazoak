<?php

namespace App\Http\Controllers\apis;

use Elsayednofal\BackendLanguages\Models\Languages;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProductsResource;
use App\Models\Product;
use App\Models\Cart;
use App\Models\CartItems;
use App\Http\Resources\CartsResource;
use Validator;

class CartsApi extends Controller {

    function add(Request $request) {
        $rules = ['product_id' => 'required|exists:products,id'];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails())
            return response()->json(['status' => 500, 'message' => 'Invalide Data', 'errors' => $validator->errors()->all()]);
        $product = Product::find($request->product_id);
        $cart= Cart::where('family_id',$product->user_id)->where('user_id',$request->user()->id)->first();
        $oldcart= Cart::where('family_id','!=',$product->user_id)->where('user_id',$request->user()->id)->first();
        if(is_object($oldcart)){
            return response ()->json(['status'=>500,'errors'=>['The Cart has products from other shop'],'type'=>1]);
        }
       
        if(!is_object($cart))
            $cart=new Cart ();
        $cart->family_id=$product->user_id;
        $cart->user_id=$request->user()->id;
        $cart->total=$product->price;
        $cart->save();
        $cartitem= CartItems::where('cart_id',$cart->id)->where('product_id',$request->product_id)->first();
        if(!is_object($cartitem))
            $cartitem=new CartItems;
        $cartitem->product_id=$request->product_id;
        $cartitem->cart_id=$cart->id;
        $cartitem->number=$cartitem->number+1;
        $cartitem->price=$product->price;
        $cartitem->total=$cartitem->number * $product->price;
        $cartitem->save();
        $cart->total= CartItems::where('cart_id',$cart->id)->sum('total');
        $cart->save();
   $cart= Cart::where('user_id',$request->user()->id)->first();
        return response()->json(['status' => 200, 'data' => $cart->toArray(), 'message' => 'success']);
    }

    function edit(Request $request) {
        $rules = ['item_id' => 'required|exists:cart_items,id',
            'number' => 'required|min:1|integer'
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails())
            return response()->json(['status' => 500, 'message' => 'Invalide Data', 'errors' => $validator->errors()->all()]);

        $item = CartItems::find($request->item_id);
        
        $number = $request->number;
        $item->number = $number;
        $item->total=$request->number * $item->price;
        $item->save();
        $cart= Cart::find($item->cart_id);
        $cart->total= CartItems::where('cart_id',$item->cart_id)->sum('total');
        $cart->save();
        return response()->json(['status' => 200, 'data' => $cart->toArray(), 'message' => 'success']);
    }

    function index(Request $request) {
     
          $carts=Cart::where('user_id',auth()->guard('api')->user()->id)->get();
          
        
        return response()->json(['status'=>200,'message'=>'success','data'=>$carts->toArray()]);
    }
    function countCart(Request $request){
         $carts=Cart::where('user_id',auth()->guard('api')->user()->id)->first();
          $count=0;
          $total=0;
          if(is_object($carts)){
              $count=$carts->number;
              $total=$carts->total;
          }
        
        return response()->json(['status'=>200,'message'=>'success','count'=>$count,'total'=>$total]);
    }
            function clear(Request $request) {
 
         
          $carts=Cart::where('user_id',auth()->guard('api')->user()->id)->delete();
       
        
        return response()->json(['status'=>200,'message'=>'success']);
    }

    function delete(Request $request) {
          $rules = [
              'item_id' => 'required',
              'item_id.*' => 'required|exists:cart_items,id',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails())
            return response()->json(['status' => 500, 'message' => 'Invalide Data', 'errors' => $validator->errors()->all()]);
        
        $item=CartItems::whereIn('id',$request->item_id)->first();
         $cart= Cart::find($item->cart_id);
        $cart->total= CartItems::where('cart_id',$item->cart_id)->sum('total');
        $cart->save();
        CartItems::whereIn('id',$request->item_id)->delete();
        if(CartItems::where('cart_id',$cart->id)->count()<1)
            Cart::where('id',$cart->id)->delete();    
                return response()->json(['status' => 200,'message' => 'success']);

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
    function calculateTotal($carts){
        $total=0;
        foreach ($carts as $cart){
            $total+=$cart->product->price_after_discount *$cart->number;
        }
        return $total;
    }
            function checkPromo(Request $request){
        $rules = ['promocode' => 'required|exists:promo_codes,code',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails())
            return response()->json(['status' => 500, 'message' => 'Invalide Data', 'errors' => $validator->errors()->all()]);
        $promocode= \App\Models\PromoCode::where('code',$request->promocode)->whereDate('expire', '>',\Carbon\Carbon::now())->first();
        if(!is_object($promocode))
                        return response()->json(['status' => 500, 'message' => 'Invalide Data', 'errors' => ['Promocode is not Found']]);
        return response()->json(['status'=>200,'message'=>'success','data'=>$promocode->toArray()]);
    }

    function assignToUser(Request $request){
        Cart::where('session_id',$request->session_id)->update(['user_id'=>$request->user()->id]);
    }
    
}
