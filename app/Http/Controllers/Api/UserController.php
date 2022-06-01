<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\PermissionResource;
use App\Http\Resources\UserResource;
use App\Models\Acl;
use App\Models\Log;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

/**
 * Class UserController
 *
 * @package App\Http\Controllers\Api
 */
class UserController extends BaseController
{
    /**
     * Display a listing of the user resource.
     *
     * @param Request $request
     * @return AnonymousResourceCollection
     */
    public function index(Request $request)
    {
        $params = $request->all();

        $data = User::query()
            ->when(!empty($params['role']), function (Builder $query) use ($params) {
                $query->whereHas('roles', function ($q) use ($params) {
                    $q->where('name', $params['role']);
                });
            })
            ->when(!empty($params['keyword']), function (Builder $query) use ($params) {
                $query->where(function ($q) use ($params) {
                    $q->where('name', 'like', '%' . $params['keyword'] . '%')
                        ->orWhere('email', 'like', '%' . $params['keyword'] . '%');
                });
            })
            ->paginate($params['per_page'] ?? 10);

        return UserResource::collection($data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     */
    public function store(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            array_merge(
                $this->getValidationRules(),
                [
                    'password' => ['required', 'min:6'],
                    'confirmPassword' => 'same:password',
                ]
            )
        );

        if ($validator->fails()) {
            return responseFailed($validator->errors()->first(), Response::HTTP_BAD_REQUEST);
        } else {
            try {
                $params = $request->all();
                DB::beginTransaction();
                $user = User::create([
                    'name' => $params['name'],
                    'email' => $params['email'],
                    'password' => Hash::make($params['password']),
                    'sex' => $params['sex'],
                    'birthday' => $params['birthday'] ?? null,
                    'description' => $params['description'] ?? ''
                ]);
                $role = Role::findByName($params['role']);
                $user->syncRoles($role);
                $loginUser = Auth::user();

                Log::query()->create([
                    'user_id' => $user->id,
                    'operator_id' => $loginUser->id,
                    'title' => 'Create',
                    'content' => "Created by {$loginUser->name}({$loginUser->email})"
                ]);
                DB::commit();

                return new UserResource($user);
            } catch (\Exception $ex) {
                DB::rollBack();
                return responseFailed($ex->getMessage());
            }
        }
    }

    /**
     * Display the specified resource.
     *
     * @param User $user
     * @return UserResource|\Illuminate\Http\JsonResponse
     */
    public function show(User $user)
    {
        return new UserResource($user);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param User $user
     * @return UserResource|\Illuminate\Http\JsonResponse
     */
    public function update(Request $request, User $user)
    {
        if ($user === null) {
            return response()->json(['error' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        $currentUser = Auth::user();
        if (!$currentUser->isAdmin()
            && $currentUser->id !== $user->id
            && !$currentUser->hasPermission(Acl::PERMISSION_USER_MANAGE)
        ) {
            return response()->json(['error' => 'Permission denied'], Response::HTTP_BAD_REQUEST);
        }

        $validator = Validator::make($request->all(), $this->getValidationRules(false));
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 403);
        }

        $user->sex = intval($request->input('sex'));
        $user->birthday = $request->input('birthday');
        $user->description = $request->input('description');
        $user->save();
        return new UserResource($user);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param User $user
     * @return UserResource|\Illuminate\Http\JsonResponse
     */
    public function updatePermissions(Request $request, User $user)
    {
        if (empty($user)) {
            return responseFailed('User not found', Response::HTTP_NOT_FOUND);
        }

        if ($user->isAdmin()) {
            return responseFailed('Admin can not be modified', Response::HTTP_BAD_REQUEST);
        }

        $permissionIds = $request->get('permissions', []);
        $rolePermissionIds = array_map(
            function ($permission) {
                return $permission['id'];
            },

            $user->getPermissionsViaRoles()->toArray()
        );

        $newPermissionIds = array_diff($permissionIds, $rolePermissionIds);
        $permissions = Permission::allowed()->whereIn('id', $newPermissionIds)->get();
        $user->syncPermissions($permissions);
        return new UserResource($user);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param User $user
     */
    public function destroy(User $user)
    {
        if ($user->isAdmin()) {
            return responseFailed('Ehhh! Can not delete admin user', Response::HTTP_FORBIDDEN);
        }

        try {
            $user->delete();
        } catch (\Exception $ex) {
            return responseFailed($ex->getMessage(), Response::HTTP_FORBIDDEN);
        }

        return responseSuccess();
    }

    /**
     * Get permissions from role
     *
     * @param User $user
     */
    public function permissions(User $user)
    {
        try {
            return responseSuccess([
                'user' => PermissionResource::collection($user->getDirectPermissions()),
                'role' => PermissionResource::collection($user->getPermissionsViaRoles()),
            ]);
        } catch (\Exception $ex) {
            return responseFailed($ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @param bool $isNew
     * @return array
     */
    private function getValidationRules(bool $isNew = true): array
    {
        return [
            'name' => $isNew ? 'required|unique:users' : '',
            'email' => $isNew ? 'required|email|unique:users' : '',
            'role' => $isNew ? [
                'required',
                Rule::notIn([Acl::ROLE_ADMIN])
            ] : '',
            'sex' => [
                'required',
                Rule::in([0, 1])
            ],
            'birthday' => 'date_format:Y-m-d H:i:s',
            'description' => 'max:255'
        ];
    }
}
