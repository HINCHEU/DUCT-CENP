<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;

class RecentOrdersTableWidget extends BaseWidget
{
    protected static ?int $sort = 4;
    
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(Order::query()->latest()->limit(5))
            ->columns([
                TextColumn::make('order_number')
                    ->label('Order #')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('site.name')
                    ->label('Site')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('creator.name')
                    ->label('Requested By')
                    ->searchable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'draft'       => 'gray',
                        'submitted'   => 'warning',
                        'ready'       => 'info',
                        'fabricating' => 'primary',
                        'delivered'   => 'success',
                        default       => 'gray',
                    }),
                TextColumn::make('created_at')
                    ->label('Submitted')
                    ->dateTime('M d, Y')
                    ->sortable(),
            ])
            ->actions([
                Action::make('view')
                    ->url(fn (Order $record): string => route('filament.admin.resources.orders.view', $record))
                    ->icon('heroicon-m-eye'),
            ])
            ->paginated(false);
    }
}
