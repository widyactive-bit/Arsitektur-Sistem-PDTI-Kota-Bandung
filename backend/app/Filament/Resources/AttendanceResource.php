<?php

namespace App\Filament\Resources;

use App\Models\Attendance;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use App\Filament\Resources\AttendanceResource\Pages;

class AttendanceResource extends Resource
{
    protected static ?string $model = Attendance::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?string $navigationLabel = 'Kehadiran / Absensi';

    protected static ?string $pluralLabel = 'Absensi';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Data Atlet & Waktu')
                    ->schema([
                        Forms\Components\Select::make('athlete_id')
                            ->relationship('athlete', 'nama_lengkap')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->label('Atlet'),
                        Forms\Components\DateTimePicker::make('checkin_time')
                            ->required()
                            ->label('Waktu Check In'),
                        Forms\Components\DateTimePicker::make('checkout_time')
                            ->label('Waktu Check Out'),
                        Forms\Components\TextInput::make('duration')
                            ->numeric()
                            ->suffix('menit')
                            ->label('Durasi Latihan'),
                    ])->columns(2),

                Forms\Components\Section::make('Validasi Lokasi & Kehadiran')
                    ->schema([
                        Forms\Components\TextInput::make('latitude')
                            ->numeric()
                            ->required()
                            ->label('Latitude GPS'),
                        Forms\Components\TextInput::make('longitude')
                            ->numeric()
                            ->required()
                            ->label('Longitude GPS'),
                        Forms\Components\TextInput::make('qr_code_data')
                            ->label('Data Scan QR Code')
                            ->placeholder('Arahkan scanner QR ke sini...')
                            ->columnSpanFull(),
                        Forms\Components\FileUpload::make('selfie')
                            ->image()
                            ->directory('selfies')
                            ->required()
                            ->label('Foto Selfie Laporan'),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('athlete.nama_lengkap')
                    ->searchable()
                    ->sortable()
                    ->label('Nama Atlet'),
                Tables\Columns\TextColumn::make('checkin_time')
                    ->dateTime()
                    ->sortable()
                    ->label('Check In'),
                Tables\Columns\TextColumn::make('checkout_time')
                    ->dateTime()
                    ->label('Check Out'),
                Tables\Columns\TextColumn::make('duration')
                    ->suffix(' menit')
                    ->label('Durasi'),
                Tables\Columns\TextColumn::make('coordinates')
                    ->getStateUsing(fn ($record) => $record->latitude . ', ' . $record->longitude)
                    ->label('GPS Koordinat'),
                Tables\Columns\ImageColumn::make('selfie')
                    ->circular()
                    ->label('Selfie'),
            ])
            ->filters([
                Tables\Filters\Filter::make('checkin_time')
                    ->form([
                        Forms\Components\DatePicker::make('checkin_time')
                            ->label('Tanggal Check In'),
                    ])
                    ->query(function ($query, $data) {
                        return $query->when($data['checkin_time'], fn ($query, $date) => $query->whereDate('checkin_time', $date));
                    }),
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

        // Super Admin, Admin, Pengurus, Pelatih see all
        if (in_array($user->role, ['Super Admin', 'Admin', 'Pengurus', 'Pelatih'])) {
            return $query;
        }

        // Atlet can only see their own attendance
        if ($user->role === 'Atlet') {
            return $query->whereHas('athlete', function ($q) use ($user) {
                $q->where('email', $user->email);
            });
        }

        // Klub can only see attendance of athletes in their club
        if ($user->role === 'Klub') {
            return $query->whereHas('athlete', function ($q) use ($user) {
                $q->where('klub', 'like', '%' . $user->name . '%');
            });
        }

        // Others see nothing
        return $query->whereRaw('1 = 0');
    }

    public static function canViewAny(): bool
    {
        $user = auth()->user();
        // Wasit cannot view attendance
        return $user && $user->role !== 'Wasit';
    }

    public static function canCreate(): bool
    {
        $user = auth()->user();
        return $user && in_array($user->role, ['Super Admin', 'Admin', 'Pelatih', 'Atlet']);
    }

    public static function canEdit(\Illuminate\Database\Eloquent\Model $record): bool
    {
        $user = auth()->user();
        return $user && in_array($user->role, ['Super Admin', 'Admin', 'Pelatih']);
    }

    public static function canDelete(\Illuminate\Database\Eloquent\Model $record): bool
    {
        $user = auth()->user();
        return $user && in_array($user->role, ['Super Admin', 'Admin']);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAttendances::route('/'),
        ];
    }
}
