<?php

namespace App\Providers;

use App\Models\Admin;
use App\Models\AppSetting;
use App\Models\Attachment;
use App\Models\Brand;
use App\Models\CashUp;
use App\Models\Category;
use App\Models\Company;
use App\Models\Branch;
use App\Models\Customer;
use App\Models\DeliveryAgency;
use App\Models\EcomIntegration;
use App\Models\Employee;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\Manager;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\Product;
use App\Models\Role;
use App\Models\Stock;
use App\Models\StockLog;
use App\Models\StockTransfer;
use App\Models\SubCategory;
use App\Models\Supplier;
use App\Models\User;
use App\Models\UserProfile;
use App\Models\UserRole;
use App\Policies\AdminPolicy;
use App\Policies\AppSettingPolicy;
use App\Policies\AttachmentPolicy;
use App\Policies\BrandPolicy;
use App\Policies\CashUpPolicy;
use App\Policies\CategoryPolicy;
use App\Policies\CompanyPolicy;
use App\Policies\BranchPolicy;
use App\Policies\CustomerPolicy;
use App\Policies\DeliveryAgencyPolicy;
use App\Policies\EcomIntegrationPolicy;
use App\Policies\EmployeePolicy;
use App\Policies\ExpenseCategoryPolicy;
use App\Policies\ExpensePolicy;
use App\Policies\ManagerPolicy;
use App\Policies\OrderPolicy;
use App\Policies\OrderProductPolicy;
use App\Policies\ProductPolicy;
use App\Policies\RolePolicy;
use App\Policies\StockLogPolicy;
use App\Policies\StockPolicy;
use App\Policies\StockTransferPolicy;
use App\Policies\SubCategoryPolicy;
use App\Policies\SupplierPolicy;
use App\Policies\UserPolicy;
use App\Policies\UserProfilePolicy;
use App\Policies\UserRolePolicy;
use App\Services\Helpers\ScopesHelper;
use Ejarnutowski\LaravelApiKey\Models\ApiKey;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Gate;
use Laravel\Passport\Passport;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        Role::class => RolePolicy::class,
        User::class => UserPolicy::class,
        UserRole::class => UserRolePolicy::class,
        UserProfile::class => UserProfilePolicy::class,

        Attachment::class => AttachmentPolicy::class,
        AppSetting::class => AppSettingPolicy::class,

        Admin::class => AdminPolicy::class,

        CashUp::class => CashUpPolicy::class,

        Company::class => CompanyPolicy::class,
        Branch::class => BranchPolicy::class,

        Manager::class => ManagerPolicy::class,
        Employee::class => EmployeePolicy::class,
        Customer::class => CustomerPolicy::class,
        Supplier::class => SupplierPolicy::class,

        Category::class => CategoryPolicy::class,
        SubCategory::class => SubCategoryPolicy::class,
        Brand::class => BrandPolicy::class,
        Product::class => ProductPolicy::class,
        Stock::class => StockPolicy::class,
        StockLog::class => StockLogPolicy::class,
        DeliveryAgency::class => DeliveryAgencyPolicy::class,
        StockTransfer::class => StockTransferPolicy::class,

        Order::class => OrderPolicy::class,
        OrderProduct::class => OrderProductPolicy::class,

        ExpenseCategory::class => ExpenseCategoryPolicy::class,
        Expense::class => ExpensePolicy::class,

        ApiKey::class => AdminPolicy::class,
        EcomIntegration::class => EcomIntegrationPolicy::class
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        if (!App::runningInConsole()) {
            Passport::tokensCan(ScopesHelper::allScopes());
        }

        Passport::routes(null, ['prefix' => 'api/v1/oauth']);
        Passport::tokensExpireIn(now()->addDay());
        Passport::refreshTokensExpireIn(now()->addDay());
        Passport::personalAccessTokensExpireIn(now()->addMonths(6));
    }
}
