<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\ProductsResource;
class OrdersResource extends JsonResource
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
            
            'total'=> (string)$this->total,
            'date'=>date('Y-m-d',strtotime($this->created_at)),
            'shipping'=> (string)$this->shipping,
            'price_after_discount'=>(string) $this->price_after_discount,
             'address'=>$this->address? $this->address->toArray():null,
            'status_id'=> (string)$this->status_id,
            'status'=> $this->status,
            
             'details'=> $this->details($this,$request)
           
        ];
    }
    function details($order,$request){
    $arr=[];
    foreach ($order->details as $key=>$detail){
        $arr[$key]['product']=new ProductsResource($detail->product);
           $arr[$key]['number']=(string)$detail->number;
            $arr[$key]['value_id']=(string) $detail->value_id;
             $arr[$key]['value']= $detail->value ? $detail->value->lang($request->lang_id)->value : '';
             $arr[$key]['code']= $detail->value ? $detail->value->code : '';
    }
       return $arr;      
    }
}
