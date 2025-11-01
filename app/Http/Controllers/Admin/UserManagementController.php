<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Division;
use App\Traits\LogsActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserManagementController extends Controller
{
    use LogsActivity;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // 管理者のみアクセス可能
        if (auth()->user()->role !== 1) {
            abort(403);
        }

        $query = User::with('division')
            ->where('delete_flg', 0);

        // 検索
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('user_code', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // 部署でフィルタ
        if ($request->has('division_id') && $request->division_id) {
            $query->where('division_id', $request->division_id);
        }

        // ロールでフィルタ
        if ($request->has('role') && $request->role !== '') {
            $query->where('role', $request->role);
        }

        $users = $query->orderBy('name')->paginate(20);
        $divisions = Division::getHierarchical();

        return view('admin.users.index', compact('users', 'divisions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // 管理者のみアクセス可能
        if (auth()->user()->role !== 1) {
            abort(403);
        }

        $divisions = Division::getHierarchical();

        return view('admin.users.create', compact('divisions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // 管理者のみアクセス可能
        if (auth()->user()->role !== 1) {
            abort(403);
        }

        $validated = $request->validate([
            'user_code' => ['required', 'string', 'max:255', 'unique:users'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', 'integer', 'in:0,1,2'],
            'division_id' => ['nullable', 'exists:divisions,id'],
        ]);

        User::create([
            'user_code' => $validated['user_code'],
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'division_id' => $validated['division_id'] ?? null,
            'email_verified_at' => now(),
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', 'ユーザーを登録しました。');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        // 管理者のみアクセス可能
        if (auth()->user()->role !== 1) {
            abort(403);
        }

        $divisions = Division::getHierarchical();

        return view('admin.users.edit', compact('user', 'divisions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        // 管理者のみアクセス可能
        if (auth()->user()->role !== 1) {
            abort(403);
        }

        $validated = $request->validate([
            'user_code' => ['required', 'string', 'max:255', Rule::unique('users')->ignore($user->id)],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'role' => ['required', 'integer', 'in:0,1,2'],
            'division_id' => ['nullable', 'exists:divisions,id'],
        ]);

        $updateData = [
            'user_code' => $validated['user_code'],
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'division_id' => $validated['division_id'] ?? null,
        ];

        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        $user->update($updateData);

        return redirect()->route('admin.users.index')
            ->with('success', 'ユーザーを更新しました。');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        // 管理者のみアクセス可能
        if (auth()->user()->role !== 1) {
            abort(403);
        }

        // 削除前のデータを保存
        $deletedData = $user->toArray();

        // 論理削除
        $user->update(['delete_flg' => 1]);

        // ログ記録
        $this->logDeletion('user', $user->id, $deletedData);

        return redirect()->route('admin.users.index')
            ->with('success', 'ユーザーを削除しました。');
    }
}

