<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\ModuleManagerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * @OA\Get(
     *     path="/users",
     *     tags={"System Users"},
     *     summary="List all system users",
     *     @OA\Response(response=200, description="Successful operation", @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/User")))
     * )
     */
    public function index()
    {
        $users = User::latest()->paginate(15);
        return view('admin.users.index', compact('users'));
    }

    /**
     * @OA\Get(
     *     path="/admin/users/create",
     *     tags={"System Users"},
     *     summary="Show user creation form",
     *     @OA\Response(response=200, description="Successful operation")
     * )
     */
    public function create()
    {
        $moduleManager = app(ModuleManagerService::class);
        $allModules = $moduleManager->getAllModules();
        return view('admin.users.create', compact('allModules'));
    }

    /**
     * @OA\Post(
     *     path="/users",
     *     tags={"System Users"},
     *     summary="Create a new system user",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/User")
     *     ),
     *     @OA\Response(response=201, description="User created successfully")
     * )
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role' => 'required|string',
            'permissions' => 'nullable|array',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'permissions' => $request->permissions,
        ]);

        return redirect()->route('admin.users.index')->with('success', 'Nhân viên đã được tạo thành công.');
    }

    /**
     * @OA\Get(
     *     path="/admin/users/{user}/edit",
     *     tags={"System Users"},
     *     summary="Show user edit form",
     *     @OA\Parameter(name="user", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Successful operation")
     * )
     */
    public function edit(User $user)
    {
        $moduleManager = app(ModuleManagerService::class);
        $allModules = $moduleManager->getAllModules();
        return view('admin.users.edit', compact('user', 'allModules'));
    }

    /**
     * @OA\Put(
     *     path="/users/{user}",
     *     tags={"System Users"},
     *     summary="Update a system user",
     *     @OA\Parameter(name="user", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/User")
     *     ),
     *     @OA\Response(response=200, description="User updated successfully")
     * )
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'role' => 'required|string',
            'permissions' => 'nullable|array',
        ]);

        $user->name = $request->name;
        $user->email = $request->email;
        $user->role = $request->role;
        $user->permissions = $request->permissions;

        if ($request->password) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return redirect()->route('admin.users.index')->with('success', 'Thông tin nhân viên đã được cập nhật.');
    }

    /**
     * @OA\Delete(
     *     path="/users/{user}",
     *     tags={"System Users"},
     *     summary="Delete a system user",
     *     @OA\Parameter(name="user", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="User deleted successfully")
     * )
     */
    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Bạn không thể tự xóa chính mình.');
        }

        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'Đã xóa nhân viên.');
    }
}
