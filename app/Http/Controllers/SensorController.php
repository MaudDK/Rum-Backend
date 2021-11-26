<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Sensor;
use App\Models\Notification;
use Illuminate\Http\Request;
use Carbon\Carbon;

class SensorController extends Controller
{
    // all sensors
    public function index()
    {
       $sensors = Sensor::all();
       return Sensor::all()->toArray();

    }

    public function getSensor($name){
        $sensor = Sensor::where('name', $name)->firstOrFail();
        $response = [
            'id' => $sensor->id,
            'name' => $sensor->name,
            'building' => $sensor->building,
            'location' => $sensor->location,
            'status' => $sensor->status,
            'reading' => $sensor->reading
        ];

        return response($response, 200);
    }

    // add sensor
    public function store(Request $request)
    {
        //Validates request data integrity
        $field = $request->validate([
            'name' => 'required|string',
            'building' => 'required|string',
            'location' => 'required|string',
            'status' => 'required',
            'reading' => 'required|integer'
        ]);

        $sensorExists = false;

        $sensors = Sensor::all();
        foreach($sensors as $sensor)
        {
            if ($sensor->name == $field['name'])
            {
                $sensorExists = true;
            }
        }

        if ($sensorExists)
        {
            return response(['Message' => 'Sensor Already Exists In Database'], 409);
        }

        $newSensor = Sensor::create([
            'name' => $field['name'],
            'building' => $field['building'],
            'location' => $field['location'],
            'status' => $field['status'],
            'reading' => $field['reading']
        ]);

        $response = [
            'id' => $newSensor->id,
            'name' => $newSensor->name,
            'building' => $newSensor->building,
            'location' => $newSensor->location,
            'status' => $newSensor->status,
            'reading' => $newSensor->reading
        ];
        $message = "is now online.";
        $newNotification = Notification::create(['alert' => 'Informative','message' => $newSensor->name.' '.$message]);

        return response($response, 201);

    }

    // edit sensor
    public function edit($name, Request $request)
    {
        $field = $request->validate([
            'name' => 'required|string',
            'building' => 'required|string',
            'location' => 'required|string',
            'status' => 'required|string',
            'reading' => 'required|integer'
        ]);

        $sensor = Sensor::where('name', $name)->firstOrFail();

        $sensor->name = $field['name'];
        $sensor->building = $field['building'];
        $sensor->location = $field['location'];
        $sensor->status = $field['status'];
        $sensor->reading = $field['reading'];


        $response = [
            'id' => $sensor->id,
            'name' => $sensor->name,
            'building' => $sensor->building,
            'location' => $sensor->location,
            'status' => $sensor->status,
            'reading' => $sensor->reading
        ];

        return response($response, 200);
    }

    // update sensor
    public function update($name, Request $request)
    {
        $field = $request->validate([
            'status' => 'required|string',
            'reading' => 'required|integer'
        ]);

        $sensor = Sensor::where('name', $name)->firstOrFail();

        $sensor->status = $field['status'];
        $sensor->reading = $field['reading'];

        $sensor->save();

        $response = [
            'id' => $sensor->id,
            'name' => $sensor->name,
            'building' => $sensor->building,
            'location' => $sensor->location,
            'status' => $sensor->status,
            'reading' => $sensor->reading
        ];

        return response($response, 200);   
    }

    // delete sensor
    public function destroy($name)
    {
        $sensor = Sensor::where('name', $name)->firstOrFail();

        $response = [
            'id' => $sensor->id,
            'name' => $sensor->name,
            'building' => $sensor->building,
            'location' => $sensor->location,
            'status' => $sensor->status,
            'reading' => $sensor->reading
        ];
        $message = "was deleted.";
        $newNotification = Notification::create(['alert' => 'Informative','message' => $sensor->name.' '.$message]);

        $sensor->delete();
        return response($response, 200);

    }
}
