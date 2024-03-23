<?php

namespace App\Http\Controllers;

use App\Http\Requests\Customer\DuePayRequest;
use App\Http\Requests\Customer\IndexRequest;
use App\Http\Requests\Customer\StoreRequest;
use App\Http\Requests\Customer\UpdateRequest;
use App\Http\Resources\CustomerResource;
use App\Models\Customer;
use App\Repositories\Contracts\CustomerRepository;
use App\Services\Helpers\PdfHelper;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Arr;


class CustomerController extends Controller
{
    /**
     * @var CustomerRepository
     */
    protected $customerRepository;

    /**
     * CustomerController constructor.
     * @param CustomerRepository $customerRepository
     */
    public function __construct(CustomerRepository $customerRepository)
    {
        $this->customerRepository = $customerRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @param IndexRequest $request
     * @return AnonymousResourceCollection
     */
    public function index(IndexRequest $request): AnonymousResourceCollection
    {
        $customer = $this->customerRepository->findBy($request->all());

        $customerResources =  CustomerResource::collection($customer['customers']);

        $customerResources->additional(Arr::except($customer, ['customers']));

        return $customerResources;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreRequest $request
     * @return CustomerResource
     */
    public function store(StoreRequest $request)
    {
        if(!isset($request['type']) || (isset($request['type']) && $request['type'] == '')) {
            $request['type'] = "walk-in";
        }

        $customer = $this->customerRepository->save($request->all());

        return new CustomerResource($customer);
    }

    /**
     * Display the specified resource.
     *
     * @param Customer $customer
     * @return CustomerResource
     */
    public function show(Customer $customer)
    {
        return new CustomerResource($customer);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateRequest $request
     * @param Customer $customer
     * @return CustomerResource
     */
    public function update(UpdateRequest $request, Customer $customer)
    {
        $customer = $this->customerRepository->update($customer, $request->all());

        return new CustomerResource($customer);
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param Customer $customer
     * @return JsonResponse
     */
    public function destroy(Customer $customer)
    {
        $this->customerRepository->delete($customer);

        return response()->json(null, 204);
    }


    /**
     * @param DuePayRequest $request
     * @return JsonResponse
     */
    public function customerDuePay(DuePayRequest $request): JsonResponse
    {
        $this->customerRepository->payCustomerDue($request->all());

        return response()->json(null, 200);
    }


    public function customerReportPdf(IndexRequest $request)
    {
        $customer = $this->customerRepository->findBy($request->all());

//        return $customer['customers'];
        return PdfHelper::downloadPdf($customer['customers'], 'pdf.reports.customerReport', 'Customer-report.pdf');
    }
}
