<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        if (auth()->user()->is_superAdmin) {
            return view('dashboard.superAdmin.index');
        }
        elseif (auth()->user()->is_admin) {
            return view('dashboard.admin.index');
        }
        else {
            return view('dashboard.reviewer.index');
        }
    }
}
