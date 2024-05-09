<?php

namespace App\Filament\Resources\OrderResource\Widgets;

use App\Models\Order;
use Illuminate\Support\Number;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class OrderStats extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('New Orders', Order::query()->where('status','New')->count() ?? 0),
            Stat::make('Order Processing', Order::query()->where('status','Processing')->count() ?? 0),
            Stat::make('Order Shipped', Order::query()->where('status','Shipped')->count() ?? 0),
            // Stat::make('Order Canceled', Order::query()->where('status','Cancelled')->count() ?? 0),
            Stat::make('Average Price', Number::currency(Order::query()->avg('grand_total') ?? 0, 'IDR'))
        ];
    }
}
