<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Exports\ReportsExport;
use App\Exports\UsersExport;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;

class UsersExportController extends Controller
{
    public function export(Request $request){

        $users = User::orderByDesc('id')->get();
        return Excel::download(new UsersExport($users), 'users.xlsx');
    }
}
