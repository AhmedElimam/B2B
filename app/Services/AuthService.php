<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use App\Models\Role;

class AuthService
{
    protected $userService;

    public function __construct(UserServices $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Register a new user
     *
     * @param array $data
     * @return array
     * @throws \Exception
     */
    public function register(array $data): array
    {
        try {
            DB::beginTransaction();

            Log::info('Starting user registration', ['email' => $data['email']]);

            // Create user
            $user = $this->userService->createUser($data);
            Log::info('User created successfully', ['user_id' => $user->id]);

            // Generate token
            $token = $user->createToken('auth_token')->plainTextToken;
            Log::info('Token generated successfully', ['user_id' => $user->id]);

            DB::commit();

            return [
                'user' => $user,
                'token' => $token
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Registration failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => array_merge($data, ['password' => '[REDACTED]'])
            ]);
            
            throw $e;
        }
    }

    /**
     * Login a user
     *
     * @param string $email
     * @param string $password
     * @return array
     * @throws \Exception
     */
    public function login(string $email, string $password): array
    {
        try {
            $user = User::where('email', $email)->first();

            if (!$user || !Hash::check($password, $user->password)) {
                throw new \Exception('Invalid credentials');
            }

            // Revoke existing tokens
            $user->tokens()->delete();

            // Generate new token
            $token = $user->createToken('auth_token')->plainTextToken;

            return [
                'user' => $user,
                'token' => $token
            ];

        } catch (\Exception $e) {
            Log::error('Login failed', [
                'error' => $e->getMessage(),
                'email' => $email
            ]);
            
            throw $e;
        }
    }

    /**
     * Logout a user
     *
     * @param User $user
     * @return bool
     */
    public function logout(User $user): bool
    {
        try {
            $user->tokens()->delete();
            return true;
        } catch (\Exception $e) {
            Log::error('Logout failed', [
                'error' => $e->getMessage(),
                'user_id' => $user->id
            ]);
            return false;
        }
    }

    /**
     * Get authenticated user
     *
     * @param User $user
     * @return User
     */
    public function getAuthenticatedUser(User $user): User
    {
        return $user->load('roles');
    }
} 