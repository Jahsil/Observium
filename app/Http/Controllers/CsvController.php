<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Facade;
use DB;

class CsvController extends Controller
{
   
    public function parseStoreData(){

        $token = 'eyJhbGciOiJFUzUxMiIsInR5cCI6IkpXVCJ9.eyJhdWQiOiJkZWZhdWx0IiwiZXhwIjoxNzA0OTkwODQ5LCJpYXQiOjE3MDQ5NzY0NDksImlzcyI6IiIsImp0aSI6ImJmMjU0ODI4LTUyNTAtNGM4Ni04ZjI2LTFhZDBiZTFiMTMyMiIsIm5iZiI6MTcwNDk3NjQ0OCwicm9sZXMiOlsiRFRSX1dTX1RFQ0hOSUNJQU4iLCJDUk1fQ1JFQVRFX1RJQ0tFVCIsIkNSTV9ERVZFTE9QTUVOVF8zX0lNRU1CRVIiLCJSUFRfT0JTRVJWSVVNX0FETUlOIiwiRFRSX0RFVklDRV9SRVFVRVNUIl0sInN1YiI6ImFiNTVmYTU0LTdlNTItNGU5OS1iMTFlLWY0YjQ2Mjg2OWE5NSIsInR5cCI6ImFjY2Vzc190b2tlbiJ9.AbcvcbDHInO3j-QQZNvp56_9PJDYzldpCQ8zjFzY1vrOxqlqf9OkYS6pHrn8Lz2Nkvv_GZkFfeBF00hY51hOjdtxAE6gqYyFSqADw4UGZ5Q_IH3coacJbZHaL1VIbpSC5bh6GY9602hspd5ciJR26rkaJF0ujjjJBO72XBRivqn8fb8j';
        $client = new Client([
            'verify' => false,
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/json', // Adjust as needed based on the API requirements
            ],
        ]);

        $response = $client->request('GET', 'https://ws-inv-be.websprix.com/api/v2/products/products');
        $products = json_decode($response->getBody());

        $fh = fopen('app/Http/obs/Shelf_A1.csv', 'r');

        $lineNumber = 0; 
        $res = [];
        $product_id_map = [];
        $product_id_name_map = []; 
        while ($line = fgetcsv($fh, 1000, ',')) {
            $lineNumber++; 

            if (sizeof($line) != 16) {
                echo "ERROR: Invalid row\n";
                print_r($line);
                continue;

            }

            $lineString = implode(', ', $line);
            // echo "Type of line " . gettype($line) . "\n";
            $row = $lineNumber; 
            $numColumns = count($line);
            for ($i = 0; $i < $numColumns; $i++) {
                if(empty($line[$i])){
                    continue; 
                }
                $shelf_name = strval(chr(65 + $i)) . strval($row);
                $res[$shelf_name] = $line[$i];
                // echo "Shelf name ".$shelf_name." ---------- ".$line[$i];
            }

            
        }

           

            foreach ($products as $product) {

                if (!isset($product_id_map[$product->id])){
                    $product_id_map[$product->product_id] = $product->id;
                    $product_id_name_map[$product->product_name] = $product->id; 
                }else{
                    continue; 
                }             
            }

            foreach ($res as $shelf_name => $product_id) {
                
                $id = DB::table("shelves")->insertGetId([
                    "label" => $shelf_name,
                    "store_id" => 1,
                    "created_at" => now(),
                    "updated_at" => now(),
                ]);

                $csvValues = explode(',', $product_id);
                
               
                foreach($csvValues as $index => $prod ){
                    if (isset($product_id_map[$prod])){
                        DB::table('product_shelves')->insert([
                            "shelf_id" => $id,
                            "product_id" => $product_id_map[trim($prod)],
                            "created_at" => now(),
                            "updated_at" => now(),
                        ]);
                    }elseif ( isset($product_id_name_map[trim($prod)]) &&  !isset($product_id_map[trim($prod)])   ) {
                        DB::table('product_shelves')->insert([
                            "shelf_id" => $id,
                            "product_id" => $product_id_name_map[trim($prod)],
                            "created_at" => now(),
                            "updated_at" => now(),
                        ]);
                    }
                }
                

            
                
            }



