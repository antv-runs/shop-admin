<?php

namespace App\Http\Controllers\Api;

use App\Services\ProfileApiService;
use App\Http\Requests\ProfileApiRequest;
use App\Http\Resources\UserResource;
use Symfony\Component\HttpFoundation\Response;

class ProfileController extends BaseController
{
    private ProfileApiService $profileService;

    public function __construct(ProfileApiService $profileService)
    {
        $this->profileService = $profileService;
    }

    public function edit()
    {
        $user = auth()->user();
        return $this->success(new UserResource($user), 'Edit profile');
    }

    public function update(ProfileApiRequest $request)
    {
        $user = auth()->user();
        $validated = $request->validated();

        if ($request->hasFile('profile_image')) {
            $validated['profile_image'] = $request->file('profile_image');
        }

        $user = $this->profileService->updateProfile($user, $validated);

        return $this->success(new UserResource($user), 'Profile updated successfully');
    }

    public function deleteImage()
    {
        $user = auth()->user();
        $user = $this->profileService->deleteProfileImage($user);

        return $this->success(new UserResource($user), 'Profile image deleted successfully');
    }
}
