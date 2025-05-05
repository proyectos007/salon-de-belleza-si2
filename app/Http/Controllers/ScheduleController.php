<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use App\Models\User;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    public function store (Request $request)
    {
        $validated = $request->validate([
            'start_hour' => 'required',
            'end_hour' => 'required',
            'name' => 'required|string|max:255|unique:schedules,name',
        ]);

        $schedule = Schedule::create($validated);

        return response()->json($schedule, 201);
    }

    public function getList () 
    {
        $schedules = Schedule::all();

        return response()->json($schedules);
    }

    public function updateUser (Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'schedule_id' => 'required|exists:schedules,id',
        ]);

        $user = User::find($validated['user_id']);
        $user->schedule_id = $validated['schedule_id'];
        $user->save();

        return response()->json($user);
    }
}