            // parse second csv file 

            $fh = fopen('app/Http/obs/BLM2.csv', 'r');
            $lineNumber = 0; 
            $res = [];
            while ($line = fgetcsv($fh, 1000, ',')) {
                $lineNumber++; 
                if (sizeof($line) != 5) {
                    echo "ERROR: Invalid row\n";
                    print_r($line);
                    continue;
    
                }
    
                $lineString = implode(', ', $line);
                // echo "Type of line " . gettype($line) . "\n";
                $row = $lineNumber; 
                $numColumns = count($line);
                for ($i = 0; $i < $numColumns; $i++) {
                    if(empty($line[$i])){
                        continue; 
                    }
                    $shelf_name = strval(chr(65 + $i)) . strval($row);
                    $res[$shelf_name] = $line[$i];
                    // echo "Shelf name ".$shelf_name." ---------- ".$line[$i];
                }
    
                
            }
    
               
    
                foreach ($res as $shelf_name => $product_id) {
                    
                    $id = DB::table("shelves")->insertGetId([
                        "label" => $shelf_name,
                        "store_id" => 8,
                        "created_at" => now(),
                        "updated_at" => now(),
                    ]);
    
                    $csvValues = explode(',', $product_id);
                    
                   
                    foreach($csvValues as $index => $prod ){
                        if (isset($product_id_map[$prod])){
                            DB::table('product_shelves')->insert([
                                "shelf_id" => $id,
                                "product_id" => $product_id_map[trim($prod)],
                                "created_at" => now(),
                                "updated_at" => now(),
                            ]);
                        }elseif ( isset($product_id_name_map[trim($prod)]) &&  !isset($product_id_map[trim($prod)])   ) {
                            DB::table('product_shelves')->insert([
                                "shelf_id" => $id,
                                "product_id" => $product_id_name_map[trim($prod)],
                                "created_at" => now(),
                                "updated_at" => now(),
                            ]);
                        }
                    }
                    
    
                
                    
                }


            

            // parse third csv file 
            

            $fh = fopen('app/Http/obs/BLM3.csv', 'r');
            $lineNumber = 0; 
            $res = [];
            while ($line = fgetcsv($fh, 1000, ',')) {
                $lineNumber++; 
                if (sizeof($line) != 13) {
                    echo "ERROR: Invalid row\n";
                    print_r($line);
                    continue;
    
                }
    
                $lineString = implode(', ', $line);
                // echo "Type of line " . gettype($line) . "\n";
                $row = $lineNumber; 
                $numColumns = count($line);
                for ($i = 0; $i < $numColumns; $i++) {
                    if(empty($line[$i])){
                        continue; 
                    }
                    $shelf_name = strval(chr(65 + $i)) . strval($row);
                    $res[$shelf_name] = $line[$i];
                    // echo "Shelf name ".$shelf_name." ---------- ".$line[$i];
                }
    
                
            }
    
               
    
