<?php

namespace App\Http\Requests\Order;

use App\Http\Requests\Request;
use App\Models\Branch;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Stock;
use App\Models\Tax;

class ExchangeRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        //Todo come to this and fix for big client
        return [
            'createdByUserId' => 'exists:users,id',
            'companyId' => 'exists:companies,id',
            'couponId' => 'exists:coupons,id',
            'customerId' => ['exists:customers,id', function ($attribute, $value, $fail) {
                $referenceInvoiceCustomer = Order::query()->where('id', $this->referenceOrderId)->first(['customerId']);
                if ($referenceInvoiceCustomer && $referenceInvoiceCustomer->customerId !== $value) {
                    $fail("The selected $attribute is invalid.");
                }
            }],
            'salePersonId' => 'exists:employees,id',
            'referenceId' => 'string',
            'terminal' => 'string',
            'deliveryMethod' => 'required|in:' . implode(',', Order::getConstantsByPrefix('DELIVERY_METHOD_')),
            'date' => 'required|date_format:Y-m-d',
            'status' => 'in:' . implode(',', Order::getConstantsByPrefix('STATUS_')),
            'comment' => 'nullable|string',
            'quotationId' => 'nullable|numeric',
            'roundOffAmount' => 'numeric|between:-0.99,0.99',
            'shippingCost' => 'numeric',
            'due' => 'numeric',
            'tax' => 'numeric',
            'branchId' => [
                'required',
                function ($attribute, $value, $fail) {
                    if (!Branch::where('id', $value)->whereIn('type', [Branch::TYPE_FRANCHISE, Branch::TYPE_SELF, Branch::TYPE_ECOMMERCE])->exists()) {
                        $fail("The selected $attribute is invalid.");
                    }
                }
            ],
            'amount' => [
                'required',
                'numeric',
                function ($attribute, $value, $fail) {
                    // Calculate the sum of "amount" in "orderProducts"
                    $sumOrderProductsAmount = collect($this->input('orderProducts'))->sum(function ($item) {
                        return (float) $item['amount'];
                    });

                    $sumOrderProductsReturnAmount = collect($this->input('orderProductReturns'))->sum(function ($item) {
                        return (float) $item['returnAmount'];
                    });

                    $shippingCost = $this->input('shippingCost') ?? 0;

                    // Check if the amount sums are equal
                    if(abs(((float) $sumOrderProductsAmount - (float) $sumOrderProductsReturnAmount) + (float) $shippingCost - (float) $value) > 1) {
                        $fail("The sum of amount in orderProducts must equal to order amount.");
                    }
                }
            ],
            'discount' =>[
                'numeric',
                function ($attribute, $value, $fail) {
                    // Calculate the sum of "discount" in "orderProducts"
                    $sumOrderProductsDiscountAmount = collect($this->input('orderProducts'))->sum(function ($item) {
                        return (float) $item['discount'];
                    });
                    // Check if the discount sum are equal
                    if (abs((float) $sumOrderProductsDiscountAmount - (float) $value) > 1) {
                        $fail("The sum of discount in orderProducts must equal to order discount.");
                    }
                }
            ],

            'orderProducts' =>'nullable|array',
            'orderProducts.*.productId' => 'required|exists:products,id',
            'orderProducts.*.quantity' =>'required|numeric',
            'orderProducts.*.unitPrice' => 'required|numeric',
            'orderProducts.*.discountedUnitPrice' => 'numeric',
            'orderProducts.*.discountId' => 'nullable|exists:discounts,id',
            'orderProducts.*.taxId' => 'nullable|exists:taxes,id',
            'orderProducts.*.size' => 'string',
            'orderProducts.*.color' => 'string',
            'orderProducts.*.status' => 'string',
            'orderProducts.*.stockId' => [
                'required',
                function ($attribute, $value, $fail) {
                    preg_match('/^orderProducts\.(\d+)\.stockId$/', $attribute, $matches);
                    $index = $matches[1];

                    $orderStock = $this->input('orderProducts')[$index];

                    $stock = Stock::where('id', $orderStock['stockId'])->where('quantity', '>=', $orderStock['quantity'])->first();

                    if (!$stock instanceof Stock) {
                        $fail('This product stockId is invalid.');
                    } else {
                        if($stock->unitPrice != $orderStock['unitPrice']) {
                            $fail('This product unit price is invalid.');
                        }
                    }
                }
            ],
            'orderProducts.*.tax' => [
                'numeric',
                'required_with:orderProducts.*.taxId',
                function ($attribute, $value, $fail) {
                    preg_match('/^orderProducts\.(\d+)\.tax$/', $attribute, $matches);
                    $index = $matches[1];

                    if($value > 0) {
                        $opTax = $this->input('orderProducts')[$index];

                        $tax = Tax::where('id', $opTax['taxId'])->first();

                        if(!$tax instanceof Tax) {
                            $fail('This product taxId is not valid.');
                        } else {
                            $taxAmount = ((float)$tax->amount * (float)$opTax['discountedUnitPrice'] * (float)$opTax['quantity']) / 100;

                            if(abs((float) $taxAmount - (float) $value) > 0.1) {
                                $fail('This product tax amount is not valid.');
                            }
                        }
                    }
                }
            ],
            'orderProducts.*.discount' => [
                'numeric',
                function ($attribute, $value, $fail) {
                    preg_match('/^orderProducts\.(\d+)\.discount$/', $attribute, $matches);
                    $index = $matches[1];

                    $opdAmount = $this->input('orderProducts')[$index];

                    if(abs(((float)$opdAmount['unitPrice'] * (float)$opdAmount['quantity'] - (float)$opdAmount['discountedUnitPrice'] * (float)$opdAmount['quantity']) - (float) $value) > 1) {
                        $fail('This product discount is not valid.');
                    }
                }
            ],
            'orderProducts.*.amount' => [
                'required',
                'numeric',
                function ($attribute, $value, $fail) {
                    preg_match('/^orderProducts\.(\d+)\.amount$/', $attribute, $matches);
                    $index = $matches[1];

                    $opAmount = $this->input('orderProducts')[$index];

                    if(abs( ((float) $opAmount['discountedUnitPrice'] * (float) $opAmount['quantity'] + (float) $opAmount['tax']) - (float) $value) > 1) {
                        $fail('This product amount is not valid.');
                    }
                }
            ],

            /*
             * For Returnable product
             * */

            'referenceOrderId' => 'required|numeric|exists:orders,id',
            'orderProductReturns' => 'required|array',
            'orderProductReturns.*.orderProductId' => 'required|numeric|exists:order_products,id',
            'orderProductReturns.*.quantity' => 'required|numeric',
            'orderProductReturns.*.returnAmount' => 'required|numeric',

            'payment' => "",
            'payment.amount' => 'numeric',
            'payment.changedAmount' => 'numeric|nullable',
            'payment.receivedAmount' => [
                'numeric',
                'nullable',
                function ($attribute, $value, $fail) {
                    $paymentMethod = $this->input('payment.method');
                    $inputAmount = $this->input('amount');

                    if ($paymentMethod === Payment::METHOD_CASH) {
                        // Calculate the next rounded thousand for the input amount
                        $roundedTotal = ceil($inputAmount / 1000) * 1000;

                        if ($value > $roundedTotal) {
                            $fail("The $attribute must be within the range of 0 to $roundedTotal when paying with cash.");
                        }
                    } else {
                        if ($value > $inputAmount) {
                            $fail("The $attribute must be less than or equal to the $inputAmount.");
                        }
                    }
                },
            ],
            'payment.method' => 'string|in:' . implode(',', Payment::getConstantsByPrefix('METHOD_')),
            'payment.status' => 'string|nullable|in:' . implode(',', Payment::getConstantsByPrefix('status_')),
            'payment.txnNumber' => 'string|nullable|required_if:method,' . implode(',', Payment::referenceNumberRequiredAblePaymentMethod()),
            'payment.referenceNumber' => 'string|nullable|required_if:method,' . implode(',', Payment::referenceNumberRequiredAblePaymentMethod()),

            'payments' => "array",
            'payments.*.amount' => 'numeric',
            'payments.*.changedAmount' => 'numeric|nullable',
            'payments.*.receivedAmount' => [
                'numeric',
                'nullable',
                function ($attribute, $value, $fail) {
                    $paymentMethod = $this->input('payments.*.method');
                    $inputAmount = $this->input('amount');

                    if (in_array(Payment::METHOD_CASH, $paymentMethod, true)) {
                        // Calculate the next rounded thousand for the input amount
//                        $roundedTotal = ceil($inputAmount / 1000) * 1000;
                        $roundedTotal = $inputAmount + 999;

                        if ($value > $roundedTotal) {
                            $fail("The $attribute must be within the range of 0 to $roundedTotal when paying with cash.");
                        }
                    } else {
                        if ($value > $inputAmount) {
                            $fail("The $attribute must be less than or equal to the $inputAmount.");
                        }
                    }
                },
            ],
            'payments.*.redeemedPoints' => 'required_if:method,'.Payment::METHOD_LOYALTY_REWARD.'|numeric',
            'payments.*.method' => 'string|in:' . implode(',', Payment::getConstantsByPrefix('METHOD_')),
            'payments.*.status' => 'string|nullable|in:' . implode(',', Payment::getConstantsByPrefix('status_')),
            'payments.*.txnNumber' => 'string|nullable|required_if:method,' . implode(',', Payment::referenceNumberRequiredAblePaymentMethod()),
            'payments.*.referenceNumber' => 'string|nullable|required_if:method,' . implode(',', Payment::referenceNumberRequiredAblePaymentMethod()),
        ];
    }
}
