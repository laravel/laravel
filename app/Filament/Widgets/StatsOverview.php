<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Services', \App\Models\Service::count())
                ->description('All registered services')
                ->descriptionIcon('heroicon-o-server')
                ->color('primary'),
            Stat::make('Active Services', \App\Models\Service::where('status', 'active')->count())
                ->description('Currently active')
                ->descriptionIcon('heroicon-o-check-circle')
                ->color('success'),
            Stat::make('Pending Services', \App\Models\Service::where('status', 'pending')->count())
                ->description('Awaiting activation')
                ->descriptionIcon('heroicon-o-clock')
                ->color('warning'),
            Stat::make('Monthly Revenue', '$' . number_format(\App\Models\Service::where('status', 'active')->sum('price'), 2))
                ->description('Active services revenue')
                ->descriptionIcon('heroicon-o-currency-dollar')
                ->color('success'),
        ];
    }
}
