<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Division;
use App\Models\User;
use App\Traits\LogsActivity;
use Illuminate\Http\Request;

class DivisionManagementController extends Controller
{
    use LogsActivity;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // 管理者のみアクセス可能
        if (auth()->user()->role !== 1) {
            abort(403);
        }

        $divisions = Division::with(['manager', 'parent', 'children'])->orderBy('name')->get();
        $hierarchicalDivisions = Division::getHierarchical();

        return view('admin.divisions.index', compact('divisions', 'hierarchicalDivisions'));
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

        $divisions = Division::whereNull('parent_id')->orderBy('name')->get();
        $users = User::where('delete_flg', 0)->orderBy('name')->get();

        return view('admin.divisions.create', compact('divisions', 'users'));
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
            'name' => ['required', 'string', 'max:255'],
            'parent_id' => ['nullable', 'exists:divisions,id'],
            'manager_id' => ['nullable', 'exists:users,id'],
        ]);

        Division::create([
            'name' => $validated['name'],
            'parent_id' => $validated['parent_id'] ?? null,
            'manager_id' => $validated['manager_id'] ?? null,
        ]);

        return redirect()->route('admin.divisions.index')
            ->with('success', '部署を登録しました。');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Division $division)
    {
        // 管理者のみアクセス可能
        if (auth()->user()->role !== 1) {
            abort(403);
        }

        $divisions = Division::whereNull('parent_id')
            ->where('id', '!=', $division->id)
            ->orderBy('name')
            ->get();
        $users = User::where('delete_flg', 0)->where('role', 2)->orderBy('name')->get();

        return view('admin.divisions.edit', compact('division', 'divisions', 'users'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Division $division)
    {
        // 管理者のみアクセス可能
        if (auth()->user()->role !== 1) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'parent_id' => ['nullable', 'exists:divisions,id', function ($attribute, $value, $fail) use ($division) {
                if ($value == $division->id) {
                    $fail('親部署に自分自身を設定することはできません。');
                }
                // 循環参照チェック（簡易版）
                $children = $division->children;
                foreach ($children as $child) {
                    if ($child->id == $value) {
                        $fail('子部署を親部署に設定することはできません。');
                    }
                }
            }],
            'manager_id' => ['nullable', 'exists:users,id'],
        ]);

        $division->update([
            'name' => $validated['name'],
            'parent_id' => $validated['parent_id'] ?? null,
            'manager_id' => $validated['manager_id'] ?? null,
        ]);

        return redirect()->route('admin.divisions.index')
            ->with('success', '部署を更新しました。');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Division $division)
    {
        // 管理者のみアクセス可能
        if (auth()->user()->role !== 1) {
            abort(403);
        }

        // 子部署が存在する場合は削除不可
        if ($division->children()->count() > 0) {
            return redirect()->route('admin.divisions.index')
                ->with('error', '子部署が存在するため削除できません。先に子部署を削除してください。');
        }

        // ユーザーが所属している場合は削除不可
        if ($division->users()->count() > 0) {
            return redirect()->route('admin.divisions.index')
                ->with('error', '所属ユーザーが存在するため削除できません。先にユーザーの所属を変更してください。');
        }

        // 削除前のデータを保存
        $deletedData = $division->toArray();
        
        $division->delete();

        // ログ記録
        $this->logDeletion('division', $division->id, $deletedData);

        return redirect()->route('admin.divisions.index')
            ->with('success', '部署を削除しました。');
    }
}

