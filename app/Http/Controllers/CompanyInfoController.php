<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CompanyInfoController extends Controller
{
    /**
     * 会社の色々ページ（ランディングページ）
     */
    public function index()
    {
        return view('company-info.index');
    }
}
