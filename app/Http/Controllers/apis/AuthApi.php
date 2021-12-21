<?php

namespace App\Http\Controllers\apis;

use Carbon\Carbon;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Validator;
use App\Models\Cart;
use GuzzleHttp\Client;

class AuthApi extends Controller {

    function checkphone(Request $request) {
        $rules = [
            'phone' => 'required|numeric',
        ];
        $validator = \Validator::make($request->all(), $rules);

        if ($validator->fails()) {

            $arr = ['status' => 500, 'errors' => $validator->errors()->all()];

            return response()->json($arr);
        }
        $user = User::where('phone', $request->phone)->first();
        if (is_object($user))
            $arr = ['status' => 200, 'message' => 'exists'];
        else
            $arr = ['status' => 200, 'message' => 'success'];


        return response()->json($arr);
    }

    function sendSms(Request $request) {
        $rules = [
            'phone' => 'required|numeric',
        ];
        $validator = \Validator::make($request->all(), $rules);

        if ($validator->fails()) {

            $arr = ['status' => 500, 'errors' => $validator->errors()->all()];

            return response()->json($arr);
        }
        $code = $this->generatemobie(4);
        $phone = new \App\Models\PhoneCode();
        $phone->code = $code;
        $phone->phone = $request->phone;
        $phone->save();
        $response = $this->send($code, $request->phone);
    if($response!=TRUE){
        return response()->json(['status'=>500,'errors'=>['error in sms provider']]);
    }
        // if($response!=false||$response->ErrorCode!='000')
        //     return response ()->json(['status'=>200,'errors'=>['there are error in sms provider']]);
        $user = User::where('phone', $request->phone)->first();
        if (is_object($user))
            $arr = ['status' => 200, 'message' => 'exists'];
        else
            $arr = ['status' => 200, 'message' => 'success'];


        return response()->json($arr);
    }

    function confirm(Request $request) {
        $rules = [
            'phone' => 'required|exists:phone_codes,phone',
            'confirm_code' => 'required|exists:phone_codes,code'
        ];
        $validator = \Validator::make($request->all(), $rules);

        if ($validator->fails()) {

            $arr = ['status' => 500, 'errors' => $validator->errors()->all()];

            return response()->json($arr);
        }

        $phone = \App\Models\PhoneCode::where('phone', $request->phone)->where('code', $request->confirm_code)->first();
        if (!is_object($phone)) {


            $arr = ['status' => 500, 'errors' => ['error']];

            return response()->json($arr);
        }
        \App\Models\PhoneCode::where('phone', $request->phone)->delete();
        return response()->json(['status' => 200, 'message' => 'success']);
    }

    function send($code, $phone) {
        $link = "https://api.gateway.sa/api/v2/SendSMS?SenderId=THEPLANET&Is_Unicode=false&Message=" . 'Your%20Confirmation%20code%20is%20' . $code . "&MobileNumbers=966$phone&ApiKey=HxbGD8cKwLPnJPWYV4RJ99UXn5HyPhOKHSRZ50HXjps%3D&ClientId=d7c9f355-3cd1-4f02-9a76-be98ea548636";

        $curl = curl_init($link);
        curl_setopt($curl, CURLOPT_URL, $link);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        $headers = array(
            "accept: text/json",
        );
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
//for debug only!
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        $resp = curl_exec($curl);
        curl_close($curl);
        if (json_decode($resp)->ErrorCode == 1)
            return true;
        else
            return FALSE;
    }

    function login(Request $request) {
        $validator = Validator::make($request->all(), [
                    'phone' => 'required|string',
                    'password' => 'required|string',
                        //'remember_me' => 'boolean'
        ]);
        if ($validator->fails())
            return response()->json(['status' => 500, 'message' => 'Invalide Data', 'errors' => $validator->errors()->all()]);
        $user = User::where('email', $request->phone)->orWhere('phone', $request->phone)->first();

        if (!is_object($user) || !Hash::check($request->password, $user->password)) {
            return response()->json(['status' => 500, 'message' => 'incorrect email or password', 'errors' => ['incorrect email or password']]);
        }
        if ($request->session_id != '') {
            Cart::where('session_id', $request->session_id)->update(['user_id' => $user->id]);
        }

        $tokenResult = $user->createToken('Personal Access Token');
        $token = $tokenResult->token;
        if ($request->remember_me)
            $token->expires_at = Carbon::now()->addWeeks(1);
        $token->save();
        if ($request->session_id != '')
            \App\Models\cart::where('session_id', $request->session_id)->update(['user_id' => $user->id]);
        $response['status'] = 200;
        $response['message'] = 'success';
        $response['data'] = [
            'access_token' => $tokenResult->accessToken,
            'token_type' => 'Bearer',
            'expires_at' => Carbon::parse($tokenResult->token->expires_at)->toDateTimeString(),
            'remember' => $request->remember_me ? true : false,
            'user' => $user->toArray()
        ];
        return response()->json($response);
    }

