<?php

namespace App\Filament\Widgets;

use App\Models\OrderItem;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class DuctTypeDistributionChart extends ChartWidget
{
    protected ?string $heading = 'Fabrication by Duct Type (Area)';
    protected static ?int $sort = 3;
    protected int | string | array $columnSpan = 1;

    protected function getData(): array
    {
        $distribution = OrderItem::select('duct_types.name', DB::raw('SUM(order_items.total_area) as total'))
            ->join('duct_types', 'order_items.duct_type_id', '=', 'duct_types.id')
            ->groupBy('duct_types.name')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        $labels = $distribution->pluck('name')->toArray();
        $data = $distribution->pluck('total')->toArray();

        return [
            'datasets' => [
                [
                    'label' => 'Total Area (m²)',
                    'data' => $data,
                    'backgroundColor' => [
                        '#8b5cf6', // Violet
                        '#ec4899', // Pink
                        '#0ea5e9', // Sky
                        '#10b981', // Emerald
                        '#f59e0b', // Amber
                    ],
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
