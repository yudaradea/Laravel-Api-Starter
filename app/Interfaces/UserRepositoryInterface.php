<?php

namespace App\Interfaces;

interface UserRepositoryInterface
{
    /**
     * Get all users with pagination
     *
     * @param int $perPage
     * @param string $search
     * @return \Illuminate\Http\JsonResponse
     */
    public function index($perPage, $search);

    /**
     * Get all users with pagination (different format)
     *
     * @param int $perPage
     * @param string $search
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAllPaginated($perPage, $search);

    /**
     * Store new user
     *
     * @param array $data
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(array $data);

    /**
     * Show user detail
     *
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id);

    /**
     * Update user
     *
     * @param array $data
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(array $data, $id);

    /**
     * Delete user
     *
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id);

    /**
     * Update user password
     *
     * @param array $data
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function updatePassword(array $data, $id);
}
