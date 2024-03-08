<?php

namespace App\Http\Controllers;

use DB;
use App\Models\Device;
use App\Models\Site;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class ObserviumController extends Controller
{
    //
    public function getDevices()
    {

        $device = DB::table('test_observium_devices')->get();
        return response()->json([
            "status" => "OK",
            "result" => $device
        ], 200);
    }

    public function index()
    {
        $device =  Device::all();
        return response()->json([
            'status' => "OK",
            "data" => $device
        ], 200);
    }

    public function store(Request $request)
    {
        $request_name = $request->device_name;
        $is_valid_name = Device::where("device_name", "=", $request_name)->first();
        // dd($is_valid_name);
        if ($is_valid_name !== null || isset($is_valid_name)) {
            $response = [
                "status" => "Failed",
                "message" => "Device Was Not Created",
            ];
            return response()->json($response, 404);
        }
        $validatedData = $request->validate([
            'site' => 'required|string',
            'site_id' => 'required|numeric',
            'device_name' => 'required|string|unique:test_observium_devices',
            'bandwidth' => 'required|numeric',
            'observium_device_id' => 'nullable|integer',
            'observium_port_id' => 'nullable|integer',
            'type' => 'required|integer',
            'description' => 'string'
        ]);

        $device = Device::create($validatedData);
        $response = [
            "status" => "OK",
            "message" => "Device Created Successfully",
            "data" => $device
        ];

        return response()->json($response, 200);
    }

    public function show($name)
    {

        try {
            $device =  Device::findOrFail($name);
        } catch (ModelNotFoundException $err) {
            $response = [
                "status" => "Failed",
                "message" => "Device not Found"
            ];
            return response()->json($response, 404);
        }

        $response = [
            "status" => "OK",
            "data" => $device
        ];
        return response()->json($response, 200);
    }

    public function update(Request $request, $deviceName)
    {

        $device = Device::where("device_name", $deviceName)->first();
        // dd($device);
        if (!$device || $device == null) {
            $response = [
                "Status" => "failed",
                "message" => "Device not found"
            ];

            return response()->json($response, 404);
        }

        $validatedData = $request->validate([
            'site' => 'required|string',
            'site_id' => 'required|numeric',
            'device_name' => 'required|string',
            'bandwidth' => 'required|numeric',
            'observium_device_id' => 'nullable|integer',
            'observium_port_id' => 'nullable|integer',
            'type' => 'required|integer',
            'description' => 'string'
        ]);

        $device->update($validatedData);

        $response = [
            "Status" => "OK",
            "message" => "Device was updated",
            "data" => $device
        ];
        return response()->json($response, 200);
    }

    public function destroy(Request $request, $name)
    {
        $device = Device::where("device_name", $name)->first();
        if (!$device || $device == null) {
            $response = [
                "Status" => "failed",
                "message" => "Device not deleted"
            ];

            return response()->json($response, 404);
        }
        $device->delete();
        $response = [
            "Status" => "OK",
            "message" => "Device was deleted",
            "data" => $device
        ];
        return 204;
        // return response()->json($response , 204);

    }

    public function addSite(Request $request)
    {

        try {
            $request->validate([
                'site' => 'required|string',
                'isActive' => 'required|boolean',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        }


        // $site = new Site();
        // $site->site = $request->input('site');
        // $site->isActive = $request->input('isActive'); 
        // $site->save();

        $siteData = [
            'site' => $request->input('site'),
            'isActive' => $request->input('isActive'),
        ];

        $site = DB::table('device_sites')->insert($siteData);

        return response()->json([
            'status' => "OK",
            'message' => 'Site added successfully',
            'data' => $site
        ], 201);
    }

    public function updateSite(Request $request, $id)
    {
        try {
            $request->validate([
                'isActive' => 'boolean',
            ]);
        } catch (ValidationException $e) {
            // Return JSON response with validation errors
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        }

        $site = DB::table('device_sites')->where('id', $id)->first();

        if (!$site) {
            return response()->json([
                'status' => 'error',
                'message' => 'Site not found',
            ], 404);
        }


        // $site->site_name = $request->input('site_name');
        // $site->is_active = $request->input('is_active', false); // Default to false if not provided
        // $site->save();


        DB::table('device_sites')
            ->where('id', $id)
            ->update([
                'isActive' => $request->input('isActive'),
            ]);

        $updatedSite = DB::table('device_sites')->find($id);


        return response()->json([
            'status' => 'OK',
            'message' => 'Site updated successfully',
            'data' => $updatedSite,
        ]);
    }


    public function getSites()
    {
        // $sites = Site::all();

        $sites = DB::table('device_sites')->get();


        return response()->json([
            'status' => "OK",
            'sites' => $sites
        ]);
    }



    public function destroySite(Request $request, $id)
    {
        $site = DB::table('device_sites')->where('id', $id)->first();

        if (!$site) {
            return response()->json([
                'status' => 'error',
                'message' => 'Site not found',
            ], 404);
        }

        DB::table('device_sites')->where('id', $id)->delete();

        return response()->json([
            'status' => 'OK',
            'message' => 'Site deleted successfully',
            'data' => $site,
        ]);
    }
}






// CREATE TABLE observium_device_logs (
//     id BIGINT PRIMARY KEY AUTO_INCREMENT,
//     user_id BIGINT NOT NULL,
//     device_name VARCHAR(255) NOT NULL,
//     bandwidth DOUBLE NOT NULL,
//     observium_device_id BIGINT NOT NULL,
//     observium_port_id BIGINT NOT NULL,
//     message VARCHAR(255) NOT NULL,
//     type VARCHAR(255) NOT NULL,
//     updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
//     created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
// );