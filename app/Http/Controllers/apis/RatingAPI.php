<?php

namespace App\Http\Controllers\apis;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Validator;
class RatingAPI extends Controller
{
    function add(Request $request){
        $rules=
                ['family_id'=>'required|exists:famillies,user_id',
                'order_id'=>'required|exists:orders,id',
                    'rate'=>'required|integer|min:1|max:5'
                    ];
         $validator = Validator::make($request->all(),$rules);
        if ($validator->fails() ) {
            return response()->json([
                        'status' => 500,
                        'meesage' => 'please choose your location & language',
                        'errors' => $validator->errors()
            ]);
        }
        $rating=new \App\Models\Rating();
        $rating->rate=$request->rate;
        $rating->family_id=$request->family_id;
        $rating->order_id=$request->order_id;
        $rating->user_id=$request->user()->id;
        $rating->comment=$request->comment;
        $rating->save();
        $shop= \App\Models\Family::find($request->family_id);
   
        $shop->rate= \App\Models\Rating::where('family_id',$shop->user_id)->sum('rate')/\App\Models\Rating::where('family_id',$shop->user_id)->count();
        $shop->rating_num=\App\Models\Rating::where('family_id',$shop->user_id)->count();
        $shop->save();
          return response()->json([
                    'status' => 200,
                    'message' => trans('messages.success'),
                    'data' => []
        ]);
    }
}
