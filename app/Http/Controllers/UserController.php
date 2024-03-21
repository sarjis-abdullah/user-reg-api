<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\IndexRequest;
use App\Http\Requests\User\StoreRequest;
use App\Http\Resources\UserResource;
use App\Models\OtpManager;
use App\Models\User;
use Illuminate\Http\Request;
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
        $orderDirection = $request['order_direction']    ?? 'desc';

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
            'code' => $code,
            'expireAt' => $expireAt,
            'userId' => $user->id,
        ]);

        return new UserResource($user);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
