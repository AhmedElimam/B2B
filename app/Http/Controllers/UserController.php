<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Services\UserServices;
use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Http\Requests\User\RoleRequest;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class UserController extends Controller
{
    protected $userService;

    public function __construct(UserServices $userService)
    {
        $this->userService = $userService;
    }

    public function index(): AnonymousResourceCollection
    {
        $users = $this->userService->getAllUsers();
        return UserResource::collection($users);
    }

    public function show(int $id): UserResource
    {
        $user = $this->userService->getUserById($id);
        return new UserResource($user);
    }

    public function store(StoreUserRequest $request): JsonResponse
    {
        $user = $this->userService->createUser($request->validated());
        return response()->json([
            'status' => 'success',
            'message' => 'User created successfully',
            'data' => new UserResource($user)
        ], 201);
    }

    public function update(UpdateUserRequest $request, int $id): JsonResponse
    {
        $user = $this->userService->updateUser($id, $request->validated());
        return response()->json([
            'status' => 'success',
            'message' => 'User updated successfully',
            'data' => new UserResource($user)
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->userService->deleteUser($id);
        return response()->json([
            'status' => 'success',
            'message' => 'User deleted successfully'
        ]);
    }

    public function assignRole(RoleRequest $request, int $id): JsonResponse
    {
        $user = $this->userService->assignRole($id, $request->validated()['role_id']);
        return response()->json([
            'status' => 'success',
            'message' => 'Role assigned successfully',
            'data' => new UserResource($user)
        ]);
    }

    public function removeRole(RoleRequest $request, int $id): JsonResponse
    {
        $user = $this->userService->removeRole($id, $request->validated()['role_id']);
        return response()->json([
            'status' => 'success',
            'message' => 'Role removed successfully',
            'data' => new UserResource($user)
        ]);
    }
}
