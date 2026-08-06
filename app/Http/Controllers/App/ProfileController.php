<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateProfileRequest;
use App\Http\Resources\UserResource;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{
    public function edit(Request $request): Response
    {
        return Inertia::render('Profile/Edit', [
            'profile' => UserResource::make($request->user())->resolve(),
        ]);
    }

    public function update(UpdateProfileRequest $request): RedirectResponse
    {
        $user = $request->user();
        $data = $request->validated();

        if ($request->hasFile('profile_picture')) {
            $data['profile_picture'] = $request->file('profile_picture')->store('avatars', 'public');
        } else {
            unset($data['profile_picture']);
        }

        if (empty($data['password'])) {
            unset($data['password']);
        }
        unset($data['current_password']);

        $user->update($data);

        return redirect()->route('profile.edit')->with('success', 'Profile updated');
    }
}
