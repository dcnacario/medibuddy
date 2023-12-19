<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ArduinoController extends Controller
{
    public function sendCommand(Request $request)
    {
        $command = $request->input('command');

        // Process the command and perform actions
        // For example, update the database, dispense medicine, etc.

        return response()->json(['message' => 'Command processed']);
    }
}
