<?php


namespace App\Repositories\Contracts;


use App\Models\PasswordReset;
use App\Models\User;

interface PasswordResetRepository extends BaseRepository
{
    /**
     * reset a user's password
     *
     * @param User $user
     * @param array $data
     * @return User
     */
    public function resetPassword(User $user, array $data): User;

    /**
     * reset a user's password
     *
     * @return String
     */
    public function generatePin(): String;

    /**
     * reset a user's password
     *
     * @param $pin
     * @param array $data
     */
    public function getAValidAccessRequestWithPin($pin, array $data);

}
