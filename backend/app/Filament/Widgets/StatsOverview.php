<?php

namespace App\Filament\Widgets;

use App\Models\Athlete;
use App\Models\Coach;
use App\Models\Referee;
use App\Models\Club;
use App\Models\Achievement;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Atlet Takraw', Athlete::count())
                ->description('Terdaftar aktif di PSTI Kota Bandung')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('success'),
            Stat::make('Total Pelatih', Coach::count())
                ->description('ASTAF & Nasional Certified')
                ->descriptionIcon('heroicon-m-academic-cap')
                ->color('warning'),
            Stat::make('Total Wasit', Referee::count())
                ->description('White & Bronze Badge')
                ->descriptionIcon('heroicon-m-shield-check')
                ->color('danger'),
            Stat::make('Total Klub Terdaftar', Club::count())
                ->description('Klub binaan di bawah naungan PSTI')
                ->descriptionIcon('heroicon-m-home-modern')
                ->color('info'),
        ];
    }

    public static function canView(): bool
    {
        $user = auth()->user();
        return $user && in_array($user->role, ['Super Admin', 'Admin', 'Pengurus', 'Pelatih']);
    }
}
