<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductOptionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        //return parent::toArray($request);
        return [
            'id'=>$this->id,
            'images'=>$this->images,
            'lang'=>$this->lang($request->lang_id),
            'option'=>$this->option->lang($request->lang_id)->name,
            'value'=>$this->getValue($this,$request)
        ];
    }

    function getValue($product_option,$request){
        if($product_option->value_id!=''){
            $x=1;
            $value=$product_option->value()->first()->lang($request->lang_id)->value;
        }else{
            $x=2;
            $value=$product_option->lang($request->lang_id)->value;
        }
        
        return $value;
    }
}
