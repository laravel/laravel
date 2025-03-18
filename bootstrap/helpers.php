<?php

use App\Models\User;

/**
 * Get the currently authenticated user model.
 */
function user(): User
{
    $currentUser = auth()->user();

    if ($currentUser === null) {
        throw new Exception('The current user is not authenticated.');
    }

    if (! $currentUser instanceof User) {
        throw new Exception('The currently authenticated user is not an instance of '.User::class);
    }

    return $currentUser;
}