    function resetPassword(Request $request) {
        $rules = [
            'phone' => 'required|exists:users,phone',
            'password' => 'required',
            'confirm_password' => 'required|same:password'
        ];
        $validator = \Validator::make($request->all(), $rules);

        if ($validator->fails()) {

            $arr = ['status' => 500, 'errors' => $validator->errors()->all()];

            return response()->json($arr);
        }

        $user = User::where('phone', $request->phone)->first();
        if (is_object($user)) {
            if ($request->filled('password')) {
                $user->password = Hash::make($request->password);

                $user->save();

                $arr = ['status' => 200, 'message' => 'Your Password Changes Try login Now'];
                return response()->json($arr);
            }
        } else {
            $arr = ['status' => 403, 'message' => 'This Token Not Found '];
            return response()->json($arr);
        }
    }

    function register(Request $request) {
        $rules = [
            'email' => 'required|string|email|unique:users,email',
            'password' => 'required|string',
            'confirm_password' => 'required|string|same:password',
            'phone' => 'required|numeric|unique:users,phone',
            'name' => 'required',
            'type' => 'required|integer|min:1|max:3'
                //'remember_me' => 'boolean'
        ];
        if ($request->type == 2) {
            $rules['lat'] = 'required';
            $rules['lng'] = 'required';
        }
        if ($request->type == 3) {
            $rules['car_mark'] = 'required';
            $rules['id_image'] = 'required|image';
            $rules['car_id_image'] = 'required|image';
        }
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails())
            return response()->json(['status' => 500, 'message' => 'Invalide Data', 'errors' => $validator->errors()->all()]);
        $user = new \App\Models\User;
        $user->email = $request->email;
        $user->name = $request->name;
        $user->password = Hash::make($request->password);
        $user->phone = $request->phone;
        $user->type = $request->type;
        $user->active = 0;
        $user->save();
        if ($request->type == 2) {
            $family = new \App\Models\Family();
            $family->user_id = $user->id;
            $family->lat = $request->lat;
            $family->lng = $request->lng;
            $family->address = $this->getCityFromLatLng($request->lat, $request->lng);
            $family->save();
        }
        if ($request->type == 3) {
            $delivery = new \App\Models\Delivery();
            $delivery->user_id = $user->id;
            $delivery->car_mark = $request->car_mark;
            $delivery->id_image = $this->uploadfile($request->id_image);
            $delivery->car_id_image = $this->uploadfile($request->car_id_image);
            $delivery->save();
        }
        $tokenResult = $user->createToken('Personal Access Token');
        $token = $tokenResult->token;
//        if ($request->remember_me)
        $token->expires_at = Carbon::now()->addWeeks(2);
        $token->save();

        $arr['status'] = 200;
        $arr['message'] = 'success';
        $arr['data'] = [
            'access_token' => $tokenResult->accessToken,
            'token_type' => 'Bearer',
            'expires_at' => Carbon::parse(
                    $tokenResult->token->expires_at
            )->toDateTimeString(),
            'user' => $user->toArray()
        ];
        return response()->json($arr);
    }

    function loginSocial(Request $request) {
        $validator = Validator::make($request->all(), [
                    'email' => 'required|string|email',
                    'name' => 'required|string',
        ]);
        if ($validator->fails())
            return response()->json(['status' => 500, 'message' => 'Invalide Data', 'errors' => $validator->errors()->all()]);
        $user = User::where('email', $request->email)->first();

        if (!is_object($user)) {
            $data = $request->all();

            $user = User::create($data);
        } else {

            $user->phone = $request->phone;
            $user->name = $request->name;
            $user->save();
        }

        $tokenResult = $user->createToken('Personal Access Token');
        $token = $tokenResult->token;

        $token->save();
        $arr['status'] = true;
        $arr['message'] = 'success';
        $arr['data'] = [
            'access_token' => $tokenResult->accessToken,
            'token_type' => 'Bearer',
            'expires_at' => Carbon::parse(
                    $tokenResult->token->expires_at
            )->toDateTimeString(),
            'userdata' => $user->toArray()
        ];
        return response()->json($arr);
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
//  return 5555;
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

    function generatemobie($length = 6) {
        $characters = '0123456789';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return '5555';
        return $randomString;
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
            $address = $content['results'][0]['formatted_address'];
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

        return ['address' => false, 'city' => false];
    }

}
