<?php

namespace App\Http\Controllers;

use App\Models\Event;
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

        $data = $request->all();

        $validator = Validator::make($data, [
            'name'  => 'required',
            'start' => 'required|date',
            'end'   => 'required|date|after_or_equal:' . $request->input('start'),
        ]);

        if ($validator->fails()) {
            $start = Carbon::parse($request->input('start'));
            $end = Carbon::parse($request->input('end'));
            if (!$data['name']) {
                $messages[] = 'Event title is required.';
            }
            if (!$data['start']) {
                $messages[] = 'Date from is required.';
            }
            if (!$data['end']) {
                $messages[] = 'Date to is required.';
            }
            if (!($start->lessThan($end))) {
                $messages[] = 'Date to is earlier than date from.';
            }

            return response()->json([
                'messages' => $messages,
                'status' => 'error'
            ]);
        }

        $active_days = 0;

        if ($data['sunday']) {
            $active_days += 64;
        }
        if ($data['monday']) {
            $active_days += 32;
        }
        if ($data['tuesday']) {
            $active_days += 16;
        }
        if ($data['wednesday']) {
            $active_days += 8;
        }
        if ($data['thursday']) {
            $active_days += 4;
        }
        if ($data['friday']) {
            $active_days += 2;
        }
        if ($data['saturday']) {
            $active_days += 1;
        }

        if ($active_days == 0) {
            return response()->json([
                'messages' => ['Atleast one filter required.'],
                'status' => 'error'
            ]);
        }

        try {
            $event = new Event;

            $event->name = $data['name'];
            $event->start = $data['start'];
            $event->end = $data['end'];
            $event->active_days = $active_days;
            $event->save();

            return response()->json([
                'data' => new EventResource($event),
                'messages' => ['Successfully added new event!'],
                'status' => 'success'
            ]);
        } catch (\Exception $ex) {
            return response()->json([
                'messages' => ['Failed to add new event, please try again.'],
                'status' => 'error'
            ]);
        }
    }
}
