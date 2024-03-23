<?php

use App\Http\Controllers\ApiKeyController;
use App\Http\Controllers\DBQueryUpdateController;
use App\Http\Controllers\GenericExportController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\WC\WCApiController;
use App\Http\Controllers\Woocommerce\WoocommerceResourceController;
use App\Http\Controllers\Woocommerce\WooCommerceWebHookController;
use App\Services\Ecommerce\WoocomCommunicationService\WoocomCommunicationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use const App\Http\Controllers\WoocommerceResourceController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('/', function (Request $request) {
    return ['message' => "Api's working fine."];
});

Route::group(['prefix' => 'api/v1'], function () {
    Route::get('/', function (Request $request) {
        return ['message' => "Api's working fine."];
    });
    Route::group(['middleware' => ['auth:api']], function () {
        /**
         * related to attachments
         */
        Route::apiResource('attachment', 'AttachmentController');

        /**
         * related to user features
         */
        Route::apiResource('role', 'RoleController', ['except' => ['destroy']]);
        Route::apiResource('admin', 'AdminController', ['except' => ['destroy']]);
        Route::apiResource('user', 'UserController', ['except' => ['destroy']]);
        Route::apiResource('user-role', 'UserRoleController', ['except' => ['destroy']]);
        Route::apiResource('user-profile', 'UserProfileController', ['except' => ['destroy']]);
        Route::apiResource('payroll', 'PayrollController');


        # Notifications
        Route::get('notifications', [NotificationController::class, 'index']);
        Route::put('notifications/all', [NotificationController::class, 'updateAll']);
        Route::put('notifications/{notification}', [NotificationController::class, 'update']);

        /**
         * related to company
         */
        Route::apiResource('company', 'CompanyController');
        Route::apiResource('branch', 'BranchController');
        Route::apiResource('manager', 'ManagerController');
        Route::apiResource('employee', 'EmployeeController');
        Route::post('employee-assign-to-manager', 'EmployeeController@employeeAssignToManager');
        Route::apiResource('customer', 'CustomerController');
        Route::apiResource('supplier', 'SupplierController');
        Route::post('customer-due-pay', 'CustomerController@customerDuePay');
        Route::post('supplier-due-pay', 'SupplierController@supplierDuePay');

        Route::get('customer-report-pdf', 'CustomerController@customerReportPdf');
        Route::get('supplier-report-pdf', 'SupplierController@supplierReportPdf');

        /**
         * Department and Sub department route
         */
        Route::apiResource('department', 'DepartmentController');
        Route::apiResource('sub-department', 'SubDepartmentController');

        /**
         * related to app settings
         */
        Route::post('app-setting', 'AppSettingController@setSetting');
        Route::put('app-setting', 'AppSettingController@setSetting');
        Route::get('app-setting', 'AppSettingController@index');
        Route::get('app-setting/{appSetting}', 'AppSettingController@show');

        /**
         * related to income & expense
         */
        Route::apiResource('expense-category', 'ExpenseCategoryController');
        Route::apiResource('expense', 'ExpenseController');
        Route::apiResource('cash-up', 'CashUpController');
        Route::apiResource('income', 'IncomeController');
        Route::apiResource('income-category', 'IncomeCategoryController');

        /*
         * Expense report pdf
         */

        Route::get('expense-pdf', 'ExpenseController@expensePdf');

        /**
         * related to brand and category
         */
        Route::apiResource('brand', 'BrandController');
        Route::apiResource('unit', 'UnitController');
        Route::apiResource('category', 'CategoryController');
        Route::apiResource('sub-category', 'SubCategoryController');
        Route::apiResource('product', 'ProductController');
        Route::post('products-csv-upload', 'ProductController@batchUploadCsvFile');
        Route::get('archive-product', 'ProductController@getArchiveData');
        Route::get('generate-product-barcode', 'ProductController@generateProductBarcodeNumber');
        Route::apiResource('stock', 'StockController');
        Route::put('stock/{stock}/combo-product', 'StockController@updateComboProduct');

        Route::get('archive-stock', 'StockController@getArchiveData');
        Route::apiResource('stock-log', 'StockLogController', ['except' => ['destroy']]);
        Route::apiResource('stock-transfer', 'StockTransferController', ['except' => ['destroy']]);
        Route::get('stock-transfer-pdf', 'StockTransferController@stockTransferPdf');
        Route::get('stock-transfer-excel', 'StockTransferController@stockTransferExcel');

        Route::apiResource('delivery-agency', 'DeliveryAgencyController');
        Route::apiResource('adjustment', 'AdjustmentController', ['except' => ['destroy']]);
        Route::get('adjustment-pdf-download', 'AdjustmentController@downloadAdjustmentPdf');

        Route::get('product-group-by-stock', 'StockController@getProductGroupByStock');
        Route::get('product-by-expiration-date', 'ProductController@getProductByExpirationDate');

        Route::get('archive-product-and-product-with-archive-stocks', 'ProductController@getArchivedProductAndProductWithArchiveStocks');

        Route::get('product-pdf-download', 'ProductController@downloadProductPdf');
        Route::get('product-stock-pdf', 'ProductController@productStockPdf');
        Route::get('product-stock-excel', 'ProductController@productStockExcel');

        //Product stock api
        Route::get('product-stocks', 'ProductController@productStock');

        Route::post('product-restore/{id}', 'ProductController@restore');
        /**
         * related to payments
         */
        Route::apiResource('payment', 'PaymentController', ['except' => ['destroy']]);
        Route::get('payment-summary', 'PaymentController@paymentSummary');
        Route::get('payment-summary-pdf', 'PaymentController@paymentSummaryPdf');
        Route::get('payment-summary-excel', 'PaymentController@paymentSummaryExcel');

        /**
         * related to order
         */
        Route::post('order', 'OrderController@store');
        Route::put('order/{orderId}/change-status', 'OrderController@changeStatus');
        Route::put('order/{orderId}/change-order-status', 'OrderController@changeOrderStatus');
        Route::apiResource('order', 'OrderController', ['except' => ['store', 'destroy', 'update']]);
        Route::apiResource('order-product', 'OrderProductController', ['except' => ['store', 'update', 'destroy']]);
        Route::apiResource('order-product-return', 'OrderProductReturnController', ['except' => ['update', 'destroy']]);
        Route::get('order-product-return-group-by-date', 'OrderController@getOrderReturnProducts');
        Route::get('order-product-revert-wrong-stock-sale/{id}', 'OrderProductController@revertWrongStockSale');
        Route::get('order/{order}/logs', 'OrderLogController@index');

        Route::get('order-report-pdf', 'OrderController@orderReportPdf');
        Route::get('order-product-return-group-by-date-pdf', 'OrderController@getOrderReturnProductsPdf');
        Route::get('order-invoice-pdf/{order}', 'OrderController@orderInvoicePdf')->name('order-invoice-pdf');
        Route::get('preview-order-invoice-pdf/{id}', 'OrderController@previewOrderInvoicePdf');

        /*
         * Exchange apis
         * */

        Route::post('order-exchange', 'OrderController@orderExchange');

        Route::apiResource('coupon', 'CouponController', ['except' => 'delete']);
        Route::get('coupon-validation', 'CouponController@couponValidation');
        Route::apiResource('coupon-customer', 'CouponCustomerController', ['except' => 'update', 'store']);

        Route::get('order-export', 'OrderController@orderExcelExport');
        Route::get('sales-person-wise-orders', 'OrderController@salesPersonOrder');
        Route::get('sales-person-wise-orders-pdf', 'OrderController@salesPersonOrderPDF');
        Route::get('sales-person-wise-orders-excel', 'OrderController@salesPersonOrderExcel');

        /**
         * related to quotation
         */
        Route::apiResource('quotation', 'QuotationController');

        /**
         * related to purchase
         */
        Route::apiResource('purchase', 'PurchaseController', ['except' => ['update', 'destroy']]);
        Route::put('purchase/{purchase}/status-update', 'PurchaseController@statusUpdate');
        Route::apiResource('purchase-product', 'PurchaseProductController',  ['except' => ['store', 'update', 'destroy']]);
        Route::apiResource('purchase-product-return', 'PurchaseProductReturnController', ['except' => ['update', 'destroy']]);
        Route::get('purchase-product-return-group-by-date', 'PurchaseController@getPurchaseReturnProducts');

        Route::get('purchase-report-pdf', 'PurchaseController@purchaseReportPdf');
        Route::get('purchase-report-excel', 'PurchaseController@purchaseReportExcel');
        Route::get('purchase-product-return-group-by-date-pdf', 'PurchaseController@getPurchaseReturnProductsPdf');

        /*
         * related to product stock serial
         * */
        Route::apiResource('product-stock-serial', 'ProductStockSerialController', ['except' => ['store', 'update', 'destroy']]);

        Route::post('product-stock-merge', 'StockController@mergeSameSkuStock');
        /**
         * related to module and permissions
         */
        Route::apiResource('module', 'ModuleController');
        Route::apiResource('module-action', 'ModuleActionController');
        Route::apiResource('company-module', 'CompanyModuleController');
        Route::apiResource('user-role-module-permission', 'UserRoleModulePermissionController');

        /**
         * related to tax & discount
         */
        Route::apiResource('tax', 'TaxController');
        Route::apiResource('discount', 'DiscountController');

        Route::post('change-password', 'PasswordResetController@changePassword');

        /**
         * related to reports
         */
        Route::get('reporting-count-of-model-instance',  'Reports\\DashboardController@getStateOfResource');
        Route::get('date-wise-sale-reports',  'Reports\\ProfitController@getDateWiseSaleProfit');
        Route::get('product-wise-sale-reports',  'Reports\\ProfitController@getProductWiseSaleProfit');
        Route::get('category-wise-sale-reports', 'Reports\\ProfitController@getCategoryWiseSaleReport');
        Route::get('supplier-wise-purchase-reports', 'Reports\\ProfitController@getSupplierWisePurchaseReport');
        Route::get('supplier-wise-stock-reports', 'Reports\\ProfitController@getSupplierWiseStockReport');
        Route::get('sale-chart-count', 'Reports\\DashboardController@getSaleChartCount');
        Route::get('daily-summary-report', 'Reports\\DashboardController@getDailySummaryReport');

        Route::get('date-wise-sale-reports-pdf', 'Reports\\ProfitController@getDateWiseSaleProfitPdf');
        Route::get('date-wise-sale-reports-excel', 'Reports\\ProfitController@getDateWiseSaleProfitExcel');
        Route::get('product-wise-sale-reports-pdf', 'Reports\\ProfitController@getProductWiseSaleProfitPdf');
        Route::get('product-wise-sale-reports-excel', 'Reports\\ProfitController@getProductWiseSaleProfitExcel');
        Route::get('category-wise-sale-reports-pdf', 'Reports\\ProfitController@getCategoryWiseSaleReportPdf');
        Route::get('category-wise-sale-reports-excel', 'Reports\\ProfitController@getCategoryWiseSaleReportExcel');
        Route::get('supplier-wise-purchase-reports-pdf', 'Reports\\ProfitController@getSupplierWisePurchaseReportPdf');
        Route::get('supplier-wise-purchase-reports-excel', 'Reports\\ProfitController@getSupplierWisePurchaseReportExcel');

        Route::get('supplier-wise-stock-reports-pdf', 'Reports\\ProfitController@getSupplierWiseStockReportPdf');

        Route::get('sales-wise-vat-report', 'Reports\\ProfitController@salesWiseVatReport');
        Route::get('product-wise-vat-report', 'Reports\\ProfitController@productWiseVatReport');

        Route::get('sales-wise-vat-report-pdf', 'Reports\\ProfitController@salesWiseVatReportPdf');
        Route::get('sales-wise-vat-report-excel', 'Reports\\ProfitController@salesWiseVatReportExcel');


        Route::get('sales-person-report', 'Reports\\ProfitController@salesPersonReport');
        Route::get('sales-person-report-pdf', 'Reports\\ProfitController@salesPersonReportPdf');
        Route::get('sales-person-report-excel', 'Reports\\ProfitController@salesPersonReportExcel');

        Route::get('cashier-report', 'Reports\\ProfitController@cashierReport');
        Route::get('cashier-report-pdf', 'Reports\\ProfitController@cashierReportPdf');
        Route::get('cashier-report-excel', 'Reports\\ProfitController@cashierReportExcel');


        /**
         * related to export pdf/csv
         */
        Route::prefix('export-to/{as}')->group(function () {
            Route::get('adjustment-list', [GenericExportController::class, 'adjustmentList'])->where('as', 'pdf|csv');
            Route::get('product-list', [GenericExportController::class, 'productList'])->where('as', 'pdf|csv');
            Route::get('purchase-list', [GenericExportController::class, 'purchaseList'])->where('as', 'pdf|csv');
            Route::get('order-list', [GenericExportController::class, 'orderList'])->where('as', 'pdf|csv');
            Route::get('stock-report', [GenericExportController::class, 'stockReport'])->where('as', 'pdf|csv');
        });

        //TODO: remove this
        Route::get('/fix-vat', function () {
            Artisan::call('order:fix-tax');
            return response()->json(["message" => "Order Tax Fix Command Executed"]);
        });

        #related to api-keys
        Route::get('api-keys', [ApiKeyController::class, 'index']);
        Route::post('api-keys', [ApiKeyController::class, 'store']);
        Route::get('api-keys/{apiKey}', [ApiKeyController::class, 'show']);
        Route::put('api-keys/{apiKey}', [ApiKeyController::class, 'update']);
        Route::delete('api-keys/{apiKey}', [ApiKeyController::class, 'destroy']);

        # related to ecom integration (manage woocommerce api credentials) -@TODO Add Gate only for admin
        Route::apiResource('ecom-integration', 'EcomIntegrationController', ['except' => ['destroy']]);

        # related to woocommerce resource
        Route::prefix('woocommerce')->group(function() {
            Route::get('test', function (Request $request) {
                return app()->make(WoocomCommunicationService::class)->show('products/', $request->get('id'));
            });
            Route::get('webhooks', function () {
                return app()->make(WoocomCommunicationService::class)->index('webhooks');
            });
            Route::post('webhooks', function (Request $request) {
                return app()->make(WoocomCommunicationService::class)->store('webhooks', $request->all());
            });
            Route::put('webhooks/{id}', function (Request $request, $id) {
                return app()->make(WoocomCommunicationService::class)->update("webhooks/$id", $request->all());
            });
            Route::delete('webhooks/{id}', function (Request $request, $id) {
                return app()->make(WoocomCommunicationService::class)->update("webhooks/$id", $request->all());
            });

            Route::get('upload-categories', [WoocommerceResourceController::class, 'uploadSystemCategoriesToWC']);
            Route::get('upload-sub-categories', [WoocommerceResourceController::class, 'uploadSystemSubCategoriesToWC']);
            Route::get('upload-brands', [WoocommerceResourceController::class, 'uploadSystemBrandsToWC']);
            Route::get('upload-taxes', [WoocommerceResourceController::class, 'uploadSystemTaxesToWC']);
            Route::get('upload-products', [WoocommerceResourceController::class, 'uploadSystemProductsToWC']);
        });

        /*
         * This api for developer, Please use this api very careful.
         *
         * */

        Route::post('/run-query', [DBQueryUpdateController::class, 'executeQuery']);
        Route::post('/remove-duplicate-category', [DBQueryUpdateController::class, 'removeDuplicateCategory']);
        Route::post('/update-order-return-product-id', [DBQueryUpdateController::class, 'updateOrderReturnProductId']);

    });

    #Auth
    Route::post('login', 'Auth\\LoginController@index');
    Route::get('logout', 'Auth\\LoginController@logout');
    Route::post('generate-pin', 'PasswordResetController@generateResetPin');
    Route::post('password-reset', 'PasswordResetController@resetPassword');

    Route::get('get-pdf', 'Reports\\DashboardController@viewPdf');

});

# this route groups is for woocommerce api and webhooks
Route::group(['prefix' => 'wc-api'], function () {
    Route::post('order-created', [WooCommerceWebHookController::class, 'store']);
    Route::post('order-updated', [WooCommerceWebHookController::class, 'update']);
});

# this route groups is for woocommerce api and webhooks
Route::group(['prefix' => 'wc-api', 'middleware' => 'auth.apikey'], function () {
    /*
     * Products apis
     */
    Route::get('products', [WCApiController::class, 'products']);
    Route::get('products/{id}', [WCApiController::class, 'product']);

    /*
     * Order apis
     */
    Route::get('orders', [WCApiController::class, 'orders']);
    Route::get('orders/{id}', [WCApiController::class, 'order']);

    /*
     * Stock Update
     * */
    Route::put('stocks', [WCApiController::class, 'stock']);

});
