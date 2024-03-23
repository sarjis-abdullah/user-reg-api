<?php

use App\Models\Order;
use App\Models\Purchase;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class UpdatePaymentableIdAndTypeValueToPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('payments', function (Blueprint $table) {
            DB::statement("ALTER TABLE payments MODIFY paymentableId BIGINT UNSIGNED NULL DEFAULT NULL");

            $payments = DB::table('payments')->select('id', 'orderId', 'purchaseId')->get();

            foreach ($payments as $payment) {
                $paymentableId = $payment->orderId ?: $payment->purchaseId;
                $paymentableType = $payment->orderId ? Order::class : Purchase::class;

                DB::table('payments')->where('id', $payment->id)
                    ->update(['paymentableId' => $paymentableId, 'paymentableType' => $paymentableType]);
            }

            DB::statement("ALTER TABLE payments MODIFY paymentableId BIGINT UNSIGNED NOT NULL");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('payments', function (Blueprint $table) {
            // Drop the default value constraint for paymentableId column
            DB::statement("ALTER TABLE payments MODIFY paymentableId BIGINT UNSIGNED NULL DEFAULT NULL");

            // Reset paymentableId to NULL
            DB::table('payments')->update(['paymentableId' => null, 'paymentableType' => null]);

            // Restore the default value constraint for paymentableId column
            DB::statement("ALTER TABLE payments MODIFY paymentableId BIGINT UNSIGNED NOT NULL");
        });
    }
}
