<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Contracts\ProfileServiceInterface;
use App\Http\Requests\ProfileApiRequest;
use App\DTOs\UpdateUserProfileDTO;

class ProfileController extends Controller
{
    private ProfileServiceInterface $profileService;

    public function __construct(ProfileServiceInterface $profileService)
    {
        $this->profileService = $profileService;
    }

    /**
     * Show the admin's profile.
     */
    public function show()
    {
        $user = auth()->user();
        return view('admin.profile.show', compact('user'));
    }

    /**
     * Show the edit profile form.
     */
    public function edit()
    {
        $user = auth()->user();
        return view('admin.profile.edit', compact('user'));
    }

    /**
     * Update the admin's profile information.
     */
    public function update(ProfileApiRequest $request)
    {
        $user = auth()->user();
        $validated = $request->validated();

        // Handle image file
        if ($request->hasFile('profile_image')) {
            $validated['profile_image'] = $request->file('profile_image');
        } else {
            unset($validated['profile_image']);
        }

        // Create DTO from validated data
        $dto = UpdateUserProfileDTO::fromArray($validated);

        $this->profileService->updateProfile($user, $dto);

        return redirect()->route('admin.profile.show')->with('success', 'Profile updated successfully!');
    }

    /**
     * Delete the admin's profile image.
     */
    public function deleteImage()
    {
        $user = auth()->user();
        $this->profileService->deleteProfileImage($user);

        return back()->with('success', 'Profile image deleted successfully!');
    }
}
