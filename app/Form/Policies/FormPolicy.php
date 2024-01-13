<?php

namespace App\Form\Policies;

use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Spatie\MediaLibrary\HasMedia;

class FormPolicy
{
    use HandlesAuthorization;

    public function listMedia(User $user, HasMedia $model): bool
    {
        return true;
    }

    public function storeMedia(User $user, ?HasMedia $model, ?string $collection = null): bool
    {
        return true;
    }

    public function updateMedia(User $user, HasMedia $model, string $collection): bool
    {
        return true;
    }

    public function destroyMedia(User $user, HasMedia $model, string $collection): bool
    {
        return true;
    }
}
