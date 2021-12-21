<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CartsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
         return[
            
            'id'=> $this->id,
            'number'=> (string)$this->number,
            'product'=> new ProductsResource($this->product),
            
             'value_id'=> $this->value_id,
             'value'=> $this->value ? $this->value->lang($request->lang_id)->value : '',
             'code'=> $this->value ? $this->value->code : '',
           
        ];
    }
}
