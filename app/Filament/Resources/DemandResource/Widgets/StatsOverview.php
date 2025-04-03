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

        // Base query
        $query = Demand::query();

        if (!$isAdmin) {
            $query->where('user_id', $user->id);
        }

        $totalDemands = $query->count();
        $pendingDemands = $query->where('status', 'pending')->count();
        $completedDemands = $query->where('status', 'completed')->count();

        return [
            Stat::make('Total Demands', $totalDemands)
                ->description('All demands')
                ->descriptionIcon('heroicon-o-document-text')
                ->chart([7, 3, 4, 5, 6, 3, 5])
                ->color('primary'),

            Stat::make('Pending Demands', $pendingDemands)
                ->description('Require action')
                ->descriptionIcon('heroicon-o-clock')
                ->chart([3, 5, 2, 4, 6, 3, 2])
                ->color('warning'),

            Stat::make('Completed Demands', $completedDemands)
                ->description('Finished work')
                ->descriptionIcon('heroicon-o-check-circle')
                ->chart([2, 4, 3, 1, 5, 6, 7])
                ->color('success'),

            // For trend indicators
            Stat::make('New Demands', Demand::whereDate('created_at', today())->count())
                ->description('Today')
                ->descriptionIcon('heroicon-o-arrow-trending-up')
                ->color($increase ? 'success' : 'danger')
                ->chart([7, 3, 4, 5, 6, 3, 5]),

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
