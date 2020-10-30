<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class EventResource extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $days = [0,0,0,0,0,0,0];

        for ($i=0; $i < 7; $i++) { 
            $base = 2**(6-$i);
            $days[$i] = ($base & $this->active_days) == $base ? 1 : 0;
        }

        return [
            'id' => $this->id,
            'title' => $this->name,
            'start' => $this->start,
            'end' => $this->end,
            'sunday' => $days[0],
            'monday' => $days[1],
            'tuesday' => $days[2],
            'wednesday' => $days[3],
            'thursday' => $days[4],
            'friday' => $days[5],
            'saturday' => $days[6],
        ];
    }
}
