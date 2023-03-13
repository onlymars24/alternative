<?php

namespace App\Http\Controllers\Api;

use Nette\Utils\DateTime;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DateController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id, Request $request)
    {
        $start = new DateTime($request->start);
        $finish = new DateTime($request->finish);
        $interval = $finish->diff($start);
        $dateStr = '';
        if($interval->d){
            $dateStr .= '%d дн. ';
        }
        if($interval->h){
            $dateStr .= '%h час. ';
        }
        if($interval->i){
            $dateStr .= '%i мин.';
        }
        return $interval->format($dateStr);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
