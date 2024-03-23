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
use Illuminate\Support\Facades\Hash;
use Mockery\Exception;

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

        $useOrderBy     = fn($qb) => $qb->orderBy($orderBy, $orderDirection);
        $getPaginated   = fn($qb) => $qb->paginate($limit);
        $getAll         = fn($qb) => $qb->get();

        return new UserResourceCollection(User::where('phoneVerified', true)->when($useOrderBy, $getAll, $getPaginated));
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
     */
    public function store(StoreRequest $request)
    {
        dd(12);
        $user = User::create([...$request->validated(), 'password' => Hash::make('A!23456')]);

        $digits = "0123456789";
        $code = "";
        for ($i = 0; $i < 4; $i++) {
            $code .= $digits[rand(0, 9)];
        }
        $currentTime = time();
        $expireAt = date('Y-m-d H:i:s', strtotime('+1 minute', $currentTime));

        OtpManager::create([
            'code' => 1234 ?? $code,
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
        $user = User::where('phone', $request->phone)->first();

        if ($user instanceof User) {
            if (Hash::check($request->get('password'), $user->password)) {
                $token = $user->createToken('Password Grant Client')->accessToken;

                return response(['accessToken' => $token, 'user' => new UserResource($user)], 200);
            } else {
                return response(['message' => __('auth.password_mismatch')], 422);
            }
        } else {
            return response(['message' => __('auth.no_user')], 422);
        }
    }
}
