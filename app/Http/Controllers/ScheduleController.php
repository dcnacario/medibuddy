<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Kreait\Firebase\Contract\Database;

class ScheduleController extends Controller
{
    protected $database;

    public function __construct(Database $database)
    {
        $this->database = $database;
    }

    public function create()
    {
        return view('schedules.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'time_slots.*' => 'required|date_format:H:i',
        ]);

        $date = $request->input('date');
        $timeSlots = $request->input('time_slots');

        // Store the new schedule in Firebase
        $this->database->getReference("schedules/$date")->set($timeSlots);

        return redirect()->route('schedules.create')->with('message', 'Schedule created successfully');
    }
}
