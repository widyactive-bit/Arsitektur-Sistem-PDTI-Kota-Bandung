<?php

namespace App\Filament\Widgets;

use App\Models\Athlete;
use App\Models\AthleteStat;
use Filament\Widgets\ChartWidget;
use Carbon\Carbon;

class AthleteGrowthChart extends ChartWidget
{
    protected static ?string $heading = 'Grafik Perkembangan Rata-Rata Skor Atlet (6 Bulan Terakhir)';

    protected function getData(): array
    {
        // Fetch monthly stat averages for the last 6 months
        $months = [];
        $averages = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $months[] = $date->format('M Y');
            
            // Calculate average ranking score of all athletes for that month
            // For mock/simplification, we calculate avg based on stats in that period
            $statsForMonth = AthleteStat::whereYear('record_date', $date->year)
                ->whereMonth('record_date', $date->month)
                ->get();
            
            if ($statsForMonth->isEmpty()) {
                $averages[] = 0.0;
            } else {
                $scoresSum = 0.0;
                foreach ($statsForMonth as $stat) {
                    $scoresSum += $stat->athlete->calculateRankingScore();
                }
                $averages[] = round($scoresSum / $statsForMonth->count(), 2);
            }
        }

        return [
            'datasets' => [
                [
                    'label' => 'Skor Rata-Rata Atlet',
                    'data' => $averages,
                    'borderColor' => '#e5b922',
                    'backgroundColor' => 'rgba(229, 185, 34, 0.2)',
                    'fill' => true,
                ],
            ],
            'labels' => $months,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
