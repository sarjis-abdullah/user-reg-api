<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDailySummaryReportHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('daily_summary_report_histories', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->json('branch_wise');
            $table->json('all_branch');
            $table->boolean('operation_status');
            $table->text('operation_message');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('daily_summary_report_histories');
    }
}
