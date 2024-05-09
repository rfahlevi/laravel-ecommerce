<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Product;
use Filament\Forms\Set;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Filament\Resources\Resource;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Section;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\ImageColumn;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\MarkdownEditor;
use App\Filament\Resources\ProductResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\ProductResource\RelationManagers;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-squares-2x2';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Group::make()->schema([
                    Section::make('Product Information')->schema([
                        TextInput::make('name')
                        ->required()
                        ->live(onBlur: true)
                        ->afterStateUpdated(fn(string $operation, $state, Set $set) => $operation
                        === 'create' ? $set('slug', Str::slug($state)) : null)
                        ->maxLength(255),

                        TextInput::make('slug')
                        ->required()
                        ->maxLength(255)
                        ->disabled()
                        ->dehydrated()
                        ->unique(Product::class, 'slug', ignoreRecord:true),

                        MarkdownEditor::make('description')
                        ->columnSpanFull()
                        ->fileAttachmentsDirectory('products'),

                    ])->columns(2),

                    Section::make('Product Images')->schema([
                        FileUpload::make('images')
                        ->label('Upload Images (max: 5')
                        ->multiple()
                        ->directory('products')
                        ->maxFiles(5)
                        ->reorderable(),
                    ])
                ])->columnSpan(2),

                Group::make()->schema([
                    Section::make('Product Price')->schema([
                        TextInput::make('price')
                        ->required()
                        ->numeric()
                        ->prefix('IDR')
                    ]),

                    Section::make('Associations')
                        ->schema([
                            Select::make('category_id')
                                ->label('Select Category')
                                ->required()
                                ->searchable()
                                ->preload()
                                ->relationship('category', 'name'),
                            
                            Select::make('brand_id')
                                ->label('Select Brand')
                                ->required()
                                ->searchable()
                                ->preload()
                                ->relationship('brand', 'name'),
                        ]),
                    
                    Section::make('Status')
                        ->schema([
                            Toggle::make('in_stock')
                            ->required()
                            ->default(true),

                            Toggle::make('is_active')
                            ->required()
                            ->default(true),

                            Toggle::make('is_featured')
                            ->required(),

                            Toggle::make('on_sale')
                            ->required()
                        ])
                        ->columns(2),
                ])->columnSpan(1)
            ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('images')
                    ->circular()
                    ->stacked()
                    ->limit(5),
                TextColumn::make('name')
                    ->sortable()
                    ->limit(40)
                    ->searchable(),
                TextColumn::make('category.name'),
                TextColumn::make('brand.name'),
                TextColumn::make('price')
                    ->money('IDR')
                    ->sortable(), 
                IconColumn::make('in_stock')
                    ->boolean(),
                IconColumn::make('is_active')
                    ->boolean(),
                IconColumn::make('is_featured')
                    ->boolean(),
                IconColumn::make('on_sale')
                    ->boolean(),
                TextColumn::make('created_at')
                    ->datetime()
                    ->timezone('Asia/Jakarta')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->datetime()
                    ->timezone('Asia/Jakarta')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('category')
                    ->relationship('category', 'name'),
                
                SelectFilter::make('brand')
                    ->relationship('brand', 'name'),
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
