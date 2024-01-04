<?php

namespace App\Http\Controllers;
use DB;
use App\Models\Device;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class ObserviumController extends Controller
{
    //
    public function getDevices(){

        $device = DB::table('test_observium_devices')->get();
        return response()->json([
            "status" => "OK",
            "result" => $device
        ] , 200);

        
    }

    public function index(){
        $device =  Device::all();
        return response()->json([
            'status' => "OK",
            "data" => $device 
        ] , 200);
     }

    public function store(Request $request){
        $request_name = $request->device_name;
        $is_valid_name = Device::where("device_name",$request_name)->first();
        // dd($is_valid_name);
        if ($is_valid_name !== null){
            $response = [
                "status" => "Failed",
                "message" => "Device Was Not Created",
            ];
            return response()->json($response , 404);
        }
        $validatedData = $request->validate([
            'site' => 'required|string',
            'site_id' => 'required|numeric'
            'device_name' => 'required|string|unique:test_observium_devices',
            'bandwidth' => 'required|numeric',
            'observium_device_id' => 'nullable|integer',
            'observium_port_id' => 'nullable|integer',
        ]); 

        $device = Device::create($validatedData);
        $response = [
            "status" => "OK",
            "message" => "Device Created Successfully",
            "data" => $device 
        ];

        return response()->json($response , 200); 

    }

    public function show($name){

        try{
        $device =  Device::findOrFail($name);
        }catch(ModelNotFoundException $err){
            $response = [
                "status" => "Failed",
                "message" => "Device not Found"
            ];
            return response()->json($response , 404); 
        }

        $response = [
            "status" => "OK",
            "data" => $device 
        ];
        return response()->json($response , 200);
    }

    public function update(Request $request, $deviceName){
        
        $device = Device::where("device_name", $deviceName)->first();
        // dd($device);
        if(!$device || $device == null){
            $response = [
                "Status" => "failed",
                "message" => "Device not found"
            ];

            return response()->json($response , 404);
        }

        $validatedData = $request->validate([
            'site' => 'required|string',
            'site_id' => 'required|numeric'
            'device_name' => 'required|string',
            'bandwidth' => 'required|numeric',
            'observium_device_id' => 'nullable|integer',
            'observium_port_id' => 'nullable|integer',
        ]);

        $device->update($validatedData);

        $response = [
            "Status" => "OK",
            "message" => "Device was updated",
            "data" => $device
        ];
        return response()->json($response , 200);

    }

    public function destroy(Request $request , $name){
        $device = Device::where("device_name",$name)->first();
        if (!$device || $device == null){
            $response = [
                "Status" => "failed",
                "message" => "Device not deleted"
            ];

            return response()->json($response , 404);
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
}
