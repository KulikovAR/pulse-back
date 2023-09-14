<?php

namespace App\Filament\Resources\FaqResource\Pages;

use App\Filament\BaseEditAction;
use App\Filament\Resources\FaqResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditFaq extends BaseEditAction
{
    protected static string $resource = FaqResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
