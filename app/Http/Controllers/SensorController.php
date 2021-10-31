<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Sensor;
use Illuminate\Http\Request;

class SensorController extends Controller
{
    // all sensors
    public function index()
    {
       $sensors = Sensor::all()->toArray();
       return array_reverse($sensors);

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
            'status' => 'required|in:Online, Pending, Offline',
            'reading' => 'required|integer'
        ]);

        $sensorExists = false;
        //replace with laravel built in finding method read docs eloquent models Retrieving Single Models / Aggregates
        //temp code
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

        return response($response, 201);

    }

    // edit sensor
    public function edit($name, Request $request)
    {
        $field = $request->validate([
            'name' => 'required|string',
            'building' => 'required|string',
            'location' => 'required|string',
            'status' => 'required|in:Online, Pending, Offline',
            'reading' => 'required|integer'
        ]);

        //What if not found ??? FIX !!
        $sensor = Sensor::where('name', $name)->firstOrFail();

        $sensor->name = $field['name'];
        $sensor->building = $field['building'];
        $sensor->location = $field['location'];
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

    // update sensor
    public function update($name, Request $request)
    {
        $field = $request->validate([
            'status' => 'required|in:Online, Pending, Offline',
            'reading' => 'required|integer'
        ]);

        //What if not found ??? FIX !!
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

        $sensor->delete();
        return response($response, 200);

    }
}
