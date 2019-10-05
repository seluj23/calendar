<?php

namespace App\Http\Controllers;

use App\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class EventsController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $event = new Event;

        $event->name = $request->input('name');
        $event->start = $request->input('start');
        $event->end = $request->input('end');
        $event->sunday = $request->input('sunday');
        $event->monday = $request->input('monday');
        $event->tuesday = $request->input('tuesday');
        $event->wednesday = $request->input('wednesday');
        $event->thursday = $request->input('thursday');
        $event->friday = $request->input('friday');
        $event->saturday = $request->input('saturday');

        if ($event->save()) {
            return response()->json([
                'data' => $this->generateDates($event)
            ], 200);
        }
    }

    /**
     * Generate filtered days.
     *
     * @param  \App\Event;  $event
     * @return Array
     */
    private function generateDates($event)
    {
        $dates = [];

        $start = Carbon::parse($event->start);
        $end = Carbon::parse($event->end);
        $period = $start->diffInDays($end);

        $week = [
            $event->sunday, 
            $event->monday, 
            $event->tuesday, 
            $event->wednesday, 
            $event->thursday, 
            $event->friday, 
            $event->saturday
        ];

        for ($i=0; $i <= $period; $i++) {
            $j = ($i + $start->dayOfWeek) % 7;
            if ($week[$j]) {
                $dates[] = $start->copy()->addDay($i);
            }
        }

        return $dates;
    }
}
