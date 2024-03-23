<?php

namespace App\Http\Controllers;

use App\Models\PasswordReset;
use App\Http\Requests\PasswordReset\GeneratePinRequest;
use App\Http\Requests\PasswordReset\PasswordResetRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Repositories\Contracts\PasswordResetRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

class PasswordResetController extends Controller
{
    /**
     * @var PasswordResetRepository
     */
    protected $passwordResetRepository;

    /**
     * PasswordResetController constructor.
     * @param PasswordResetRepository $passwordResetRepository
     */
    public function __construct(PasswordResetRepository $passwordResetRepository)
    {
        $this->passwordResetRepository = $passwordResetRepository;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param GeneratePinRequest $request
     * @return JsonResponse
     */
    public function generateResetPin(GeneratePinRequest $request): JsonResponse
    {
        try {

            $this->passwordResetRepository->save($request->all());

            return response()->json(['status' => 200, 'message' => 'An OTP has been sent to your email/phone'], 200);

        }catch (\Exception $exception){
            return response()->json(['status' => $exception->getCode(), 'message' => $exception->getMessage()], $exception->getCode());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param PasswordResetRequest $request
     * @return JsonResponse
     */
    public function resetPassword(PasswordResetRequest $request): JsonResponse
    {
        $passwordReset = $this->passwordResetRepository->getAValidAccessRequestWithPin($request->get('pin'), []);

        if (!$passwordReset instanceof PasswordReset) {
            return response()->json(['status' => 404, 'message' => 'Pin is invalid.'], 404);
        }

        $user = $this->passwordResetRepository->resetPassword($passwordReset->user, $request->all());

        $this->passwordResetRepository->delete($passwordReset);

        return response()->json(['status' => 201, 'message' => 'Password has been reset.', 'user' => new UserResource($user)], 201);

    }
    public function changePassword(Request $request)
    {
        $request->validate([
            'oldPassword' => 'required',
            'newPassword' => 'required',
            'newPasswordConfirmed' => ['same:newPassword'],
        ]);

        #Match The Old Password
        if(!Hash::check($request->oldPassword, Auth::user()->password)){
            return response(['message' => "Old Password Doesn't match!"], 422);
        }

        #Update the new Password
        User::whereId(Auth::user()->id)->update([
            'password' => Hash::make($request->newPassword)
        ]);

        return response(['message' => "Password successfully changed!"], 200);
    }
}
