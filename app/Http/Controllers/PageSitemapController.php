<?php

namespace App\Http\Controllers;

use App\Models\SitemapPage;
use Illuminate\Http\Request;

class PageSitemapController extends Controller
{
    public function checkUrl(Request $request){
        return response(['page' => SitemapPage::where('url', $request->url)->first()]);
    }
}