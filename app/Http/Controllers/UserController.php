<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\UserStoreRequest;
use App\Http\Requests\User\UserUpdatePasswordRequest;
use App\Http\Requests\User\UserUpdateRequest;
use App\Interfaces\UserRepositoryInterface;
use Illuminate\Http\Request;

class UserController extends Controller
{
    private UserRepositoryInterface $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Display a listing of users
     */
    public function index(Request $request)
    {
        $perPage = $request->query('per_page', 10);
        $search = $request->query('search', '');
        
        return $this->userRepository->index($perPage, $search);
    }

    /**
     * Display a listing of users with different pagination format
     */
    public function getAllPaginated(Request $request)
    {
        $perPage = $request->query('per_page', 10);
        $search = $request->query('search', '');
        
        return $this->userRepository->getAllPaginated($perPage, $search);
    }

    /**
     * Store a newly created user
     */
    public function store(UserStoreRequest $request)
    {
        return $this->userRepository->store($request->validated());
    }

    /**
     * Display the specified user
     */
    public function show($id)
    {
        return $this->userRepository->show($id);
    }

    /**
     * Update the specified user
     */
    public function update(UserUpdateRequest $request, $id)
    {
        return $this->userRepository->update($request->validated(), $id);
    }

    /**
     * Remove the specified user
     */
    public function destroy($id)
    {
        return $this->userRepository->destroy($id);
    }

    /**
     * Update user password
     */
    public function updatePassword(UserUpdatePasswordRequest $request, $id)
    {
        return $this->userRepository->updatePassword($request->validated(), $id);
    }
}
