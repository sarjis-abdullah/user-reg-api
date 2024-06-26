<?php


namespace App\Repositories;


use App\Events\OrderProduct\OrderProductCreatedEvent;
use App\Repositories\Contracts\OrderProductRepository;

class EloquentOrderProductRepository extends EloquentBaseRepository implements OrderProductRepository
{
    /**
     * @inheritDoc
     */
    public function save(array $data): \ArrayAccess
    {
        $orderProduct = parent::save($data); // TODO: Change the autogenerated stub

        $productStockSerialId = isset($data['productStockSerialId']) ? $data['productStockSerialId'] : '';

        event(new OrderProductCreatedEvent($orderProduct, $productStockSerialId));

        return $orderProduct;
    }
}
