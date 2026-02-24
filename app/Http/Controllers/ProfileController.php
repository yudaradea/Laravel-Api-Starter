<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Interfaces\ProfileRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller implements HasMiddleware
{
    private ProfileRepositoryInterface $profileRepository;

    public static function middleware(): array
    {
        return [
            new Middleware('permission:view own profile', only: ['show']),
            new Middleware('permission:edit own profile', only: ['update']),
        ];
    }

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
