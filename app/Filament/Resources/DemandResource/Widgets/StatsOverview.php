<?php

namespace App\Filament\Resources\DemandResource\Widgets;

use App\Models\Demand;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;



use Illuminate\Support\Facades\Auth;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $user = Auth::user();
        $isAdmin = $user->isAdmin();

        // Base queries
        $baseQuery = Demand::query();
        $monthlyQuery = Demand::whereBetween('created_at', [
            now()->startOfMonth(),
            now()->endOfMonth()
        ]);

        if (!$isAdmin) {
            $baseQuery->where('user_id', $user->id);
            $monthlyQuery->where('user_id', $user->id);
        }


        $totalDemands = $baseQuery->count();
        $monthlyDemands = $monthlyQuery->count();
        $newDemands = $baseQuery->clone()->whereDate('created_at', today())->count();
        $pendingDemands = $baseQuery->clone()->where('status', 'pending')->count();
        $completedDemands = $baseQuery->clone()->where('status', 'completed')->count();

        return [
            Stat::make(__('resources.widgets.stats_overview.new_demands'), $newDemands)
                ->description('Today')
                ->descriptionIcon('heroicon-o-arrow-trending-up')
                ->color('info') //(primary, success, warning, danger, info)
                ->chart([7, 3, 4, 5, 6, 3, 5]),

            Stat::make(__('resources.widgets.stats_overview.total_demands'), $totalDemands.' / 100')
                ->description('All demands')
                ->descriptionIcon('heroicon-o-document-text')
                ->chart([7, 3, 4, 5, 6, 3, 5])
                ->color('warning'),

            Stat::make(__('resources.widgets.stats_overview.pending_demands'), $pendingDemands)
                ->description('Require action')
                ->descriptionIcon('heroicon-o-clock')
                ->chart([3, 5, 2, 4, 6, 3, 2])
                ->color('danger'),

            Stat::make(__('resources.widgets.stats_overview.completed_demands'), $completedDemands)
                ->description('Finished work')
                ->descriptionIcon('heroicon-o-check-circle')
                ->chart([2, 4, 3, 1, 5, 6, 7])
                ->color('success'),



            // Only show to admins
            /*$isAdmin ? Stat::make('Active Users', User::where('is_active', true)->count())
                ->description('Currently active')
                ->descriptionIcon('heroicon-o-users')
                ->chart([1, 2, 3, 4, 3, 2, 1])
                ->color('info') : null,*/
        ];
    }

    // Optional: Card customization
    protected function getColumns(): int
    {
        return 4;
    }

    // Optional: Refresh interval
    protected function getPollingInterval(): ?string
    {
        return '30s';
    }
}
