<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\IndexRequest;
use App\Http\Requests\User\StoreRequest;
use App\Http\Requests\User\UpdateRequest;
use App\Http\Resources\UserResource;
use App\Models\OtpManager;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(IndexRequest $request)
    {
        $limit          = (int) $request['per_page']     ?? 20;
        $orderBy        = $request['order_by']           ?? 'id';
        $orderDirection = $request['order_direction'] == 'asc' ? 'asc' : 'desc';

        $useOrderBy     = fn($qb) => $qb->orderBy($orderBy, $orderDirection);
        $getPaginated   = fn($qb) => $qb->paginate($limit);
        $getAll         = fn($qb) => $qb->get();

        return User::when($useOrderBy)->when($getAll, $getPaginated);
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
        $request['password'] = Hash::make('A!23456');
        $user = User::create($request->validated());

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
    public function destroy(string $id)
    {
        //
    }
}
