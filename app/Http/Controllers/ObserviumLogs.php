<?php

namespace App\Http\Controllers;
use DB;

use Illuminate\Http\Request;

class ObserviumLogs extends Controller
{
    //
    public function getLogs()
    {
        try {
            $logs = DB::table('observium_device_logs')->get();
            return response()->json($logs);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function storeLog(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'user_id' => 'required|integer',
                'device_name' => 'required|string',
                'bandwidth' => 'required|numeric',
                'observium_device_id' => 'required|integer',
                'observium_port_id' => 'required|integer',
                'type' => 'required|string',
                'message' => 'required|string'
            ]);
            
            $device = DB::table('observium_device_logs')->insertGetId($validatedData);
            $newDevice = DB::table('observium_device_logs')->find($device);
            
            $response = [
                'status' => 'OK',
                'message' => 'Device created successfully',
                'data' => $newDevice,
            ];
            
            return response()->json($response, 200);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function showLog($id)
    {
        try {
            $log = DB::table('observium_device_logs')->find($id);
            if ($log) {
                return response()->json($log);
            } else {
                return response()->json(['error' => 'Log not found'], 404);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function updateLog(Request $request, $id)
    {
        try {
            $validatedData = $request->validate([
                'user_id' => 'required|integer',
                'device_name' => 'required|string',
                'bandwidth' => 'required|numeric',
                'observium_device_id' => 'required|integer',
                'observium_port_id' => 'required|integer',
                'type' => 'required|string',
                'message' => 'required|string'
            ]);


            
            $updatedRows = DB::table('observium_device_logs')->where('id', $id)->update($validatedData);
            if ($updatedRows > 0) {
                $log = DB::table('observium_device_logs')->find($id);
                return response()->json($log);
            } else {
                return response()->json(['error' => 'Log not found or No changes made'], 404);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function destroyLog($id)
    {
        try {
            $deletedRows = DB::table('observium_device_logs')->where('id', $id)->delete();
            // dd($deletedRows);
            if ($deletedRows > 0) {
                return response()->json([
                    'status' => 'Log deleted successfully'
                ], 204);
            } else {
                return response()->json(['error' => 'Log not found'], 404);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function getLogWithDeviceName(Request $request, $device_name)
    {
        try {
            $logs = DB::table('observium_device_logs')->where('device_name', $device_name)->get();

            if ($logs->isEmpty()) {
                return response()->json(['message' => 'No logs found for the specified device name.'], 404);
            }

            return response()->json($logs, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

}


