<?php


namespace App\Repositories\Contracts;


interface OrderRepository extends BaseRepository
{
    public function saveExchange(array $data): \ArrayAccess;
}
