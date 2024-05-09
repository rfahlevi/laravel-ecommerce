<?php

namespace App\Filament\Resources\OrderResource\Pages;

use Filament\Actions;
use Filament\Resources\Components\Tab;
use App\Filament\Resources\OrderResource;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\OrderResource\Widgets\OrderStats;

class ListOrders extends ListRecords
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            OrderStats::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'All' => Tab::make('All'),
            'New' => Tab::make()->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'New')),
            'Processing' => Tab::make()->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'Processing')),
            'Shipped' => Tab::make()->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'Shipped')),
            'Delivered' => Tab::make()->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'Delivered')),
            'Cancelled' => Tab::make()->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'Cancelled')),
        ];
    }
}
