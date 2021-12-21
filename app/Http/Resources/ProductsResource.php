<?php

namespace App\Http\Resources;

use App\Models\ProductOption;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductsResource extends JsonResource {

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request) {
        return [
            'id' => $this->id,
            'name' =>$this->lang($request->lang_id)? $this->lang($request->lang_id)->name:'',
            'brand' => $this->brand ?$this->brand->lang($request->lang_id)->name: '',
            'brand_id' => $this->brand_id,
            'image' => $this->imagepath,
            'description' =>$this->lang($request->lang_id)? $this->lang($request->lang_id)->description :"",
            'images' => $this->images($this),
            'price' =>(string) $this->price,
            'discount' =>(string) $this->discount,
            'discount_precent' =>(string) $this->discount>0? $this->discount:'0',
            'has_video' => $this->video!=''? 1:0,
            'video' => $this->video,
            'price_after_discount' =>(string) $this->price_after_discount,
            'stock' =>(string) $this->stock,
            'properties' => $this->getproperties($this, $request),
//            'options_data'=>ProductOptionResource::collection($this->options),
            //'options_data'=>$this->handelOptions($this),
            'options' =>  count($this->getOptions2($this, $request)) > 0 ? $this->getOptions2($this, $request) : null,
            'has_options' => count($this->getOptions2($this, $request)) > 0 ? true : false,
            'in_wishlist'=> $this->checkWishlist($this,$request)
        ];
    }

    function handelOptions($product){
        $result=[];
        foreach($product->options as $option){
            //if(!key_exists($option->option_id,$result)){
                $result[$option->option_id]=$option;
            //}
        }
        return $result;
    }

    function getproperties($product, $request) {
        $catoptions_ids = \App\Models\CategoryOption::whereNull('is_stock')->pluck('id')->toArray();
        $options = $product->options()->whereIn('option_id', $catoptions_ids)->get();
        $optionsArr = [];
        foreach ($options as $key => $one) {
            $optionsArr[$key]['option'] = $one->option->lang($request->lang_id)->name;

            $optionsArr[$key]['value'] ='';
            if ($one->value) {
                $optionsArr[$key]['value'] = $one->value->lang($request->lang_id)->value;
            } elseif ($one->has_value==1) {
                $optionsArr[$key]['value'] =$one->lang($request->lang_id)->value;
            } 
        }
        return $optionsArr;
    }

    function images($product){
        $arr=[];
        foreach ($product->images as $key=>$val){
            $arr[$key]=$val->imagepath;
        }
        return $arr;
    }
    

    function getOptions($product, $request){
        $options=ProductOption::where('product_id',$product->id)->with('langs')->with('images')->get();
        return $options;
    }

    function getOptions2($product, $request) {
        
        $catoptions_ids = \App\Models\CategoryOption::where('is_stock', 1)->pluck('id')->toArray();
        $optiondata=$product->options()->whereIn('option_id', $catoptions_ids)->first();
        $optionsArr = [];
       $valuearr = [];
        
        if(is_object($optiondata)){
            $optionsArr['option']=$optiondata->option->lang($request->lang_id)->name;
            $optionsArr['type_id']=$optiondata->option->type;
            $optionsArr['type']=$optiondata->option->typename;
        $options = $product->options()->where('option_id', $optiondata->option_id)->get();
        foreach ($options as $key => $one) {

               //$optionsArr['values'][$key]['id'] = $one->value->lang($request->lang_id)->id;
               $optionsArr['values'][$key]['value'] = $one->value->lang($request->lang_id)->value;
            $optionsArr['values'][$key]['value_id'] =(string) $one->value_id;
            $optionsArr['values'][$key]['code'] = $one->value->code;
            $optionsArr['values'][$key]['images'] = $one->imageArr;

            $optionsArr['values'][$key]['stock'] =(string) $one->stock;
        }
        }
        return $optionsArr;
    }
    function checkWishlist($product,$request){
      
         if (auth()->guard('api')->check()) {
             $wishlist= \App\Models\Wishlist::where('user_id',auth()->guard('api')->user()->id)->where('product_id',$product->id)->first();
             if(is_object($wishlist))
                 return 1 ;
         }
         return 0;
    }

}
