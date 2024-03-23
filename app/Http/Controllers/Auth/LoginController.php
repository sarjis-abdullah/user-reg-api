<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\UserLoginResource;
use App\Repositories\Contracts\UserRepository;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;


class LoginController extends Controller
{
    /**
     * @var UserRepository
     */
    public $userRepository;

    /**
     * LoginController constructor.
     *
     * @param UserRepository $userRepository
     */
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Login using email and password
     *
     * @param LoginRequest $request
     * @return Response
     */
    public function index(LoginRequest $request)
    {
        $emailOrPhone = $request->get('email') ? $request->get('email') : $request->get('phone');
        $user = $this->userRepository->findUserByEmailPhone($emailOrPhone);

        if ($user instanceof User) {
            if (Hash::check($request->get('password'), $user->password)) {

                if (!$user->isActive) {
                    return response(['message' => __("auth.inactive_user")], 422);
                }

                $token = $user->createToken('Password Grant Client', $user->getRolesTitles());

                //TODO: will have to add for userLogged in event

                return response(['accessToken' => $token->accessToken, 'user' => new UserLoginResource($user)], 200);
            } else {
                return response(['message' => __('auth.password_mismatch')], 422);
            }
        } else {
            return response(['message' => __('auth.no_user')], 422);
        }
    }

    /**
     * Logout a user
     *
     * @param Request $request
     * @return Application|ResponseFactory|JsonResponse|Response
     */
    public function logout(Request $request)
    {
        try {
            $user = \auth()->guard('api')->user();
            if (!$user instanceof User) {
                throw new AccessDeniedHttpException();
            }
            $user->token()->revoke();

            return response(__('Successfully logged out'), 200);
        } catch (\Exception $exception) {
            return response(['message' => __('Already logged out')], 200);
        }
    }
}
