<?php

namespace App\Filament\Resources\AthleteStatResource\Pages;

use App\Filament\Resources\AthleteStatResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAthleteStats extends ListRecords
{
    protected static string $resource = AthleteStatResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('Input Evaluasi Bulanan'),
        ];
    }
}
