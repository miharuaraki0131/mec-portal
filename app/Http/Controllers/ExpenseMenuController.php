<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ExpenseMenuController extends Controller
{
    /**
     * 経費精算メニュー（ランディングページ）
     */
    public function index()
    {
        return view('expenses.menu');
    }
}
