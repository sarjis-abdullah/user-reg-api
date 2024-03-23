<?php

namespace App\Repositories;

use App\Repositories\Contracts\CouponCustomerRepository;
use App\Repositories\Contracts\CouponRepository;
use Illuminate\Support\Facades\DB;

class EloquentCouponRepository extends EloquentBaseRepository implements CouponRepository
{
    /**
     * @inheritDoc
     */
    public function save(array $data): \ArrayAccess
    {
        DB::beginTransaction();
        $coupon = parent::save($data);

        if(isset($data['groups']) || isset($data['customerIds'])) {
            $couponCustomersData = $data['groups'] ?? $data['customerIds'];
            $type = isset($data['groups']) ? 'group' : 'customerId';

            $couponCustomers = collect($couponCustomersData)->map(function ($couponCustomer) use($coupon, $type) {
                return [
                    'couponId' => $coupon->id,
                    $type => $couponCustomer,
                ];
            });

            $coupon->couponCustomers()->createMany($couponCustomers);
        }

        DB::commit();
        return $coupon;
    }

    public function update(\ArrayAccess $model, array $data): \ArrayAccess
    {
        DB::beginTransaction();
        $coupon = parent::update($model, $data);

        if(isset($data['groups']) || isset($data['customerIds'])) {
            $couponCustomersData = $data['groups'] ?? $data['customerIds'];
            $type = isset($data['groups']) ? 'group' : 'customerId';

            $couponCustomers = collect($couponCustomersData)->each(function ($couponCustomer) use($coupon, $type) {
                $coupon->couponCustomers()->withTrashed()->updateOrCreate(
                    [
                        'couponId' => $coupon->id,
                        $type => $couponCustomer
                    ],
                    [
                        'deleted_at' => NULL
                    ]
                );
            });

            app(CouponCustomerRepository::class)->getModel()
                ->where('couponId', $coupon->id)
                ->whereNotIn($type, $couponCustomers->toArray())
                ->delete();
        }

        DB::commit();
        return $coupon;
    }
}
