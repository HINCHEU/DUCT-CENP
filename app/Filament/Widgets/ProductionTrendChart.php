<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use App\Models\OrderItem;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use Illuminate\Support\Carbon;

class ProductionTrendChart extends ChartWidget
{
    protected ?string $heading = 'Production Volume (m²)';
    protected static ?int $sort = 2;
    
    // In Filament v3, use ->columnSpan(...) on the widget if needed, or set it via property
    protected int | string | array $columnSpan = 1;

    protected function getData(): array
    {
        // Simple aggregate of total area from order items
        // In a real app we'd group by the order's created_at, but we don't have the package Flowframe\Trend by default unless installed.
        // Wait, let's just write a raw query or simple Eloquent since Flowframe might not be installed.

        $data = [];
        $labels = [];

        // Last 7 days
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $labels[] = $date->format('M d');
            
            $area = OrderItem::whereHas('order', function ($query) use ($date) {
                $query->whereDate('created_at', $date);
            })->sum('total_area');
            
            $data[] = $area;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Total Area Fabricated',
                    'data' => $data,
                    'fill' => 'start',
                    'backgroundColor' => 'rgba(139, 92, 246, 0.2)', // Violet
                    'borderColor' => 'rgb(139, 92, 246)',
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
