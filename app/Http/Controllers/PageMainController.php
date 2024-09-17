<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class PageMainController extends Controller
{
    public function get(){
        $pageMain = Setting::where('name', 'pageMain')->first();
        $pageMain = json_decode($pageMain->data);
        return response([
            'pageMain' => $pageMain->content
        ]);
    }

    public function edit(Request $request){
        $pageMain = Setting::where('name', 'pageMain')->first();
        $data = json_decode($pageMain->data);
        $data->content = $request->content;
        $pageMain->data = json_encode($data);
        $pageMain->save();
        return response([
            'pageMain' => $pageMain
        ]);
    }

    public function mainPages(){
        $pagesOnMain = Setting::where('name', 'pagesOnMain')->first();
        $pagesOnMain = json_decode($pagesOnMain->data);
        return response([
            'pagesOnMain' => $pagesOnMain
        ]);
    }    
}