<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\IndexRequest;
use App\Http\Requests\User\LoginRequest;
use App\Http\Requests\User\StoreRequest;
use App\Http\Requests\User\UpdateRequest;
use App\Http\Resources\UserResource;
use App\Http\Resources\UserResourceCollection;
use App\Models\OtpManager;
use App\Models\User;
use App\Services\SmsService;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Hash;
use Mockery\Exception;
use GuzzleHttp\Client;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(IndexRequest $request)
    {
        $limit          = (int) $request['per_page']  ?? 20;
        $orderBy        = $request['order_by']           ?? 'id';
        $orderDirection = $request['order_direction'] == 'asc' ? 'asc' : 'desc';

        $useOrderBy     = function ($qb) use ($orderBy, $orderDirection) {
            return $qb->orderBy($orderBy, $orderDirection);
        };
        $getPaginated   = function ($qb) use ($limit) {
            return $qb->paginate($limit);
        };
        $getAll         = function ($qb) {
            return $qb->get();
        };

        $response = User::where('phoneVerified', true);
        $response = $response->when(isset($request['phone']), function ($query) use ($request) {
            $phone = '%' . $request['phone'];
            return $query->where('phone', 'LIKE', $phone);
        })->when($useOrderBy, $getAll, $getPaginated);

        return new UserResourceCollection($response);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     * @throws GuzzleException
     */
    public function store(StoreRequest $request)
    {
        $req = array_merge($request->validated(), ['password' => $request->password ?? Hash::make('A!23456')]);
        $user = User::create($req);

        $digits = "0123456789";
        $code = "";
        for ($i = 0; $i < 4; $i++) {
            $code .= $digits[rand(0, 9)];
        }
        $currentTime = time();
        $expireAt = date('Y-m-d H:i:s', strtotime('+1 minute', $currentTime));

        $API_URL = 'https://smsplus.sslwireless.com/api/v3/send-sms';
        $API_TOKEN = 'vyufz84x-xsfz7prj-6glud8jl-crvcwtsv-rqhmwlfh';
        $params = [
            "api_token" => $API_TOKEN,
            "sid" => 'KMARTMASKAPI',
//            "msisdn" => '01521487616',
            "msisdn" => $user->phone,
            "sms" => 'Your OTP is '. $code. ' Enter this OTP to verify your phone!',
            "csms_id" => uniqid()
        ];
        SmsService::init()->sendSMS($params);
        OtpManager::create([
            'code' => $code,
            'expireAt' => $expireAt,
            'userId' => $user->id,
        ]);

        return new UserResource($user);
    }
    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRequest $request, string $userId)
    {
        try {
            if ($userId == null){
                abort(404);
            }
            $otpData = OtpManager::where('userId', $userId)->where('code', $request->code)->first();
            if ($otpData instanceof OtpManager){
                $user = User::find($otpData->userId);
                $user->update([
                    'phoneVerified' => true
                ]);
                return new UserResource($user);
            }

            abort(404);
        }catch (\Exception $exception){
            return response()->json([
                'message' => 'Record not found.'
            ], 404);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function login(LoginRequest $request)
    {
        $user = User::where('email', $request->email)->first();

        if ($user instanceof User) {
            if (Hash::check($request->get('password'), $user->password)) {
                $token = $user->createToken('Password Grant Client')->accessToken;

                return response(['accessToken' => $token, 'user' => new UserResource($user)], 200);
            } else {
                return response(['message' => 'password mismatch error'], 422);
            }
        } else {
            return response(['message' => 'No user in this credentials'], 422);
        }
    }
}
