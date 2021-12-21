<?php

namespace App\Http\Controllers\apis;

use App\Models\User;
use App\Models\Address;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\OrdersResource;

class UsersAPI extends Controller {

    function notifications(Request $request) {
        $notifications = \App\Models\Notification::orderBy('id', 'desc')->where('user_id', $request->user()->id)->get();
        return response()->json(['status' => 200, 'data' => $notifications->toArray()]);
    }

    function all(request $request) {
        $users = User::where('type', '!=', 4)->orderBy('id', 'desc');
        if ($request->type != '')
            $users = $users->where('type', $request->type);
        if ($request->active != '')
            $users = $users->where('active', $request->active);
        if ($request->special != '') {
            $ids = \App\Models\Family::where('special', $request->special)->pluck('user_id')->toArray();
            $users = $users->whereIn('id', $ids);
        }
        $users = $users->paginate(20);
        $arr = ['status' => 200, 'message' => '', 'data' => $users->toArray()];
        return response()->json($arr);
    }

    function myacount(Request $request) {
        $user = $request->user();
        $arr = ['status' => 200, 'message' => '', 'data' => $user->toArray()];
        return response()->json($arr);
    }

    function active(Request $request) {
        $rules = ['user_id' => 'required|exists:users,id',
            'active' => 'required'
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails())
            return response()->json(['status' => 500, 'message' => 'Invalide Data', 'errors' => $validator->errors()->all()]);

        $user = User::find($request->user_id);
        $user->active = $request->active;
        $user->save();
        $arr = ['status' => 200, 'message' => 'success'];
        return response()->json($arr);
    }

