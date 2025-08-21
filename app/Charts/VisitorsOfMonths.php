<?php

namespace App\Charts;

use App\Helpers\ChartHelper;
use ConsoleTVs\Charts\Classes\Highcharts\Chart;

class VisitorsOfMonths extends Chart
{
    /**
     * Initializes the chart.
     *
     * @return void
     */
    public function __construct($period = 6, $per_day_visits = 0)
    {
        parent::__construct();

        $labels = ChartHelper::Months($period);

        $labels[$period - 1] = trans('app.this_month');

        $breakdown = config('charts.visitors.breakdown_last_days') > 0 ? config('charts.visitors.breakdown_last_days') : null;

        if ($breakdown) {
            $lastWeek = ChartHelper::Days($breakdown, 'l');
            $lastWeek[$breakdown - 1] = trans('app.today');
            $labels = array_merge($labels, $lastWeek);
        }

        $this->height(300)->width(0)
            ->labels($labels)
            ->options([
                'subtitle' => [
                    'text' => $per_day_visits > 0 ? trans('app.visits_per_day', ['value' => $per_day_visits]) : null,
                ],
                'legend' => [
                    'layout' => 'vertical',
                    'align' => 'left',
                    'verticalAlign' => 'top',
                    'x' => 40,
                    'y' => 30,
                    'floating' => true,
                    'borderWidth' => 0,
                    'backgroundColor' => '#FFFFFF',
                ],
                'yAxis' => [
                    'title' => [
                        'text' => null,
                    ],
                ],
                'tooltip' => [
                    'shared' => true,
                ],
                'credits' => [
                    'enabled' => false,
                ],
                'plotOptions' => [
                    'series' => [
                        'marker' => [
                            'enabled' => false,
                        ],
                    ],
                ],
                'exporting'  => [
                    'buttons'  => [
                        'contextButton' => [
                            'menuItems' => ['printChart', 'downloadPNG', 'downloadCSV', 'downloadXLS'],
                        ],
                    ],
                ],
            ]);
    }
}
