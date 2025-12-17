<?php

namespace App\Interfaces;

interface AuthRepositoryInterface
{
    /**
     * Login user
     *
     * @param array $credentials
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(array $credentials);

    /**
     * Register user
     *
     * @param array $data
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(array $data);

    /**
     * Logout user
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout();

    /**
     * Get authenticated user
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me();
}
