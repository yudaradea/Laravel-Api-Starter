<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginStoreRequest;
use App\Http\Requests\RegisterStoreRequest;
use App\Interfaces\AuthRepositoryInterface;

class AuthController extends Controller
{
    private AuthRepositoryInterface $authRepository;

    public function __construct(AuthRepositoryInterface $authRepository)
    {
        $this->authRepository = $authRepository;
    }

    /**
     * Login user
     */
    public function login(LoginStoreRequest $request)
    {
        return $this->authRepository->login($request->validated());
    }

    /**
     * Register user
     */
    public function register(RegisterStoreRequest $request)
    {
        return $this->authRepository->register($request->validated());
    }

    /**
     * Logout user
     */
    public function logout()
    {
        return $this->authRepository->logout();
    }

    /**
     * Get authenticated user
     */
    public function me()
    {
        return $this->authRepository->me();
    }
}
