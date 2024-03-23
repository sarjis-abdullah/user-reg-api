<?php


namespace App\Providers;


use App\Models\Adjustment;
use App\Models\Admin;
use App\Models\AppSetting;
use App\Models\Attachment;
use App\Models\Brand;
use App\Models\CashUp;
use App\Models\Category;
use App\Models\Company;
use App\Models\Branch;
use App\Models\CompanyModule;
use App\Models\Coupon;
use App\Models\CouponCustomer;
use App\Models\Customer;
use App\Models\CustomerLoyaltyReward;
use App\Models\Delivery;
use App\Models\DeliveryAgency;
use App\Models\Department;
use App\Models\Discount;
use App\Models\EcomIntegration;
use App\Models\Employee;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\Income;
use App\Models\IncomeCategory;
use App\Models\Manager;
use App\Models\Module;
use App\Models\ModuleAction;
use App\Models\Notification;
use App\Models\Order;
use App\Models\OrderLog;
use App\Models\OrderProduct;
use App\Models\OrderProductReturn;
use App\Models\PasswordReset;
use App\Models\Payment;
use App\Models\Product;
use App\Models\ProductStockSerial;
use App\Models\PurchaseProductReturn;
use App\Models\Quotation;
use App\Models\Role;
use App\Models\Stock;
use App\Models\StockLog;
use App\Models\StockTransferProduct;
use App\Models\SubCategory;
use App\Models\SubDepartment;
use App\Models\Supplier;
use App\Models\Tax;
use App\Models\User;
use App\Models\UserProfile;
use App\Models\UserRole;
use App\Models\Payroll;
use App\Models\Purchase;
use App\Models\PurchaseProduct;
use App\Models\Unit;
use App\Models\StockTransfer;
use App\Models\UserRoleModulePermission;
use App\Repositories\Contracts\AdjustmentRepository;
use App\Repositories\Contracts\AdminRepository;
use App\Repositories\Contracts\AppSettingRepository;
use App\Repositories\Contracts\AttachmentRepository;
use App\Repositories\Contracts\BrandRepository;
use App\Repositories\Contracts\CashUpRepository;
use App\Repositories\Contracts\CategoryRepository;
use App\Repositories\Contracts\CompanyModuleRepository;
use App\Repositories\Contracts\CompanyRepository;
use App\Repositories\Contracts\BranchRepository;
use App\Repositories\Contracts\CouponCustomerRepository;
use App\Repositories\Contracts\CouponRepository;
use App\Repositories\Contracts\CustomerLoyaltyRewardRepository;
use App\Repositories\Contracts\CustomerRepository;
use App\Repositories\Contracts\DeliveryAgencyRepository;
use App\Repositories\Contracts\DeliveryRepository;
use App\Repositories\Contracts\DepartmentRepository;
use App\Repositories\Contracts\DiscountRepository;
use App\Repositories\Contracts\EcomIntegrationRepository;
use App\Repositories\Contracts\EmployeeRepository;
use App\Repositories\Contracts\ExpenseCategoryRepository;
use App\Repositories\Contracts\ExpenseRepository;
use App\Repositories\Contracts\IncomeCategoryRepository;
use App\Repositories\Contracts\IncomeRepository;
use App\Repositories\Contracts\ManagerRepository;
use App\Repositories\Contracts\ModuleActionRepository;
use App\Repositories\Contracts\ModuleRepository;
use App\Repositories\Contracts\NotificationRepository;
use App\Repositories\Contracts\OrderLogRepository;
use App\Repositories\Contracts\OrderProductRepository;
use App\Repositories\Contracts\OrderProductReturnRepository;
use App\Repositories\Contracts\OrderRepository;
use App\Repositories\Contracts\PasswordResetRepository;
use App\Repositories\Contracts\PaymentRepository;
use App\Repositories\Contracts\ProductRepository;
use App\Repositories\Contracts\ProductStockSerialRepository;
use App\Repositories\Contracts\PurchaseProductReturnRepository;
use App\Repositories\Contracts\QuotationRepository;
use App\Repositories\Contracts\RoleRepository;
use App\Repositories\Contracts\StockLogRepository;
use App\Repositories\Contracts\StockRepository;
use App\Repositories\Contracts\StockTransferProductRepository;
use App\Repositories\Contracts\SubCategoryRepository;
use App\Repositories\Contracts\SubDepartmentRepository;
use App\Repositories\Contracts\SupplierRepository;
use App\Repositories\Contracts\TaxRepository;
use App\Repositories\Contracts\UserProfileRepository;
use App\Repositories\Contracts\UserRepository;
use App\Repositories\Contracts\UserRoleModulePermissionRepository;
use App\Repositories\Contracts\UserRoleRepository;
use App\Repositories\Contracts\PayrollRepository;
use App\Repositories\Contracts\PurchaseRepository;
use App\Repositories\Contracts\PurchaseProductRepository;
use App\Repositories\Contracts\UnitRepository;
use App\Repositories\Contracts\StockTransferRepository;
use App\Repositories\EloquentAdjustmentRepository;
use App\Repositories\EloquentAdminRepository;
use App\Repositories\EloquentAttachmentRepository;
use App\Repositories\EloquentBrandRepository;
use App\Repositories\EloquentCashUpRepository;
use App\Repositories\EloquentCategoryRepository;
use App\Repositories\EloquentCompanyModuleRepository;
use App\Repositories\EloquentCompanyRepository;
use App\Repositories\EloquentBranchRepository;
use App\Repositories\EloquentCouponCustomerRepository;
use App\Repositories\EloquentCouponRepository;
use App\Repositories\EloquentCustomerLoyaltyRewardRepository;
use App\Repositories\EloquentCustomerRepository;
use App\Repositories\EloquentDeliveryAgencyRepository;
use App\Repositories\EloquentDeliveryRepository;
use App\Repositories\EloquentDepartmentRepository;
use App\Repositories\EloquentDiscountRepository;
use App\Repositories\EloquentEcomIntegrationRepository;
use App\Repositories\EloquentEmployeeRepository;
use App\Repositories\EloquentExpenseCategoryRepository;
use App\Repositories\EloquentExpenseRepository;
use App\Repositories\EloquentIncomeCategoryRepository;
use App\Repositories\EloquentIncomeRepository;
use App\Repositories\EloquentManagerRepository;
use App\Repositories\EloquentModuleActionRepository;
use App\Repositories\EloquentModuleRepository;
use App\Repositories\EloquentNotificationRepository;
use App\Repositories\EloquentOrderLogRepository;
use App\Repositories\EloquentOrderProductRepository;
use App\Repositories\EloquentOrderProductReturnRepository;
use App\Repositories\EloquentOrderRepository;
use App\Repositories\EloquentPasswordResetRepository;
use App\Repositories\EloquentPaymentRepository;
use App\Repositories\EloquentProductRepository;
use App\Repositories\EloquentProductStockSerialRepository;
use App\Repositories\EloquentPurchaseProductReturnRepository;
use App\Repositories\EloquentQuotationRepository;
use App\Repositories\EloquentRoleRepository;
use App\Repositories\EloquentStockLogRepository;
use App\Repositories\EloquentStockRepository;
use App\Repositories\EloquentStockTransferProductRepository;
use App\Repositories\EloquentSubCategoryRepository;
use App\Repositories\EloquentSubDepartmentRepository;
use App\Repositories\EloquentSupplierRepository;
use App\Repositories\EloquentTaxRepository;
use App\Repositories\EloquentUserProfileRepository;
use App\Repositories\EloquentUserRepository;
use App\Repositories\EloquentUserRoleModulePermissionRepository;
use App\Repositories\EloquentUserRoleRepository;
use App\Repositories\EloquentPayrollRepository;
use App\Repositories\EloquentAppSettingRepository;
use App\Repositories\EloquentPurchaseRepository;
use App\Repositories\EloquentPurchaseProductRepository;
use App\Repositories\EloquentUnitRepository;
use App\Repositories\EloquentStockTransferRepository;
use Carbon\Laravel\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        // bind RoleRepository
        $this->app->bind(RoleRepository::class, function () {
            return new EloquentRoleRepository(new Role());
        });

        // bind UserRepository
        $this->app->bind(UserRepository::class, function () {
            return new EloquentUserRepository(new User());
        });

        // bind PasswordResetRepository
        $this->app->bind(PasswordResetRepository::class, function () {
            return new EloquentPasswordResetRepository(new PasswordReset());
        });

        // bind UserRoleRepository
        $this->app->bind(UserRoleRepository::class, function () {
            return new EloquentUserRoleRepository(new UserRole());
        });

        // bind UserProfileRepository
        $this->app->bind(UserProfileRepository::class, function () {
            return new EloquentUserProfileRepository(new UserProfile());
        });

        // bind AdminRepository
        $this->app->bind(AdminRepository::class, function () {
            return new EloquentAdminRepository(new Admin());
        });

        // bind AttachmentRepository
        $this->app->bind(AttachmentRepository::class, function () {
            return new EloquentAttachmentRepository(new Attachment());
        });

        // bind CompanyRepository
        $this->app->bind(CompanyRepository::class, function () {
            return new EloquentCompanyRepository(new Company());
        });

        // bind BranchRepository
        $this->app->bind(BranchRepository::class, function () {
            return new EloquentBranchRepository(new Branch());
        });

        // bind ManagerRepository
        $this->app->bind(ManagerRepository::class, function () {
            return new EloquentManagerRepository(new Manager());
        });

        // bind EmployeeRepository
        $this->app->bind(EmployeeRepository::class, function () {
            return new EloquentEmployeeRepository(new Employee());
        });

        // bind CustomerRepository
        $this->app->bind(CustomerRepository::class, function () {
            return new EloquentCustomerRepository(new Customer());
        });

        // bind CategoryRepository
        $this->app->bind(CategoryRepository::class, function () {
            return new EloquentCategoryRepository(new Category());
        });

        // bind SubCategoryRepository
        $this->app->bind(SubCategoryRepository::class, function () {
            return new EloquentSubCategoryRepository(new SubCategory());
        });

        // bind BrandRepository
        $this->app->bind(BrandRepository::class, function () {
            return new EloquentBrandRepository(new Brand());
        });

        // bind SupplierRepository
        $this->app->bind(SupplierRepository::class, function () {
            return new EloquentSupplierRepository(new Supplier());
        });

        // bind ProductRepository
        $this->app->bind(ProductRepository::class, function () {
            return new EloquentProductRepository(new Product());
        });

        // bind StockRepository
        $this->app->bind(StockRepository::class, function () {
            return new EloquentStockRepository(new Stock());
        });

        // bind StockLogRepository
        $this->app->bind(StockLogRepository::class, function () {
            return new EloquentStockLogRepository(new StockLog());
        });

        // bind ExpenseCategoryRepository
        $this->app->bind(ExpenseCategoryRepository::class, function () {
            return new EloquentExpenseCategoryRepository(new ExpenseCategory());
        });

        // bind ExpenseRepository
        $this->app->bind(ExpenseRepository::class, function () {
            return new EloquentExpenseRepository(new Expense());
        });

        // bind OrderRepository
        $this->app->bind(OrderRepository::class, function () {
            return new EloquentOrderRepository(new Order());
        });

        // bind OrderProductRepository
        $this->app->bind(OrderProductRepository::class, function () {
            return new EloquentOrderProductRepository(new OrderProduct());
        });

        // bind CashUpRepository
        $this->app->bind(CashUpRepository::class, function () {
            return new EloquentCashUpRepository(new CashUp());
        });

        // bind PayrollRepository
        $this->app->bind(PayrollRepository::class, function () {
            return new EloquentPayrollRepository(new Payroll());
        });

        // bind AppSettingRepository
        $this->app->bind(AppSettingRepository::class, function () {
            return new EloquentAppSettingRepository(new AppSetting());
        });

        // bind PurchaseRepository
        $this->app->bind(PurchaseRepository::class, function () {
            return new EloquentPurchaseRepository(new Purchase());
        });

        // bind PurchaseProductRepository
        $this->app->bind(PurchaseProductRepository::class, function () {
            return new EloquentPurchaseProductRepository(new PurchaseProduct());
        });

        // bind PurchaseProductRepository
        $this->app->bind(UnitRepository::class, function () {
            return new EloquentUnitRepository(new Unit());
        });

        // bind DeliveryAgencyRepository
        $this->app->bind(DeliveryAgencyRepository::class, function () {
            return new EloquentDeliveryAgencyRepository(new DeliveryAgency());
        });

        // bind DeliveryRepository
        $this->app->bind(DeliveryRepository::class, function () {
            return new EloquentDeliveryRepository(new Delivery());
        });

        // bind StockTransferRepository
        $this->app->bind(StockTransferRepository::class, function () {
            return new EloquentStockTransferRepository(new StockTransfer());
        });

        // bind StockTransferProductRepository
        $this->app->bind(StockTransferProductRepository::class, function () {
            return new EloquentStockTransferProductRepository(new StockTransferProduct());
        });

        // bind PaymentRepository
        $this->app->bind(PaymentRepository::class, function () {
            return new EloquentPaymentRepository(new Payment());
        });

        // bind ModuleRepository
        $this->app->bind(ModuleRepository::class, function () {
            return new EloquentModuleRepository(new Module());
        });

        // bind ModuleActionRepository
        $this->app->bind(ModuleActionRepository::class, function () {
            return new EloquentModuleActionRepository(new ModuleAction());
        });

        // bind CompanyModuleRepository
        $this->app->bind(CompanyModuleRepository::class, function () {
            return new EloquentCompanyModuleRepository(new CompanyModule());
        });

        // bind UserRoleModulePermissionRepository
        $this->app->bind(UserRoleModulePermissionRepository::class, function () {
            return new EloquentUserRoleModulePermissionRepository(new UserRoleModulePermission());
        });

        // bind IncomeRepository
        $this->app->bind(IncomeRepository::class, function () {
            return new EloquentIncomeRepository(new Income());
        });

        // bind IncomeCategoryRepository
        $this->app->bind(IncomeCategoryRepository::class, function () {
            return new EloquentIncomeCategoryRepository(new IncomeCategory());
        });

        // bind AdjustmentRepository
        $this->app->bind(AdjustmentRepository::class, function () {
            return new EloquentAdjustmentRepository(new Adjustment());
        });

        // bind PurchaseProductReturnRepository
        $this->app->bind(PurchaseProductReturnRepository::class, function () {
            return new EloquentPurchaseProductReturnRepository(new PurchaseProductReturn());
        });

        // bind OrderProductReturnRepository
        $this->app->bind(OrderProductReturnRepository::class, function () {
            return new EloquentOrderProductReturnRepository(new OrderProductReturn());
        });

        // bind TaxRepository
        $this->app->bind(TaxRepository::class, function () {
            return new EloquentTaxRepository(new Tax());
        });

        // bind DiscountRepository
        $this->app->bind(DiscountRepository::class, function () {
            return new EloquentDiscountRepository(new Discount());
        });

        // bind Product Stock Serial Repository
        $this->app->bind(ProductStockSerialRepository::class, function (){
            return new EloquentProductStockSerialRepository(new ProductStockSerial());
        });

        // bind QuotationRepository
        $this->app->bind(QuotationRepository::class, function () {
            return new EloquentQuotationRepository(new Quotation());
        });

        // bind CouponRepository
        $this->app->bind(CouponRepository::class, function () {
            return new EloquentCouponRepository(new Coupon());
        });

        // bind CouponCustomerRepository
        $this->app->bind(CouponCustomerRepository::class, function () {
            return new EloquentCouponCustomerRepository(new CouponCustomer());
        });

        // bind NotificationRepository
        $this->app->bind(NotificationRepository::class, function () {
            return new EloquentNotificationRepository(new Notification());
        });

        // bind CustomerLoyaltyRewardRepository
        $this->app->bind(CustomerLoyaltyRewardRepository::class, function () {
            return new EloquentCustomerLoyaltyRewardRepository(new CustomerLoyaltyReward());
        });

        // bind EcomIntegrationRepository
        $this->app->bind(EcomIntegrationRepository::class, function () {
            return new EloquentEcomIntegrationRepository(new EcomIntegration());
        });

        // bind DepartmentRepository
        $this->app->bind(DepartmentRepository::class, function () {
            return new EloquentDepartmentRepository(new Department());
        });

        // bind SubDepartmentRepository
        $this->app->bind(SubDepartmentRepository::class, function () {
            return new EloquentSubDepartmentRepository(new SubDepartment());
        });

        // bind OrderLogRepository
        $this->app->bind(OrderLogRepository::class, function () {
            return new EloquentOrderLogRepository(new OrderLog());
        });
    }
}
