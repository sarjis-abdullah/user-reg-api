<?php

namespace App\Policies;

use App\Models\DeliveryAgency;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class DeliveryAgencyPolicy
{
    use HandlesAuthorization;

    /**
     * Intercept checks
     *
     * @param User $currentUser
     * @return bool
     */
    public function before(User $currentUser)
    {
        if ($currentUser->isSuperAdmin()) {
            return true;
        }
    }

    /**
     * Determine if a given user has permission to list
     *
     * @param User $currentUser
     * @return bool
     */
    public function list(User $currentUser): bool
    {
        if ($currentUser->isStandardAdmin()) {
            return true;
        }

        return false;
    }

    /**
     * Determine if a given user has permission to store
     *
     * @param User $currentUser
     * @param string $level
     * @return bool
     */
    public function store(User $currentUser): bool
    {
        if ($currentUser->isStandardAdmin()) {
            return true;
        }

        return false;
    }

    /**
     * Determine if a given user has permission to show
     *
     * @param User $currentUser
     * @param DeliveryAgency $deliveryAgency
     * @return bool
     */
    public function show(User $currentUser,  DeliveryAgency $deliveryAgency): bool
    {
        if ($currentUser->isStandardAdmin()) {
            return true;
        }

        return false;
    }

    /**
     * Determine if a given user can update
     *
     * @param User $currentUser
     * @param DeliveryAgency $deliveryAgency
     * @return bool
     */
    public function update(User $currentUser, DeliveryAgency $deliveryAgency): bool
    {
        if ($currentUser->isStandardAdmin()) {
            return true;
        }

        return false;
    }

    /**
     * Determine if a given user can delete
     *
     * @param User $currentUser
     * @param DeliveryAgency $deliveryAgency
     * @return bool
     */
    public function destroy(User $currentUser, DeliveryAgency $deliveryAgency): bool
    {
        return false;
    }
}
