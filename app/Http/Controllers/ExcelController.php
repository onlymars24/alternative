<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Http\Request;
use App\Exports\ReportsExport;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;

class ExcelController extends Controller
{
    public function export(Request $request){
        $tickets = Ticket::where('created_at', '>', $request->comparingDate1)
        ->where('created_at', '<', $request->comparingDate2)
        ->where('status', '<>', 'B')
        ->orderByDesc('id')
        ->get();
        return Excel::download(new ReportsExport($tickets, $request), 'reports.xlsx');
    }
}