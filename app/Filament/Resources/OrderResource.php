<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Order;
use App\Models\Product;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Support\Number;
use Filament\Resources\Resource;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\SelectColumn;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\ToggleButtons;
use App\Filament\Resources\OrderResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\OrderResource\RelationManagers;
use App\Filament\Resources\OrderResource\RelationManagers\AddressRelationManager;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Group::make()->schema([
                    Section::make('Order Information')
                        ->schema([
                        Select::make('user_id')
                            ->label('Customer')
                            ->relationship('user','name')
                            ->searchable()
                            ->preload()
                            ->required(),

                        Select::make('payment_method')
                            ->options([
                                'Bank Transfer' => 'Bank Transfer',
                                'Cash On Delivery' => 'Cash On Delivery',
                            ])
                            ->required(),
                        
                        Select::make('payment_status')
                            ->options([
                                    'Pending' => 'Pending',
                                    'Paid' => 'Paid',
                                    'Failed' => 'Failed'
                            ])
                            ->default('Pending')
                            ->required(),
                        
                        ToggleButtons::make('status')
                            ->inline()
                            ->default('new')
                            ->required()
                            ->options([
                                'New' => 'New',
                                'Processing' => 'Processing',
                                'Shipped' => 'Shipped',
                                'Delivered' => 'Delivered',
                                'Cancelled' => 'Cancelled',
                            ])
                            ->colors([
                                'New' => 'info',
                                'Processing' => 'warning',
                                'Shipped' => 'success',
                                'Delivered' => 'success',
                                'Cancelled' => 'danger',
                            ])
                            ->icons([
                                'New' => 'heroicon-m-sparkles',
                                'Processing' => 'heroicon-m-arrow-path',
                                'Shipped' => 'heroicon-m-truck',
                                'Delivered' => 'heroicon-m-check-badge',
                                'Cancelled' => 'heroicon-m-x-circle',
                            ]),
                        
                            Select::make('currency')
                                ->options([
                                    'IDR' => 'IDR'
                                ])
                                ->default('IDR')
                                ->required(),
                            
                            Select::make('shipping_method')
                                ->options([
                                    'Shopee Express' => 'Shopee Express',
                                    'JNE' => 'JNE',
                                    'JNT' => 'JNT',
                                    'SiCepat' => 'SiCepat',
                                    'AnterAja' => 'AnterAja',
                                ]),
                            
                            Textarea::make('notes')
                                ->columnSpanFull(),
                    ])->columns(2),

                    Section::make('Order Items')
                        ->schema([
                            Repeater::make('items')
                                ->relationship()
                                ->schema([
                                    Select::make('product_id')
                                        ->relationship('product', 'name')
                                        ->searchable()
                                        ->preload()
                                        ->distinct()
                                        ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                                        ->reactive()
                                        ->afterStateUpdated(fn($state, Set $set) => $set('unit_amount', Product::find($state)?->price ?? 0))
                                        ->afterStateUpdated(fn($state, Set $set) => $set('total_amount', Product::find($state)?->price ?? 0))
                                        ->columnSpan(5),
                                    
                                    TextInput::make('quantity')
                                        ->numeric()
                                        ->required()
                                        ->default(1)
                                        ->minValue(1)
                                        ->reactive()
                                        ->afterStateUpdated(fn($state, Set $set, Get $get) => $set('total_amount', $get('unit_amount') * $state))
                                        ->columnSpan(1),

                                     TextInput::make('unit_amount')
                                        ->numeric()
                                        ->required()
                                        ->prefix('IDR')
                                        ->disabled()
                                        ->dehydrated()
                                        ->columnSpan(3),

                                     TextInput::make('total_amount')
                                        ->numeric()
                                        ->required()
                                        ->prefix('IDR')
                                        ->disabled()
                                        ->dehydrated()
                                        ->columnSpan(3),
                                ])->columns(12),

                                Placeholder::make('grand_total_placeholder')
                                    ->label('Grand Total')
                                    ->content(function (Get $get, Set $set) {
                                        $total = 0;
                                        if(!$repeaters = $get('items')) {
                                            return $total;
                                        }

                                        foreach ($repeaters as $key => $repeater) {
                                            $total += $get("items.{$key}.total_amount");
                                        }

                                        $set('grand_total', $total);
                                        return Number::currency($total, 'IDR');
                                    }),
                                
                                Hidden::make('grand_total')
                                    ->default(0),
                        ]),
                ])->columnSpanFull()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label('Customer')
                    ->searchable(),
                
                TextColumn::make('grand_total')
                    ->numeric()
                    ->sortable()
                    ->money('IDR'),
                
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

                TextColumn::make('currency')
                    ->searchable(),

                TextColumn::make('shipping_method')
                    ->searchable()
                    ->sortable(),
                
                SelectColumn::make('status')
                    ->options([
                        'New' => 'New',
                        'Processing' => 'Processing',
                        'Shipped' => 'Shipped',
                        'Delivered' => 'Delivered',
                        'Cancelled' => 'Cancelled',
                    ])
                    ->searchable()
                    ->sortable(),
                
                TextColumn::make('created_at')
                    ->datetime()
                    ->timezone('Asia/Jakarta')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                
                TextColumn::make('updated_at')
                    ->datetime()
                    ->timezone('Asia/Jakarta')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            AddressRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'view' => Pages\ViewOrder::route('/{record}'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string {
        return static::getModel()::count();
    }

     public static function getNavigationBadgeColor(): string|array|null {
        return static::getModel()::count() > 0 ? 'success' : 'danger';
    }
}
