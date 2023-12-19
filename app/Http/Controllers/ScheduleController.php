<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Kreait\Firebase\Contract\Database;
use Illuminate\Support\Facades\Log;

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
    public function index()
    {
        $schedules = $this->getSchedulesFromFirebase();

        return view('schedules.index', compact('schedules'));
    }

    private function getSchedulesFromFirebase()
    {
        $schedules = [];

        // Assuming you store schedules under a 'schedules' node in Firebase
        $snapshot = $this->database->getReference('schedules')->getSnapshot();

        foreach ($snapshot->getValue() as $date => $timeSlots) {
            $schedule = new \stdClass();
            $schedule->date = $date;
            $schedule->time_slots = $timeSlots;
            $schedules[] = $schedule;
        }

        return $schedules;
    }


    public function store(Request $request)
    {
        try {
            $request->validate([
                'date' => 'required|date',
                'time_slots' => 'required|array',
                'time_slots.*' => 'nullable|date_format:H:i',
            ]);

            $date = $request->input('date');
            $timeSlots = $request->input('time_slots');

            // Retrieve the existing time slots from Firebase
            $existingTimeSlots = $this->database->getReference("schedules/$date")->getSnapshot()->getValue();

            // If there are existing time slots, update them with the new ones
            if ($existingTimeSlots) {
                // Merge existing time slots with the new ones
                $updatedTimeSlots = $existingTimeSlots;
                foreach ($timeSlots as $key => $slot) {
                    $updatedTimeSlots["time_slot_" . ($key + 1)] = $slot;
                }
            } else {
                // If no existing time slots, use the new ones directly with specified names
                $updatedTimeSlots = array_combine(
                    array_map(function ($key) {
                        return "time_slot_$key";
                    }, range(1, count($timeSlots))),
                    $timeSlots
                );
            }

            // Store the updated schedule in Firebase
            $this->database->getReference("schedules/$date")->set($updatedTimeSlots);

            return redirect()->route('schedules.create')->with('message', 'Schedule updated successfully');
        } catch (\Exception $e) {
            Log::error('Firebase Error:', ['message' => $e->getMessage(), 'trace' => $e->getTrace()]);
            // Handle the error appropriately (e.g., return an error response)
            return redirect()->route('schedules.create')->with('error', 'Error occurred while updating schedule');
        }
    }
}
