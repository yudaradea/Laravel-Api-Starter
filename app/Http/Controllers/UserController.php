<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\UserStoreRequest;
use App\Http\Requests\User\UserUpdatePasswordRequest;
use App\Http\Requests\User\UserUpdateRequest;
use App\Interfaces\UserRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class UserController extends Controller implements HasMiddleware
{
    private UserRepositoryInterface $userRepository;

    public static function middleware(): array
    {
        return [
            new Middleware('permission:view users', only: ['index', 'getAllPaginated', 'show']),
            new Middleware('permission:create users', only: ['store']),
            new Middleware('permission:edit users', only: ['update', 'updatePassword']),
            new Middleware('permission:delete users', only: ['destroy']),
        ];
    }

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
