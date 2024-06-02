<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use Illuminate\Http\Request;

class ExpensesController extends Controller
{
    public function all(Request $request){
        return response([
            'expenses' => Expense::orderBy('period')->get()
        ]);
    }

    public function one(Request $request){
        return response([
            'expense' => Expense::where('period', $request->period)->first()
        ]);
    }
    
    public function create(Request $request){
        $expense = Expense::create([
            'period' => $request->period,
            'whatsapp' => (int)$request->whatsapp,
            'sms' => (int)$request->sms,
            'server_host' => (int)$request->server_host,
            'ofd_ferma' => (int)$request->ofd_ferma,
        ]);
        return response([
            'expense' => $expense
        ]);
    }

    public function delete(Request $request){
        $expense = Expense::find($request->expenseId);
        $expense->delete();
    }
}