                foreach ($res as $shelf_name => $product_id) {
                    
                    $id = DB::table("shelves")->insertGetId([
                        "label" => $shelf_name,
                        "store_id" => 10,
                        "created_at" => now(),
                        "updated_at" => now(),
                    ]);
    
                    $csvValues = explode(',', $product_id);
                    
                   
                    foreach($csvValues as $index => $prod ){
                        if (isset($product_id_map[$prod])){
                            DB::table('product_shelves')->insert([
                                "shelf_id" => $id,
                                "product_id" => $product_id_map[trim($prod)],
                                "created_at" => now(),
                                "updated_at" => now(),
                            ]);
                        }elseif ( isset($product_id_name_map[trim($prod)]) &&  !isset($product_id_map[trim($prod)])   ) {
                            DB::table('product_shelves')->insert([
                                "shelf_id" => $id,
                                "product_id" => $product_id_name_map[trim($prod)],
                                "created_at" => now(),
                                "updated_at" => now(),
                            ]);
                        }
                    }
                    
    
                
                    
                }




    }

    public function parseObserviumCsv()
    {
        
        $fh = fopen('./app/Http/obs/device_list.csv', 'r');
        $first = true;
        while ($line = fgetcsv($fh, 1000, ',')) {
                if ($first) {
                        $first = false;
                        continue;
                }

                if (sizeof($line) != 9) {
                        echo "ERROR: Invalid row\n";
                        print_r($line);
                        continue;
                }
                list($site,$device,$allocated_bw,$avg_bw,$util,$peak_bw,$peak_dur,$comment,$link)=$line;
                $siteId = 0;
                foreach ($sites as $apiSite) {
                    $siteName = $apiSite->name;
                    if ($site !== null && strpos($siteName, $site) !== false) {
                        $siteId = $apiSite->id;
                        break;
                    }
                }                

                $formattedNumber = trim($allocated_bw);  
                $formatted_Allocated_bw = intval(str_replace(',', '', $formattedNumber));
                

                $pattern = '/device=(\d+).*port=(\d+)/';

                if ($device === " " || empty($device) || mb_strlen($device) == 0 ){
                    continue; 
                }

                if (isset($link)) {
                    preg_match($pattern, $link, $matches);
                    $device_id = $matches[1];
                    $port_id = $matches[2];
                }else{
                    $device_id = null;
                    $port_id = null;
                }
                // echo gettype($allocated_bw);

                DB::table('test_observium_devices')->insert([
                    'site_id' => 1,
                    'site' => $site,
                    'device_name' => $device,
                    'bandwidth' => (double)$formatted_Allocated_bw,
                    'observium_device_id' => $device_id,
                    'observium_port_id' => $port_id,
                    'created_at' => now(),
                    'updated_at' => now(), 

                ]);

        }


    }

    public function getNullTallyBatches(){

        $products = DB::table('products')
        ->select('products.product_id', 'products.product_name','products.id')
        ->leftJoin('tally_batches', 'products.id', '=', 'tally_batches.product_id')
        ->whereNull('tally_batches.id')
        ->get();

        $res = [
            "products_cnt" => count($products),
            "products" => $products
        ];

        return response()->json($res, 200);

    }

    public function parseLostDevicesCsv(){

        $filePath = '/home/eyouel/Documents/Eyouel/Observium/app/Http/obs/lost_devices.csv';

        if (!file_exists($filePath)) {
            echo "ERROR: File not found.\n";
            return;
        }

        $fh = fopen($filePath, 'r');

        $columns = 3;
        while ($line = fgetcsv($fh, 1000, ',')) {
            if (sizeof($line) != $columns) {
                echo "ERROR: Invalid row\n";
                print_r($line);
                continue;
            }

            list($serial_no, $mac_address, $product_id) = $line;

            $device = DB::table('devices')
            ->where(function ($query) use ($serial_no, $mac_address) {
                $query->where('devices.serial_no', $serial_no)
                    ->orWhere(function ($query) use ($mac_address) {
                        $query->whereNull('devices.serial_no')
                            ->where('devices.mac_address', $mac_address);
                    });
            })
            ->get();

            $user_name = "59d51042-51ea-4d63-aafa-9d7c9a9ba5c8";
            foreach($device as $dev){
                $status = 0;
                if($dev->status === 1){
                    try {
                    DB::table('device_activity')->insert([
                        'fromType' => 2,
                        'toType' => 10,
                        "from" => $dev->store_id,
                        "to" => 10,
                        'device_id' => $dev->id,
                        'created_by' => $user_name,
                        'quantity' => 0,
                    ]);
                }catch (\Exception $e) {
                    echo "Error: " . $e->getMessage();
                }
                }else if($dev->status === 2){
                    DB::table('device_activity')->insert([
                        'fromType' => 3,
                        'toType' => 10,
                        'from' => $dev->technician_id ? $dev->technician_id : $dev->dispatcher_id,
                        'to' => 10,
                        'device_id' => $dev->id,
                        'created_by' => $user_name,
                        'quantity' => 0,
                    ]);

                }else if($dev->status === 3){
                    DB::table('device_activity')->insert([
                        'fromType' => 6,
                        'toType' => 10,
                        'from' => $dev->technician_id ? $dev->technician_id : $dev->dispatcher_id,
                        'to' => 10,
                        'device_id' => $dev->id,
                        'created_by' => $user_name,
                        'quantity' => 0,
                    ]);


                }else if($dev->status === 4){
                    DB::table('device_activity')->insert([
                        'fromType' => 5,
                        'toType' => 10,
                        'from' => $dev->customer_id,
                        'to' => 10,
                        'device_id' => $dev->id,
                        'created_by' => $user_name,
                        'quantity' => 0,
                    ]);


                }else if($dev->status === 7){
                    DB::table('device_activity')->insert([
                        'fromType' => 9,
                        'toType' => 10,
                        'from' => $dev->store_id,
                        'to' => 10,
                        'device_id' => $dev->id,
                        'created_by' => $user_name,
                        'quantity' => 0,
                    ]);
                }

                try {
                    DB::table('devices')
                        ->where('id', $dev->id)
                        ->update(['status' => 9 ]);
        
                } catch (\Exception $e) {
                }


            }
            $result[] = $device;

        }    

    }

    public function parseDecSerial(){

        $filePath = './app/Http/obs/dec_serial.csv';

        if (!file_exists($filePath)) {
            echo "ERROR: File not found.\n";
            return;
        }

        $fh = fopen($filePath, 'r');
        $user_name = "425ad9fa-6fdf-4768-b2c2-3a38a447b56f";

        $columns = 7;
        $first = true;
        $count = 0; 
        while ($line = fgetcsv($fh, 1000, ',')) {
            if ($first) {
                $first = false;
                continue;
            }

            if (sizeof($line) != $columns) {
                echo "ERROR: Invalid row\n";
                print_r($line);
                continue;
            }

            list($serial_no, $stat, $location_id, $dec_serial) = $line;


            // get device from device table 
            $device = DB::table('devices')
                        ->where('serial_no', $serial_no)
                        ->first();

            $declaration = DB::table("declarations")
            ->where("serial", $dec_serial)
            ->first();

            if ($declaration) {
                if($device){
                    if ($stat == "In store") {
                        $devices = DB::table('devices')
                            ->where('serial_no', $serial_no)
                            ->update([
                                "store_id" => $location_id,
                                "dec_id" => $declaration->id,
                                "status" => 1
                            ]);
    
                            // Add to device activity 
                            try {
                                DB::table('device_activity')->insert([
                                    'fromType' => $device->status + 1,
                                    'toType' => 2,
                                    "from" => $device->store_id,
                                    "to" => $location_id,
                                    'device_id' => $device->id,
                                    'created_by' => $user_name,
                                    'quantity' => 0,
                                ]);
                            }catch (\Exception $e) {
                                echo "Error: " . $e->getMessage();
                            }
    
                    } else {
                        $devices = DB::table('devices')
                            ->where('serial_no', $serial_no)
                            ->update([
                                "facility_id" => $location_id,
                                "dec_id" => $declaration->id,
                                "status" => 6
                            ]);
    
                        // Add to device activity 
                        try {
                            DB::table('device_activity')->insert([
                                'fromType' => $device->status + 1, 
                                'toType' => 8,
                                "from" => $device->store_id,
                                "to" => $location_id,
                                'device_id' => $device->id,
                                'created_by' => $user_name,
                                'quantity' => 0,
                            ]);
                        }catch (\Exception $e) {
                            echo "Error: " . $e->getMessage();
                        }
    
                        
                    }
                }
            } else {
                echo "Declaration not found for serial number: $dec_serial\n";
            }

            
        }    
        
    }

    public function getQuantity(){
        
        

        $data = DB::table('batches as bt')
            ->join('temp_devices as td', 'td.batch_id', '=', 'bt.id')
            ->select('td.product_id', DB::raw('SUM(COALESCE(quantity, 1)) / 2 as total_quantity'))
            ->where('bt.product_id', '=', 6)
            ->where('added_to_store', '=', 1)
            ->where('bt.dec_id', '=', 1)
            ->groupBy('td.product_id')
            ->get();

        $data2 = DB::table('declaration_products')
        ->select('quantity')
        ->where('declarationId', '=', 6 )
        ->where('productId', '=', 6)
        ->get();

        
        


    }
}
