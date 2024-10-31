<?php

namespace App\Broadcasting;

use App\Enums\RolesEnum;
use App\Models\User;

class AdminChannel
{
    /**
     * Create a new channel instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Authenticate the user's access to the channel.
     */
    public function join(User $user): array|bool
    {
        logs()->info('[' . self::class . ']: Email=' . $user->email . ' => ' . $user->hasRole(RolesEnum::ADMIN->value));

        return $user->hasRole(RolesEnum::ADMIN->value);
    }
}
