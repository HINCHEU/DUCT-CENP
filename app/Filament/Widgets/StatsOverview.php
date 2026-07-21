<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use App\Models\OrderItem;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Carbon\Carbon;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $startOfMonth = Carbon::now()->startOfMonth();

        // Orders Pending Approval — "submitted" means awaiting review
        $pendingApprovals = Order::whereIn('status', ['submitted'])->count();

        // Total Area Fabricated this month from OrderItems whose order is active/done
        $totalArea = OrderItem::whereHas('order', function ($q) use ($startOfMonth) {
            $q->whereIn('status', ['ready', 'fabricating', 'delivered'])
              ->where('created_at', '>=', $startOfMonth);
        })->sum('total_area');

        // Total Active Orders (not draft, not delivered)
        $activeOrders = Order::whereNotIn('status', ['draft', 'delivered'])->count();

        return [
            Stat::make('Pending Approvals', $pendingApprovals)
                ->description('Orders waiting for review')
                ->descriptionIcon('heroicon-m-clock')
                ->color($pendingApprovals > 0 ? 'warning' : 'success'),
            Stat::make('Volume This Month', number_format($totalArea, 2) . ' m²')
                ->description('Fabricated ductwork')
                ->descriptionIcon('heroicon-m-cube')
                ->color('primary'),
            Stat::make('Active Orders', $activeOrders)
                ->description('Orders currently in progress')
                ->descriptionIcon('heroicon-m-clipboard-document-check')
                ->color('success'),
        ];
    }
}
