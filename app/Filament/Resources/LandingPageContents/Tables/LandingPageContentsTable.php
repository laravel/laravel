<?php

namespace App\Filament\Resources\LandingPageContents\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class LandingPageContentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('key')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                
                TextColumn::make('section')
                    ->badge()
                    ->searchable()
                    ->sortable(),
                
                TextColumn::make('type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'text' => 'gray',
                        'textarea' => 'info',
                        'rich_text' => 'warning',
                    })
                    ->sortable(),
                
                TextColumn::make('value')
                    ->label('Content Preview')
                    ->limit(50)
                    ->getStateUsing(function ($record) {
                        $locale = app()->getLocale();
                        return $record->value[$locale] ?? $record->value['en'] ?? '';
                    }),
                
                TextColumn::make('description')
                    ->limit(30)
                    ->toggleable(isToggledHiddenByDefault: true),
                
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('section')
                    ->options([
                        'hero' => 'Hero Section',
                        'services' => 'Services Section',
                        'pricing' => 'Pricing Section',
                        'features' => 'Features Section',
                        'how_it_works' => 'How It Works Section',
                        'statistics' => 'Statistics Section',
                        'about' => 'About Section',
                        'testimonials' => 'Testimonials Section',
                        'tech_stack' => 'Technology Stack Section',
                        'faq' => 'FAQ Section',
                        'newsletter' => 'Newsletter Section',
                        'contact' => 'Contact Section',
                    ]),
                
                SelectFilter::make('type')
                    ->options([
                        'text' => 'Short Text',
                        'textarea' => 'Long Text',
                        'rich_text' => 'Rich Text (HTML)',
                    ]),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('section');
    }
}
