<?php

namespace App\Http\Resources\Reports;

use App\Http\Resources\Resource;

class CashierResource extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'createdByUserId' => $this->createdByUserId,
            'companyId' => $this->companyId,
            'name' => $this->user->name,
            'branchId' => $this->branchId,
            'userId' => $this->userId,
            'userRoleId' => $this->userRoleId,
            'level' => $this->level,
            'title' => $this->title,
            'updatedByUserId' => $this->updatedByUserId,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at,
            'orderAmount' => $this->order->sum('amount'),
            'orderReturnAmount' => $this->totalReturnAmount(),
            'netTotalAmount' => ($this->order->sum('amount') - $this->totalReturnAmount())
        ];
    }

    public function totalReturnAmount()
    {
        $orderReturnAmount = 0;
        foreach ($this->order as $order){
            foreach ($order->orderReturnAmount as $orderReturn){
                $orderReturnAmount += $orderReturn->totalReturnAmount;
            }
        }

        return $orderReturnAmount;
    }
}
