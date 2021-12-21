<?php

namespace App\Http\Controllers\apis;

use Elsayednofal\BackendLanguages\Models\Languages;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\HomepageResourse;
use Validator;
use App\Http\Resources\OrdersResource;
use App\Models\Address;
use App\Http\Resources\CartsResource;

class OrdersApi extends Controller {

    function all(Request $request) {
        $orders = new \App\Models\Order();
        if ($request->status_id != '')
            $orders = $orders->where('status_id', $request->status_id);
        if ($request->family_id != '')
            $orders = $orders->where('family_id', $request->family_id);
        if ($request->delivery_id != '')
            $orders = $orders->where('delivery_id', $request->delivery_id);
               if ($request->status != ''&& is_array($request->status))
            $orders = $orders->whereIn('status_id', $request->status);
        if ($request->user_id != '')
            $orders = $orders->where('user_id', $request->user_id);
        $orders = $orders->orderBy('id', 'desc')->paginate(30);
        return response()->json(['status' => 200, 'data' => $orders->toArray()]);
    }

    function cost(Request $request) {
        $rules = ['address_id' => 'required|exists:addresses,id',
            'promo_id' => 'exists:promo_codes,id',
        ];
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails())
            return response()->json(['status' => 500, 'message' => 'Invalide Data', 'errors' => $validator->errors()->all()]);
        $cart = \App\Models\Cart::where('user_id', auth()->guard('api')->user()->id)->first();
        if (!is_object($carts))
            return response()->json(['status' => 500, 'message' => 'Invalide Data', 'errors' => ['Cart Has no Products']]);
        if ($request->promo_id != '') {
            $promo = \App\Models\PromoCode::where('id', $request->promo_id)->whereDate('expire', '<', \Carbon\Carbon::now())->first();
            if (!is_object($promo))
                return response()->json(['status' => 500, 'message' => 'Invalide Data', 'errors' => ['Promo not exist']]);
        }
        $data['total'] = $total = $cart->total;
        $data['discount'] = $discount = 0;
        if ($request->promo_id != '') {
            $data['discount'] = $discount = $total * $promo->discount_precent / 100;
        }
        $data['price_after_discount'] = $price_after_discount = $total - $discount;
        $data['shipping'] = $shipping = 15;
        $data['price_after_shipping'] = $after_shipping = $price_after_discount + $shipping;
        return response()->json(['status' => 200, 'data' => CartsResource::collection($carts),
                    'total' => $total, 'discount' => $discount, 'price_after_discount' => $price_after_discount, 'shipping' => $shipping, 'price_after_shipping' => $after_shipping, 'prices' => $data]);
    }

    function index(Request $request) {
        $rules = [
            'address_id' => 'required|exists:addresses,id',
        ];


        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails())
            return response()->json(['status' => 500, 'message' => 'Invalide Data', 'errors' => $validator->errors()->all()]);
        $cart = \App\Models\Cart::where('user_id', auth()->guard('api')->user()->id)->first();
        if (!is_object($cart))
            return response()->json(['status' => 500, 'message' => 'Invalide Data', 'errors' => ['Cart Has no Products']]);
        if ($cart->total < 1)
            return response()->json(['status' => 500, 'message' => 'Invalide Data', 'errors' => ['Cart Has no Products']]);
        if ($request->promo_id != '') {
            $promo = \App\Models\PromoCode::where('id', $request->promo_id)->whereDate('expire', '>', \Carbon\Carbon::now())->first();
            if (!is_object($promo))
                return response()->json(['status' => 500, 'message' => 'Invalide Data', 'errors' => ['Promo not exist']]);
        }
        $order = new \App\Models\Order();
        $order->user_id = auth()->guard('api')->user()->id;
        $order->address_id = $request->address_id;
        $order->total_price = $cart->total;
        $order->status_id = 0;
        $shipping = 15;
        $order->total = $order->total + $shipping;
        $order->shipping = $shipping;
        $order->family_id = $cart->family_id;
        $order->user_id = $request->user()->id;
        $order->notes = $request->notes;
        $order->save();
        $this->saveDetails($cart, $order->id);
        $order = \App\Models\Order::find($order->id);
        $family = \App\Models\User::find($order->family_id);
        $response = '';
        $notification = new \App\Models\Notification();
        $notification->notification = 'تم طلب طلب باسم ' . auth()->guard('api')->user()->name;
        $notification->user_id = $family->id;
        $notification->order_id=$order->id;
        $notification->status=$order->status_id;
        $notification->save();
        if ($family->device_id != '')
            $response = $this->notification($family->device_id, 'تم طلب طلب باسم ' . auth()->guard('api')->user()->name, $order->toArray(), $status = 0,$family->token_type);
        \App\Models\Cart::where('user_id', auth()->guard('api')->user()->id)->delete();
        return response()->json(['status' => 200, 'message' => 'success', 'data' => $order->toArray(), 'response' => $response]);
    }

    function calculateTotal($carts) {
        $total = 0;
        foreach ($carts as $cart) {
            $total += $cart->product->price_after_discount * $cart->number;
        }
        return $total;
    }

    function saveDetails($carts, $order_id) {
        foreach ($carts->items as $cart) {
            $detail = new \App\Models\OrderItems;
            $detail->product_id = $cart->product_id;
            $detail->number = $cart->number;

            $detail->price = $cart->price;
            $detail->total = $cart->total;
            $detail->order_id = $order_id;
            $detail->save();
            $cart->delete();
        }
    }

    function familyOrders(Request $request) {
        $orders = \App\Models\Order::where('family_id', $request->user()->id)->orderBy('id', 'desc');
        if ($request->status_id != '')
            $orders = $orders->where('status_id', $request->status_id);
        if ($request->status != ''&& is_array($request->status))
            $orders = $orders->whereIn('status_id', $request->status);
        $orders = $orders->paginate(20);
        return response()->json(['status' => 200, 'data' => $orders->toArray()]);
    }

    function DeliveryNewOrders(Request $request) {
        $orders = \App\Models\Order::where('status_id', 2)->whereNull('delivery_id');

        $orders = $orders->paginate(20);
        return response()->json(['status' => 200, 'data' => $orders->toArray()]);
    }

    function DeliveryOrders(Request $request) {
        $orders = \App\Models\Order::orderBy('id', 'desc');

        if ($request->status_id == 2)
            $orders = $orders->where('status_id', 2)->whereNull('delivery_id');
        elseif ($request->status_id != '')
            $orders = $orders->where('delivery_id', $request->user()->id)->where('status_id', $request->status_id);
        else
            $orders = $orders->where('delivery_id', $request->user()->id);
        $orders = $orders->paginate(20);
        return response()->json(['status' => 200, 'data' => $orders->toArray()]);
    }

    function DeliveryacceptOrders(Request $request) {
        $rules = ['order_id' => 'required|exists:orders,id'];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails())
            return response()->json(['status' => 500, 'message' => 'Invalide Data', 'errors' => $validator->errors()->all()]);
        $order = \App\Models\Order::where('status_id', 2)->where('id', $request->order_id)->whereNull('delivery_id')->first();
        if (!is_object($order))
            return response()->json(['status' => 500, 'message' => 'Invalide Data', 'errors' => ['order not available now']]);
        $order->delivery_id = $request->user()->id;
        $order->status_id = 3;
        $order->save();
        $family = \App\Models\User::find($order->family_id);
        $client = \App\Models\User::find($order->user_id);
        $response = '';
        if ($family->device_id != '')
            $response = $this->notification($family->device_id, 'تم الموافقه على التوصيل', $order->toArray(), $status = 3,$family->token_type);

        $response = '';
        $notification = new \App\Models\Notification();
        $notification->notification = 'تم قبول الطلب من الديليفرى';
        $notification->user_id = $family->id;
         $notification->order_id=$order->id;
        $notification->status=$order->status_id;
        $notification->save();
        

        return response()->json(['status' => 200, 'message' => 'success']);
    }

    function DeliveryrecieveOrders(Request $request) {
        $rules = ['order_id' => 'required|exists:orders,id'];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails())
            return response()->json(['status' => 500, 'message' => 'Invalide Data', 'errors' => $validator->errors()->all()]);
        $order = \App\Models\Order::where('status_id', 3)->where('delivery_id', $request->user()->id)->first();
        if (!is_object($order))
            return response()->json(['status' => 500, 'message' => 'Invalide Data', 'errors' => ['order not available now']]);
        $order->status_id = 4;
        $order->refuse_reason=$request->refuse_reason;
        $order->save();
         $client = \App\Models\User::find($order->user_id);
        $response = '';
          $notification = new \App\Models\Notification();
        $notification->notification = 'تم تحجيز  الطلب وهو قيد التوصيل';
        $notification->user_id = $client->id;
         $notification->order_id=$order->id;
        $notification->status=$order->status_id;
        $notification->save();
          if ($client->device_id != '')
            $response = $this->notification($client->device_id, 'تم الموافقه على التوصيل', $order->toArray(), $status = 4,$client->token_type);

        return response()->json(['status' => 200, 'message' => 'success']);
    }

    function DeliveryfinishOrders(Request $request) {
        $rules = ['order_id' => 'required|exists:orders,id'];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails())
            return response()->json(['status' => 500, 'message' => 'Invalide Data', 'errors' => $validator->errors()->all()]);
        $order = \App\Models\Order::where('status_id', 4)->where('id', $request->order_id)->where('delivery_id', $request->user()->id)->first();
        if (!is_object($order))
            return response()->json(['status' => 500, 'message' => 'Invalide Data', 'errors' => ['order not available now']]);
        $order->status_id = 5;
        $order->save();
        $client = \App\Models\User::find($order->user_id);
        $familly= \App\Models\User::find($order->family_id);
        $response = '';
        $notification = new \App\Models\Notification();
        $notification->notification = 'تم تسليم الطلب';
        $notification->user_id = $familly->id;
                 $notification->order_id=$order->id;
        $notification->status=$order->status_id;
        $notification->save();
        if ($client->device_id != '')
            $response = $this->notification($familly->device_id, 'تم التوصيل', $order->toArray(), $status = 5,$client->token_type);
        return response()->json(['status' => 200, 'message' => 'success']);
    }

    function acceptOrder(Request $request) {
        $rules = ['order_id' => 'required|exists:orders,id'];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails())
            return response()->json(['status' => 500, 'message' => 'Invalide Data', 'errors' => $validator->errors()->all()]);
        $order = \App\Models\Order::where('family_id', $request->user()->id)->where('id', $request->order_id)->first();
        if (!is_object($order))
            return response()->json(['status' => 500, 'message' => 'Invalide Data', 'errors' => ['order not Found']]);
        if ($order->status_id > 0)
            return response()->json(['status' => 500, 'message' => 'Invalide Data', 'errors' => ['order Status is  changed']]);
        $order->status_id = 1;
        $order->save();
        $family = \App\Models\User::find($order->user_id);
        $notification = new \App\Models\Notification();
        $notification->notification = 'تم قبول الطلب وهو الأن قيد التنفيذ ';
        $notification->user_id = $family->id;
                 $notification->order_id=$order->id;
        $notification->status=$order->status_id;
        $notification->save();
        $response = '';
        if ($family->device_id != '')
            $response = $this->notification($family->device_id, 'تم قبول الطلب وهو الأن قيد التنفيذ ', $order->toArray(), $status = 1,$family->token_type);

        return response()->json(['status' => 200, 'message' => 'success', 'response' => $response]);
    }

    function finishOrder(Request $request) {
        $rules = ['order_id' => 'required|exists:orders,id'];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails())
            return response()->json(['status' => 500, 'message' => 'Invalide Data', 'errors' => $validator->errors()->all()]);
        $order = \App\Models\Order::where('family_id', $request->user()->id)->where('id', $request->order_id)->first();
        if (!is_object($order))
            return response()->json(['status' => 500, 'message' => 'Invalide Data', 'errors' => ['order not Found']]);
        if ($order->status_id != 1)
            return response()->json(['status' => 500, 'message' => 'Invalide Data', 'errors' => ['order Status is  changed']]);
        $order->status_id = 2;
        $order->save();
        $deleivers= \App\Models\Delivery::where('status',1)->pluck('user_id');
        
        $delivery = \App\Models\User::where('type',3)->whereIn('id',$deleivers)->whereNotNull('device_id')->get();
        $response = '';
        foreach ($delivery as $one){
        $notification = new \App\Models\Notification();
        $notification->notification = 'طلب جاهز للتوصيل ';
        $notification->user_id = $one->id;
         $notification->order_id=$order->id;
        $notification->status=$order->status_id;
        $notification->save();
   
            $response = $this->notification($one->device_id, 'طلب جاهز للتوصيل', $order->toArray(), 2,$one->token_type);
        }
        return response()->json(['status' => 200, 'message' => 'success']);
    }

    function refuseOrder(Request $request) {
        $rules = ['order_id' => 'required|exists:orders,id'];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails())
            return response()->json(['status' => 500, 'message' => 'Invalide Data', 'errors' => $validator->errors()->all()]);
            
        $order = \App\Models\Order::where('family_id', $request->user()->id)->where('id', $request->order_id)->first();
        if (!is_object($order))
            return response()->json(['status' => 500, 'message' => 'Invalide Data', 'errors' => ['order not Found']]);
        if ($order->status_id > 0)
            return response()->json(['status' => 500, 'message' => 'Invalide Data', 'errors' => ['order Status is  changed']]);
        $order->status_id = 6;
        $order->refuse_reason=$request->refuse_reason;
        $order->save();
         $family = \App\Models\User::find($order->user_id);
        $response = '';
        $notification = new \App\Models\Notification();
        $notification->notification = 'الطلب غير متاح الان ';
        $notification->user_id = $family->id;
        $notification->order_id=$order->id;
        $notification->status=$order->status_id;
        $notification->save();
        if ($family->device_id != '')
            $response = $this->notification($family->device_id, 'الطلب غير متاح الانل', $order->toArray(), $status = 2,$family->token_type);

        return response()->json(['status' => 200, 'message' => 'success']);
    }

    public function notification($token, $title, $order, $status = 0,$type=1) {
        $fcmUrl = 'https://fcm.googleapis.com/fcm/send';
        $token = $token;

        $notification = [
            'title' => $title,
            'sound' => true,
        ];

        $extraNotificationData = ["message" => $title,'body'=>$title,'order_id'=>$order['id'], 'status' => $status,'data'=>$order,'type' => 'order'];

        $fcmNotification = [
            //'registration_ids' => $tokenList, //multple token array
            'to' => $token, //single token
            
            'data' => $extraNotificationData
        ];
          if($type==2){
           $fcmNotification['notification'] = $notification;
        }

        $headers = [
            'Authorization: key=AAAArtZKFa8:APA91bHeYwZbe3sozr360iAV41RFMrCxqYzCsqANqSkDAx1aDKJj8ZSjSzhC6sCDBZKlUj3VMzHzbDKKSgIKUmyBtPMWRY-VX--KJNBavqQ0DvPY7gDZPgT_Prvd7-IReH-ILfI1TU-d',
            'Content-Type: application/json'
        ];


        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $fcmUrl);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fcmNotification));
        $result = curl_exec($ch);
        curl_close($ch);

        return $result;
    }

}
