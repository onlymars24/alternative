<?php

namespace App\Http\Controllers;

use App\Models\Sms;
use App\Models\WhatsAppSms;
use Illuminate\Http\Request;
use App\Exports\ReportsExport;
use App\Exports\MessagesExport;
use App\Services\MessageService;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class MessageController extends Controller
{
    public function filter(Request $request){
        return response(['messages' => MessageService::filter($request)]);
    }

    public function exportExcel(Request $request){
        $messages = MessageService::filter($request);
        return Excel::download(new MessagesExport($request->messagesType, $messages, $request->period, $request->types, $request->statuses), 'список_сообщений.xlsx');
    }
}
