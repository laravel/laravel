<?php

namespace App\Filament\Resources\LandingPageContents;

use App\Filament\Resources\LandingPageContents\Pages\CreateLandingPageContent;
use App\Filament\Resources\LandingPageContents\Pages\EditLandingPageContent;
use App\Filament\Resources\LandingPageContents\Pages\ListLandingPageContents;
use App\Filament\Resources\LandingPageContents\Schemas\LandingPageContentForm;
use App\Filament\Resources\LandingPageContents\Tables\LandingPageContentsTable;
use App\Models\LandingPageContent;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class LandingPageContentResource extends Resource
{
    protected static ?string $model = LandingPageContent::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedLanguage;

    protected static ?string $navigationLabel = 'Landing Page Content';

    protected static ?string $recordTitleAttribute = 'key';

    public static function form(Schema $schema): Schema
    {
        return LandingPageContentForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return LandingPageContentsTable::configure($table);
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
            'index' => ListLandingPageContents::route('/'),
            'create' => CreateLandingPageContent::route('/create'),
            'edit' => EditLandingPageContent::route('/{record}/edit'),
        ];
    }
}
