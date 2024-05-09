<?php

namespace App\Filament\Resources\UserResource\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use App\Models\Order;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Actions\CreateAction;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use App\Filament\Resources\OrderResource;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Resources\RelationManagers\RelationManager;

class OrdersRelationManager extends RelationManager
{
    protected static string $relationship = 'orders';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
               
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                TextColumn::make('id')
                    ->label('Order ID'),
                
                TextColumn::make('grand_total')
                        ->money('IDR')
                        ->sortable(),
                
                TextColumn::make('status')
                        ->badge()
                        ->searchable()
                        ->sortable()
                        ->color(fn (string $state): string => match ($state) {
                            'New' => 'info',
                            'Processing' => 'warning',
                            'Shipped' => 'success',
                            'Delivered' => 'success',
                            'Cancelled' => 'danger',
                        })
                        ->icon(fn (string $state): string => match ($state) {
                            'New' => 'heroicon-m-sparkles',
                            'Processing' => 'heroicon-m-arrow-path',
                            'Shipped' => 'heroicon-m-truck',
                            'Delivered' => 'heroicon-m-check-badge',
                            'Cancelled' => 'heroicon-m-x-circle',
                        }),
                
                TextColumn::make('payment_method')
                        ->searchable()
                        ->sortable(),
                
                TextColumn::make('payment_status')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Pending' => 'warning',
                        'Paid' => 'success',
                        'Failed' => 'danger',
                    }),
                
                TextColumn::make('created_at')
                        ->label('Order Date')
                        ->searchable()
                        ->sortable()
            ])
            ->filters([
                //
            ])
            ->headerActions([
                // Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Action::make('View Order')
                    ->url(fn (Order $record): string => OrderResource::getUrl('view', ['record' => $record]))
                    ->color('info')
                    ->icon('heroicon-o-eye'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
