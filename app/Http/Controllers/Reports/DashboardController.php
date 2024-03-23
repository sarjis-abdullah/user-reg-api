<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Http\Requests\Reports\DaliySummaryRequest;
use App\Http\Requests\Reports\ModelInstanceCountRequest;
use App\Http\Requests\Reports\SaleChartRequest;
use App\Http\Resources\Reports\DailySummaryResource;
use App\Http\Resources\Reports\DashboardResource;
use App\Services\Reports\Dashboard;
use Illuminate\Support\Arr;

class DashboardController extends Controller
{
    /**
     * @param ModelInstanceCountRequest $request
     * @return DashboardResource
     */
    public function getStateOfResource(ModelInstanceCountRequest $request): DashboardResource
    {
        $state = Dashboard::dashboardCountOfModelInstanceStates($request->all());

        return new DashboardResource($state);
    }

    /**
     * @param SaleChartRequest $request
     * @return array
     */
    public function getSaleChartCount(SaleChartRequest $request): array
    {
        return Dashboard::getMonthlySaleCount($request->all());
    }

    /**
     * @param DaliySummaryRequest $request
     * @return DailySummaryResource
     */
    public function getDailySummaryReport(DaliySummaryRequest $request): DailySummaryResource
    {
        $dailySummary = Dashboard::getDailySummary($request->all());

        $dailySummaryResources = new DailySummaryResource($dailySummary['summary']);
        $dailySummaryResources->additional(Arr::except($dailySummary, ['summary']));

        return $dailySummaryResources;
    }

}
