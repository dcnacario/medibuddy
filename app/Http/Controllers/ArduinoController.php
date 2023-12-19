<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Kreait\Firebase\Contract\Database;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ArduinoController extends Controller
{
    protected $database;

    public function __construct(Database $database)
    {
        $this->database = $database;
    }

    public function getSchedule(Request $request)
    {
        // Retrieve the schedule from Firebase
        $schedule = $this->database->getReference('schedules')->getValue();

        // Send the schedule to Arduino (NodeMCU ESP8266)
        $this->sendScheduleToArduino($schedule);

        return response()->json(['message' => 'Schedule sent to Arduino']);
    }

    private function sendScheduleToArduino($schedule)
    {
        // Assume your NodeMCU ESP8266 has an IP address of 192.168.1.100 and is listening on port 80
        $arduinoUrl = 'http://192.168.1.100:80/receive-schedule';

        // Send the schedule data to Arduino using HTTP POST request
        $response = Http::post($arduinoUrl, [
            'schedule' => json_encode($schedule),
        ]);

        // Handle the response as needed
        if ($response->successful()) {
            // Successfully sent the schedule to Arduino
            // You can add additional logic based on the response from Arduino
            // For example, logging or handling specific responses
            Log::info('Schedule sent to Arduino successfully');
        } else {
            // Failed to send the schedule
            Log::error('Failed to send schedule to Arduino: ' . $response->body());
        }
    }
}
