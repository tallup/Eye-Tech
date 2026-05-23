<?php

namespace App\Policies;

use App\Models\Sales;
use App\Models\User;

class SalePolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Sales $sale): bool
    {
        return $user->role === 'admin' || $sale->user_id === $user->id;
    }

    public function void(User $user, Sales $sale): bool
    {
        return $user->role === 'admin';
    }
}
