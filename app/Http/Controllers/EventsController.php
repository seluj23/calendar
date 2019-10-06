<?php

namespace App\Http\Controllers;

use App\Event;
use App\Http\Resources\EventResource;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;

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
        $messages = [];

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'start' => 'required|date',
            'end' => 'required|date|after_or_equal:' . $request->input('start'),
        ]);

        if ($validator->fails()) {
            $start = Carbon::parse($request->input('start'));
            $end = Carbon::parse($request->input('end'));
            if (!$request->input('name')) {
                $messages[] = 'Event title is required.';
            }
            if (!$request->input('start')) {
                $messages[] = 'Date from is required.';
            }
            if (!$request->input('end')) {
                $messages[] = 'Date to is required.';
            }
            if (!(
                $request->input('sunday') or
                $request->input('monday') or
                $request->input('tuesday') or
                $request->input('wednesday') or
                $request->input('thursday') or
                $request->input('friday') or
                $request->input('saturday')
            )){
                $messages[] = 'Atleast one filter day is checked.';
            }
            if (!($start->lessThan($end))) {
                $messages[] = 'Date to is earlier than date from.';
            }

            return response()->json([
                'messages' => $messages,
                'status' => 'error'
            ]);
        }

        if (!(
            $request->input('sunday') or
            $request->input('monday') or
            $request->input('tuesday') or
            $request->input('wednesday') or
            $request->input('thursday') or
            $request->input('friday') or
            $request->input('saturday')
        )){
            return response()->json([
                'messages' =>['Atleast one filter required.'],
                'status' => 'error'
            ]);
        }

        $event = new Event;

        $event->name = $request->input('name');
        $event->start = $request->input('start');
        $event->end = $request->input('end');
        $event->sunday = $request->input('sunday') ? 1 : 0;
        $event->monday = $request->input('monday') ? 1 : 0;
        $event->tuesday = $request->input('tuesday') ? 1 : 0;
        $event->wednesday = $request->input('wednesday') ? 1 : 0;
        $event->thursday = $request->input('thursday') ? 1 : 0;
        $event->friday = $request->input('friday') ? 1 : 0;
        $event->saturday = $request->input('saturday') ? 1 : 0;

        if ($event->save()) {
            return response()->json([
                'data' => new EventResource($event),
                'messages' => ['Successfully added new event!'],
                'status' => 'success'
            ]);
        }
    }
}
