<?php

declare(strict_types=1);

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    // Note: If you introduce a UserService for complex logic later, 
    // you can override the handleRecordCreation() method here.
    // 
    // protected function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model
    // {
    //     return app(\App\Services\UserService::class)->createUser($data);
    // }
}