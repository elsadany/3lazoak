<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class HomepageResourse extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'name'=> $this->lang($request->lang_id)->name,
            'products'=> $this->getProducts($this)
        ];
    }
    function getProducts($homepage){
        $products= \App\Models\Product::whereIn('id',$homepage->products()->pluck('product_id')->toArray())->get();
        return ProductsResource::collection($products);
    }
}
