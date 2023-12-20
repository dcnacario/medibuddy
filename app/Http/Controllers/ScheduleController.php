<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Kreait\Firebase\Contract\Database;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;

class ScheduleController extends Controller
{
    protected $database;

    public function __construct(Database $database)
    {
        $this->database = $database;
    }

    public function create()
    {
        try {
            // Get the current date
            $currentDate = Carbon::now()->format('Y-m-d');

            // Fetch schedules for the current date from Firebase
            $scheduleForToday = $this->formatTime($this->getSchedulesForDate($currentDate));

            $allSchedules = $this->getSchedulesFromFirebase();

            return view('schedules.create', compact('scheduleForToday', 'allSchedules'));
        } catch (\Exception $e) {
            Log::error('Firebase Error:', ['message' => $e->getMessage(), 'trace' => $e->getTrace()]);
            // Handle the error appropriately (e.g., return an error response)
            return redirect()->back()->with('error', 'Error occurred while fetching today\'s schedule');
        }
    }

    private function getSchedulesForDate($date)
    {
        $schedule = [];

        // Assuming you store schedules under a 'schedules' node in Firebase
        $snapshot = $this->database->getReference("schedules/$date")->getSnapshot();

        if ($snapshot->exists()) {
            foreach ($snapshot->getValue() as $timeSlot) {
                $schedule[] = [
                    'label' => $timeSlot['label'] ?? null,
                    'time' => $timeSlot['time'] ?? null,
                ];
            }
        }

        return $schedule;
    }

    private function getSchedulesFromFirebase()
    {
        $schedules = [];

        // Assuming you store schedules under a 'schedules' node in Firebase
        $snapshot = $this->database->getReference('schedules')->getSnapshot();

        foreach ($snapshot->getValue() as $date => $timeSlots) {
            $schedule = new \stdClass();
            $schedule->date = $date;
            $schedule->time_slots = $this->formatTime($timeSlots);
            $schedules[] = $schedule;
        }

        return $schedules;
    }

    private function formatTime($timeSlots)
    {
        foreach ($timeSlots as &$timeSlot) {
            if (isset($timeSlot['time'])) {
                $timeSlot['time'] = Carbon::createFromFormat('H:i', $timeSlot['time'])->format('h:i A');
            }
        }

        return $timeSlots;
    }


    public function store(Request $request)
    {
        try {
            $request->validate([
                'date' => [
                    'required',
                    'date',
                    'after_or_equal:' . Carbon::now()->format('Y-m-d'),
                    function ($attribute, $value, $fail) use ($request) {
                        // Check uniqueness in Firebase RTDB
                        $date = $request->input('date');
                        $existingData = $this->database->getReference("schedules/$date")->getSnapshot()->exists();

                        if ($existingData) {
                            $fail('The ' . $attribute . ' has already been taken.');
                        }
                    },
                ],
                'time_slots' => 'required|array',
                'time_slots.*' => 'nullable|date_format:H:i',
            ]);


            $date = $request->input('date');
            $timeSlots = $request->input('time_slots');
            $labelTimeSlots = $request->input('label_time_slots');

            // Check if the schedule for the given date already exists
            $scheduleExists = $this->database->getReference("schedules/$date")->getSnapshot()->exists();

            // Check if the selected date is today
            $selectedDate = Carbon::parse($request->input('date'))->format('Y-m-d');
            $today = Carbon::now()->format('Y-m-d');

            if ($selectedDate == $today) {
                // Handle the case where the selected date is today
                return redirect()->route('schedules.create')->with('error', 'You cannot schedule for today. Please select a future date.');
            }

            // Check if the schedule for the selected date already exists
            $scheduleExists = $this->database->getReference("schedules/$selectedDate")->getSnapshot()->exists();

            if ($scheduleExists) {
                // Handle the case where the schedule already exists
                return redirect()->route('schedules.create')->with('error', 'Schedule already exists for the selected date');
            } else {
                // Continue with the code to store the schedule in Firebase

                // Retrieve the existing time slots and labels from Firebase
                $existingData = $this->database->getReference("schedules/$date")->getSnapshot()->getValue();

                // If there are existing time slots, update them with the new ones
                $updatedData = $existingData ?? [];

                foreach ($timeSlots as $key => $slot) {
                    $timeSlotKey = "time_slot_" . ($key + 1);
                    $updatedData[$timeSlotKey]['time'] = $slot;

                    // Check if a label exists for this time slot
                    if (isset($labelTimeSlots[$key])) {
                        $updatedData[$timeSlotKey]['label'] = $labelTimeSlots[$key];
                    }
                }

                // Store the updated schedule in Firebase
                $this->database->getReference("schedules/$date")->set($updatedData);

                return redirect()->route('schedules.create')->with('message', 'Schedule updated successfully');
            }
        } catch (\Exception $e) {
            Log::error('Firebase Error:', ['message' => $e->getMessage(), 'trace' => $e->getTrace()]);
            // Handle the error appropriately (e.g., return an error response)
            return redirect()->route('schedules.create')->with('error', 'Error occurred while updating schedule');
        }
    }
}
