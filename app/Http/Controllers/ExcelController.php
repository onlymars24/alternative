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
        // $tickets = Ticket::where('created_at', '>', $request->comparingDate1)
        // ->where('created_at', '<', $request->comparingDate2)
        // ->where('status', '<>', 'B')
        // ->orderByDesc('id')
        // ->get();

        $tickets = Ticket::where(function ($query) use($request) {
            $query->where('created_at', '>', $request->comparingDate1)
            ->where('created_at', '<', $request->comparingDate2)
            ->where('status', '<>', 'B');
        })
        ->orWhere(function ($query) use($request) {
            $query->where('updated_at', '>', $request->comparingDate1)
            ->where('updated_at', '<', $request->comparingDate2)
            ->where('status', '=', 'R');
        })
        ->orderByDesc('id')
        ->get();
        // dd($tickets);
        return Excel::download(new ReportsExport($tickets, $request), 'reports.xlsx');
    }
}