    function special(Request $request) {
        $rules = ['user_id' => 'required|exists:users,id',
            'special' => 'boolean'
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails())
            return response()->json(['status' => 500, 'message' => 'Invalide Data', 'errors' => $validator->errors()->all()]);

        $user = \App\Models\Family::where('user_id', $request->user_id)->first();
        $user->special = $request->special;
        $user->save();
        $arr = ['status' => 200, 'message' => 'success'];
        return response()->json($arr);
    }
function show(Request $request) {
        $rules = ['user_id' => 'required|exists:users,id',
            
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails())
            return response()->json(['status' => 500, 'message' => 'Invalide Data', 'errors' => $validator->errors()->all()]);

        $user = \App\Models\User::where('id', $request->user_id)->first();
     
        $arr = ['status' => 200, 'message' => 'success','data'=>$user->toArray()];
        return response()->json($arr);
    }
    function updateProfile(Request $request) {
        $rules = [
            'name' => 'required',
            'email' => 'required',
            'phone' => 'required',
        ];
        if ($request->user()->type == 2) {
            $rules['lat'] = 'required';
            $rules['lng'] = 'required';
        }
        if ($request->user()->type == 3) {
            $rules['car_mark'] = 'required';
        }
        if ($request->user()->email != $request->email)
            $rules['email'] = 'required|email|unique:users,email';
        if ($request->user()->phone != $request->phone)
            $rules['phone'] = 'required|unique:users,phone';
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails())
            return response()->json(['status' => 500, 'message' => 'Invalide Data', 'errors' => $validator->errors()->all()]);
        $user = $request->user();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->phone = $request->phone;
        if ($request->hasFile('image'))
            $user->image = $this->uploadfile($request->image);
        if ($request->base_image != '')
            $user->image = $this->uploadbasfile($request->base_image);
        $user->save();
        if ($request->type == 2) {
            $family = \App\Models\Family::where('user_id', $request->user()->id)->first();
            $family->user_id = $user->id;
            $family->lat = $request->lat;
            $family->lng = $request->lng;
            $family->address= $this->getCityFromLatLng($request->lat, $request->lng);
            $family->save();
        }
        if ($request->type == 3) {
            $delivery = \App\Models\Delivery::where('user_id', $request->user()->id)->first();
            $delivery->user_id = $user->id;
            $delivery->car_mark = $request->car_mark;
            if ($request->hasFile('id_image'))
                $delivery->id_image = $this->uploadfile($request->id_image);
            if ($request->hasFile('car_id_image'))
                $delivery->car_id_image = $this->uploadfile($request->car_id_image);
            $delivery->save();
        }
        $arr = ['status' => 200, 'message' => 'success', 'data' => $user->toArray()];
        return response()->json($arr);
    }
    function updateFamilyLocation(Request $request){
          $rules['lat'] = 'required';
            $rules['lng'] = 'required';
            $validator = Validator::make($request->all(), $rules);
        if ($validator->fails())
            return response()->json(['status' => 500, 'message' => 'Invalide Data', 'errors' => $validator->errors()->all()]);
         $family = \App\Models\Family::where('user_id', $request->user()->id)->first();
       
            $family->lat = $request->lat;
            $family->lng = $request->lng;
             $family->address=$this->getCityFromLatLng($request->lat, $request->lng);
            $family->save();
            $user= User::find($request->user()->id);
                  
            $arr = ['status' => 200, 'message' => 'success', 'data' => $user->toArray()];
        return response()->json($arr);
    }
    function updateStatus(Request $request){
        $rules['status']='required|boolean';
                    $validator = Validator::make($request->all(), $rules);

        if ($validator->fails())
            return response()->json(['status' => 500, 'message' => 'Invalide Data', 'errors' => $validator->errors()->all()]);
           $family = \App\Models\Delivery::where('user_id', $request->user()->id)->first();
       
            $family->status = $request->status;
          
            $family->save();
            $user= User::find($request->user()->id);
                  
            $arr = ['status' => 200, 'message' => 'success', 'data' => $user->toArray()];
        return response()->json($arr);
    }
            function updateDeliveryLocation(Request $request){
          $rules['lat'] = 'required';
            $rules['lng'] = 'required';
            $validator = Validator::make($request->all(), $rules);
        if ($validator->fails())
            return response()->json(['status' => 500, 'message' => 'Invalide Data', 'errors' => $validator->errors()->all()]);
         $family = \App\Models\Delivery::where('user_id', $request->user()->id)->first();
       
            $family->lat = $request->lat;
            $family->lng = $request->lng;
            $family->save();
            $user= User::find($request->user()->id);
                  
            $arr = ['status' => 200, 'message' => 'success', 'data' => $user->toArray()];
        return response()->json($arr);
    }
                function updatePassword(Request $request) {

        $rules = [
            'old_password' => 'required',
            'password' => 'required|string',
            'confirm_password' => 'required|string|same:password'
        ];
        $validator = \Validator::make($request->all(), $rules);

        if ($validator->fails()) {

            $arr = ['status' => 500, 'message' => $validator->errors()->all()[0], 'errors' => $validator->errors()->all()];

            return response()->json($arr);
        }
        if (!Hash::check($request->old_password, $request->user()->password)) {
            $arr = ['status' => 500, 'message' => 'password not correct', 'errors' => ['password not correct']];
            return response()->json(
                            $arr);
        }
        $user = $request->user();
        $user->password = Hash::make($request->password);
        $user->save();
        $arr = ['status' => 200, 'message' => 'password changed successfully', 'data' => ''];
        return response()->json($arr);
    }

    public function logout(Request $request) {
        $user = $request->user();
        $user->device_id = '';
        $user->save();
        $request->user()->token()->revoke();
        $arr = ['status' => 200, 'message' => 'Successfully logged out'];
        return response()->json(
                        $arr);
    }

    function getAdreesses(Request $request) {
        return response()->json([
                    'status' => 200,
                    'message' => trans('messages.success'),
                    'data' => Address::where('user_id', auth()->guard('api')->user()->id)->get()
        ]);
    }

    function addAdreess(Request $request) {
        $v = Validator::make($request->all(), [
                    'city' => 'required',
                    'address_name' => 'required',
                    'address_type' => 'required',
                    'lat' => 'required',
                    'lng' => 'required',
        ]);

        if ($v->fails())
            return response()->json(['status' => 500, 'message' => trans('messages.invalide_data'), 'errors' => $v->errors()->all()]);

        $address = new Address;
        $address->city = $request->city;
        $address->address_name = $request->address_name;
        $address->address_type = $request->address_type;

        $address->lat = $request->lat;
        $address->lng = $request->lng;
        $address->flat_no = $request->flat_no;
        $address->mark = $request->mark;
        $address->phone = $request->phone;
        $address->user_id = auth()->guard('api')->user()->id;
        $address->address= $this->getCityFromLatLng($request->lat, $request->lng);
        $address->save();

        return response()->json([
                    'status' => 200,
                    'message' => trans('messages.success'),
                    'data' => Address::where('user_id', auth()->guard('api')->user()->id)->get()
        ]);
    }

    function updateDeviceId(Request $request) {
        $v = Validator::make($request->all(), [
                    'device_id' => 'required',
        ]);

        if ($v->fails())
            return response()->json(['status' => 500, 'message' => trans('messages.invalide_data'), 'errors' => $v->errors()->all()]);
        $user = auth()->guard('api')->user();
        $user->device_id = $request->device_id;
        if ($request->type)
            $user->token_type = $request->type;
        $user->save();
        return response()->json(['status' => 200, 'message' => 'success']);
    }

    function updateAdrress(Request $request) {
        $v = Validator::make($request->all(), [
                    'address_id' => 'required|exists:addresses,id'
        ]);

        if ($v->fails())
            return response()->json(['status' => false, 'message' => trans('messages.invalide_data'), 'errors' => $v->errors()->all()]);

        $address = Address::where('id', $request->address_id)->where('user_id', $request->user()->id)->first();
        if (!is_object($address))
            return response()->json(['status' => 500, 'message' => trans('messages.invalide_data'), 'errors' => ['not found']]);

        $address->city = $request->city;
        $address->address_name = $request->address_name;
        $address->address_type = $request->address_type;

        $address->lat = $request->lat;
        $address->lng = $request->lng;
        $address->address= $this->getCityFromLatLng($request->lat, $request->lng);

        $address->flat_no = $request->flat_no;
        $address->mark = $request->mark;
        $address->phone = $request->phone;
        $address->save();

        return response()->json([
                    'status' => 200,
                    'message' => trans('messages.success'),
                    'data' => Address::where('user_id', auth()->guard('api')->user()->id)->get()
        ]);
    }

    function orders(Request $request) {
        $orders = \App\Models\Order::where('user_id', $request->user()->id)->orderBy('id', 'desc')->get();
        return response()->json([
                    'status' => 200,
                    'message' => trans('messages.success'),
                    'data' => $orders->toArray()
        ]);
    }

    function showOrder(Request $request) {
        $order = \App\Models\Order::where('id', $request->order_id)->first();
        return response()->json([
                    'status' => 200,
                    'message' => trans('messages.success'),
                    'data' => $order
        ]);
    }

    function cancelOrder(Request $request) {
        $order = \App\Models\Order::where('id', $request->order_id)->where('user_id', $request->user()->id)->first();
        if (!is_object($order))
            return response()->json(['status' => 500, 'errors' => ['Order Not Found']]);
        if ($order->status_id > 0)
            return response()->json(['status' => 500, 'errors' => ['Order cant Not canceled now']]);
        \App\Models\Order::where('id', $request->order_id)->where('user_id', $request->user()->id)->delete();
        return response()->json([
                    'status' => 200,
                    'message' => trans('messages.success'),
        ]);
    }

    function deleteAdrress(Request $request) {
        $v = Validator::make($request->all(), [
                    'address_id' => 'required|exists:addresses,id'
        ]);

        if ($v->fails())
            return response()->json(['status' => 500, 'message' => trans('messages.invalide_data'), 'errors' => $v->errors()->all()]);

        $address = Address::where('id', $request->address_id)->where('user_id', $request->user()->id)->first();
        if (!is_object($address))
            return response()->json(['status' => 500, 'message' => trans('messages.invalide_data'), 'errors' => ['not found']]);

        $address->delete();

        return response()->json([
                    'status' => 200,
                    'message' => trans('messages.success'),
                    'data' => Address::where('user_id', auth()->guard('api')->user()->id)->get()
        ]);
    }

    private function uploadfile($file) {
        $path = 'uploads/users';
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
        $path = 'uploads/users';
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
 function getCityFromLatLng($lat, $lng) {

        $endpoint = "https://maps.googleapis.com/maps/api/geocode/json";
        $client = new \GuzzleHttp\Client();


        $response = $client->request('GET', $endpoint, ['query' => [
                'latlng' => $lat . ',' . $lng,
                'sensor' => true,
                'key' => 'AIzaSyAylzC-TDTEVjgHp5EI1ofRN5Jhdrekrhg',
        ]]);


        $statusCode = $response->getStatusCode();
        $content = json_decode($response->getBody(), true);

        // dd($content);
        if (key_exists('results', $content) && count($content['results']) >= 3) {
            $address= $content['results'][0]['formatted_address'];
            $last_index = count($content['results']) - 2;
            $result['country'] = $content['results'][$last_index]['formatted_address'];
            $governorate = $content['results'][$last_index - 1]['formatted_address'];
            $governorate = explode(',', $governorate);
            $result['gover'] = $governorate[0];
            $city = $content['results'][$last_index - 2]['formatted_address'];
            $city = explode(',', $city);
           
            return $address;
            $location = \App\Models\Location::where('name', $result['city'])->first();
            if (!is_object($location)) {
                $location = new \App\Models\Location;
                $location->name = $result['city'];
                $location->save();
            }
            return $location->id;
            return $result;
        }

        return  ['address'=>false,'city'=>false];
    }
}
