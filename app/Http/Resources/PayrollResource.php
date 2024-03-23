<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

class PayrollResource extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'createdByUserId' => $this->createdByUserId,
            'companyId' => $this->companyId,
            'company' => $this->when($this->needToInclude($request, 'payroll.company'), function () {
                return new CompanyResource($this->company);
            }),
            'branchId' => $this->branchId,
            'branch' => $this->when($this->needToInclude($request, 'payroll.branch'), function () {
                return new BranchResource($this->branch);
            }),
            'employeeId' => $this->employeeId,
            'employee' => $this->when($this->needToInclude($request, 'payroll.employee'), function () {
                return new EmployeeResource($this->employee);
            }),
            'date' => $this->date,
            'account' => $this->account,
            'amount' => $this->amount,
            'method' => $this->method,
            'reference' => $this->reference,
            'updatedByUserId' => $this->updatedByUserId,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
