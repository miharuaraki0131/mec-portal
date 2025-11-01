<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Division;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     * 社員紹介ページ（部署・課ごとに表示）
     */
    public function index(Request $request)
    {
        $query = User::with('division.parent')
            ->where('delete_flg', 0)
            ->orderBy('name');

        // 部署でフィルタリング
        if ($request->has('division_id') && $request->division_id) {
            $query->where('division_id', $request->division_id);
        }

        $users = $query->get();
        
        // 部署ごとにグループ化
        $usersByDivision = $users->groupBy(function ($user) {
            if ($user->division) {
                return $user->division->parent 
                    ? $user->division->parent->name . ' > ' . $user->division->name
                    : $user->division->name;
            }
            return '未所属';
        })->sortKeys();

        // 部署一覧（フィルター用）
        $divisions = Division::getHierarchical();

        return view('users.index', compact('usersByDivision', 'divisions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        //
    }
}
