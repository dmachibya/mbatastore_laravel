<?php

namespace App\Charts;

use App\Helpers\ChartHelper;
use Carbon\Carbon;
use ConsoleTVs\Charts\Classes\Highcharts\Chart;

class SalesByPeriod extends Chart
{
    /**
     * Initializes the chart.
     *
     * @return void
     */
    public function __construct($start = null, $end = null, $grp_by = 'M')
    {
        parent::__construct();

        $start = ChartHelper::getStartDate($start);

        if ($grp_by == 'D') {
            $end = $end ? Carbon::parse($end) : $start->copy()->subDays(config('charts.default.days'));
            $grp_by = 'F d';
            $period = $start->diffInDays($end);
            $dates = ChartHelper::Days($period, $grp_by, $start);
        } else {
            $end = $end ? Carbon::parse($end) : $start->copy()->subMonths(config('charts.default.months'));
            $grp_by = 'F';
            $period = $start->diffInMonths($end);
            $dates = ChartHelper::Months($period, $grp_by);
        }

        $this->displayLegend(false)
            ->height(200)->width(0)
            ->labels($dates)
            ->options([
                'yAxis' => [
                    'title' => [
                        'text' => null,
                    ],
                    'labels' => [
                        'align'    => 'right',
                        'format' => config('system_settings.currency.symbol') . '{value}',
                    ],
                ],
                'tooltip' => [
                    'useHTML' => true,
                    'pointFormat' => '<small>{series.name}: <b>' . config('system_settings.currency.symbol') . '{point.y}</b></small>',
                ],
                'credits' => [
                    'enabled' => false,
                ],
            ]);
    }
}
