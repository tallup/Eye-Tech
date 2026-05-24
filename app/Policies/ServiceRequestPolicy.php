<?php

namespace App\Policies;

use App\Models\ServiceRequest;
use App\Models\User;

class ServiceRequestPolicy
{
    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['admin', 'cashier'], true);
    }

    public function view(User $user, ServiceRequest $serviceRequest): bool
    {
        return in_array($user->role, ['admin', 'cashier'], true);
    }

    public function create(User $user): bool
    {
        return $user->role === 'admin';
    }

    public function update(User $user, ServiceRequest $serviceRequest): bool
    {
        return $user->role === 'admin';
    }

    public function delete(User $user, ServiceRequest $serviceRequest): bool
    {
        return $user->role === 'admin';
    }
}
