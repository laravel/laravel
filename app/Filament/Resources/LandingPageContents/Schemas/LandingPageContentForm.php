<?php

namespace App\Filament\Resources\LandingPageContents\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class LandingPageContentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('key')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true)
                    ->helperText('Unique identifier (e.g., hero_title, services_heading)')
                    ->disabled(fn ($record) => $record !== null)
                    ->columnSpanFull(),
                
                Select::make('section')
                    ->required()
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
                    ])
                    ->searchable(),
                
                Select::make('type')
                    ->required()
                    ->options([
                        'text' => 'Short Text',
                        'textarea' => 'Long Text',
                        'rich_text' => 'Rich Text (HTML)',
                    ])
                    ->default('text'),
                
                Textarea::make('description')
                    ->rows(2)
                    ->helperText('Admin-facing note about what this content is for')
                    ->columnSpanFull(),
                
                TextInput::make('value.en')
                    ->label('English Content')
                    ->required()
                    ->maxLength(1000)
                    ->helperText('Content in English')
                    ->columnSpanFull(),
                
                TextInput::make('value.ar')
                    ->label('Arabic Content (العربية)')
                    ->maxLength(1000)
                    ->helperText('Content in Arabic')
                    ->nullable()
                    ->columnSpanFull(),
            ])
            ->columns(2);
    }
}
