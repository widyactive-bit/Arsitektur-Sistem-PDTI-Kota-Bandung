<?php

namespace App\Filament\Resources;

use App\Models\Club;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use App\Filament\Resources\ClubResource\Pages;

class ClubResource extends Resource
{
    protected static ?string $model = Club::class;

    protected static ?string $navigationIcon = 'heroicon-o-home-modern';

    protected static ?string $navigationLabel = 'Data Klub';

    protected static ?string $pluralLabel = 'Klub';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nama_klub')
                    ->required()
                    ->label('Nama Klub'),
                Forms\Components\TextInput::make('alamat')
                    ->required()
                    ->label('Alamat Sekretariat'),
                Forms\Components\TextInput::make('pelatih')
                    ->required()
                    ->label('Pelatih Kepala'),
                Forms\Components\TextInput::make('jumlah_atlet')
                    ->numeric()
                    ->required()
                    ->label('Jumlah Atlet Terdaftar'),
                Forms\Components\FileUpload::make('sk_terbaru')
                    ->required()
                    ->directory('klub/sk')
                    ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png'])
                    ->label('Upload SK Terbaru yang Sah (PDF/Gambar)'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama_klub')
                    ->searchable()
                    ->sortable()
                    ->label('Nama Klub'),
                Tables\Columns\TextColumn::make('alamat')
                    ->searchable()
                    ->label('Alamat'),
                Tables\Columns\TextColumn::make('pelatih')
                    ->searchable()
                    ->label('Pelatih Kepala'),
                Tables\Columns\TextColumn::make('jumlah_atlet')
                    ->sortable()
                    ->label('Jumlah Atlet'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        $query = parent::getEloquentQuery();
        $user = auth()->user();

        if (!$user) {
            return $query;
        }

        // Klub can only see their own club
        if ($user->role === 'Klub') {
            return $query->where('nama_klub', 'like', '%' . $user->name . '%');
        }

        // Others see all clubs
        return $query;
    }

    public static function canCreate(): bool
    {
        $user = auth()->user();
        return $user && in_array($user->role, ['Super Admin', 'Admin']);
    }

    public static function canEdit(\Illuminate\Database\Eloquent\Model $record): bool
    {
        $user = auth()->user();
        if (!$user) return false;

        if (in_array($user->role, ['Super Admin', 'Admin'])) {
            return true;
        }

        if ($user->role === 'Klub') {
            return str_contains(strtolower($record->nama_klub), strtolower($user->name));
        }

        return false;
    }

    public static function canDelete(\Illuminate\Database\Eloquent\Model $record): bool
    {
        $user = auth()->user();
        return $user && in_array($user->role, ['Super Admin', 'Admin']);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListClubs::route('/'),
        ];
    }
}
