<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Interfaces\ProfileRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    private ProfileRepositoryInterface $profileRepository;

    public function __construct(ProfileRepositoryInterface $profileRepository)
    {
        $this->profileRepository = $profileRepository;
    }

    public function show()
    {
        return $this->profileRepository->getProfile(Auth::id());
    }

    public function update(ProfileUpdateRequest $request)
    {
        return $this->profileRepository->updateProfile(
            Auth::id(),
            $request->validated(),
            $request->file('avatar')
        );
    }
}
