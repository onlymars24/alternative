<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\BusStation;
use Illuminate\Http\Request;

class EventController extends Controller
{

    public function all(Request $request){
        return response([
            'events' => Event::all()
        ]);
    }

    public function one(Request $request){
        $event = Event::find($request->id);
        $stations = $event->bus_stations;
        return response([
            'event' => $event,
            'stations' => $stations,
        ]);
    }

    public function stationOnes(Request $request){
        $station = BusStation::find($request->id);
        return response([
            'events' => $station->events
        ]);
    }

    public function create(Request $request){
        $event = Event::create([
            'title' => $request->title,
            'descr' => $request->descr,
            'content' => $request->content,
            'date' => $request->date,
        ]);
        if($request->stations){
            foreach($request->stations as $stationId){
                $event->bus_stations()->attach($stationId);
            }
        }
        
        
        // if ($request->hasFile('image')){
        //     $image = $request->file('image');
        //     return response()->json(['message' => $image->store('events')], 200);
        // } else {
        // return response()->json(['message' => 'No image uploaded'], 400);
        // }
        //ЗАГРУЗКА КАРТИНКИ

        return response([
            'event' => $event
        ]);
    }

    public function edit(Request $request){
        $event = Event::find($request->id);
        $event->title = $request->title;
        $event->descr = $request->descr;
        $event->content = $request->content;
        $event->date = $request->date;
        // $event->hidden = $request->hidden;
        //ЗАГРУЗКА КАРТИНКИ
        $event->save();
        return response([
            'event' => $event
        ]);
    }

    public function delete(Request $request){
        $event = Event::find($request->id);
        $event->delete();
    }

    // public function deleteImage(Request $request){
    //     $event = Event::find($request->id);
    //     //УДАЛЕНИЕ КАРТИНКИ
    //     $event->save();
    // }



    //stationId
    //eventId
    public function addStation(Request $request){
        $event = Event::find($request->eventId);
        if(!$event->bus_stations->where('id', $request->stationId)->first()) {
            $event->bus_stations()->attach($request->stationId);
        }
    }

    //stationId
    //eventId
    public function deleteStation(Request $request){
        $event = Event::find($request->eventId);
        if($event->bus_stations->where('id', $request->stationId)->first()) {
            $event->bus_stations()->detach($request->stationId);
        }
    }
}