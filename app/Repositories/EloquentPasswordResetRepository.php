<?php


namespace App\Repositories;


use App\Models\PasswordReset;
use App\Models\User;
use App\Events\PasswordReset\PasswordResetEvent;
use App\Repositories\Contracts\PasswordResetRepository;
use App\Repositories\Contracts\UserRepository;
use Carbon\Carbon;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Facades\DB;

class EloquentPasswordResetRepository extends EloquentBaseRepository implements PasswordResetRepository
{
    /**
     * @inheritDoc
     */
    public function save(array $data): \ArrayAccess
    {
        DB::beginTransaction();

        $data['pin'] = $this->generatePin();
        $data['validTill'] = Carbon::now()->addMinutes(2);

        if (isset($data['emailOrPhone']))  {
            if (strpos($data['emailOrPhone'], '@') !== false) {
                $data['email'] = $data['emailOrPhone'];
            } else {
                $data['phone'] = $data['emailOrPhone'];
            }

            $userRepository = app(UserRepository::class);
            $user = $userRepository->findActiveUserByEmailPhone($data['emailOrPhone']);

            if (!$user){
                throw new \Exception('This user is disabled, Please contact with authority.', 401);
            }

            $data['userId'] = $user->id;

            unset($data['emailOrPhone']);

            $passwordReset = $this->patch(['userId' => $user->id],$data);
        } else {
            $data['type'] = PasswordReset::TYPE_SET_PASSWORD_BY_PIN;
            $passwordReset = parent::save($data);
        }

        event(new PasswordResetEvent($passwordReset, []));

        DB::commit();

        return $passwordReset;
    }

    /**
     * @inheritDoc
     */
    public function resetPassword(User $user, array $data): User
    {
        DB::beginTransaction();

        $userRepository = app(UserRepository::class);
        $emailOrPhone = $user->phone ?? $user->email;
        $user = $userRepository->findUserByEmailPhone($emailOrPhone);

        $userRepository->updateUser($user, ['password' => $data['password'], 'isActive' => true]);

        DB::commit();

        return $user;
    }

    /**
     * @inheritDoc
     */
    public function generatePin(): string
    {
        $isUniquePin = true;
        $pin = '';
        while ($isUniquePin) {
            $pin = mt_rand(10000,99999);

            if (!$this->getAValidAccessRequestWithPin($pin) instanceof ResetPassword) {
                $isUniquePin = false;
            }
        }
        return $pin;
    }

    /**
     * @inheritDoc
     */
    public function getAValidAccessRequestWithPin($pin, array $searchCriteria = [])
    {
        $validTime =  Carbon::now()->subMinutes(2)->toDateTimeString();
        $queryBuilder = $this->model
            ->where('pin', $pin)
            ->where('validTill', '>=', $validTime);
        return $queryBuilder->first();
    }


}
