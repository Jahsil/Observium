<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Facade;
use DB;

class CsvController extends Controller
{
    public function parseCsv()
    {
        $token = 'eyJhbGciOiJFUzUxMiIsInR5cCI6IkpXVCJ9.eyJhdWQiOiJkZWZhdWx0IiwiZXhwIjoxNzAzNjk1MTk3LCJpYXQiOjE3MDM2ODA3OTcsImlzcyI6IiIsImp0aSI6ImRiOTczNmYxLWQzZjMtNDEzOC05YjJhLWQ5Y2Q2NDY5ZDkzOCIsIm5iZiI6MTcwMzY4MDc5Niwicm9sZXMiOlsiQ1JNX0NSRUFURV9USUNLRVQiLCJDUk1fREVWRUxPUE1FTlRfM19JTUVNQkVSIl0sInN1YiI6ImFiNTVmYTU0LTdlNTItNGU5OS1iMTFlLWY0YjQ2Mjg2OWE5NSIsInR5cCI6ImFjY2Vzc190b2tlbiJ9.AbgLb8RGY01_Us80eELYA5miGShH4TPbltcZmDZ_a-G1biE69QFWtZovPB97tTfUSrpDnRcMws6Zt-7qKuIzGhdDAVSdLkjvsYAw52ux61MLx5gZif4S0ozVR91HXZFziE2_bOdAOeGQwYxVLvbYfKhD8DlcFqZw8tHpYQXdVCm1RktM';
        $client = new Client([
            'verify' => false,
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/json', // Adjust as needed based on the API requirements
            ],
        ]);
        $response = $client->request('GET', 'https://wsbss1.websprix.com/api/v1/leaf_localities');
        $sites = json_decode($response->getBody());
        print_r($sites);
        // print_r($sites[0]->name);
        $fh = fopen('app\Http\obs\device_list.csv', 'r');
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
                print_r($siteId);
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
                    'site' => $site,
                    'site_id' => $siteId,
                    'device_name' => $device,
                    'bandwidth' => (double)$formatted_Allocated_bw,
                    'observium_device_id' => $device_id,
                    'observium_port_id' => $port_id,
                    'created_at' => now(),
                    'updated_at' => now(), 
                ]);


                
          
        }
        
    }


    public function parseStoreData(){

        $token = 'eyJhbGciOiJFUzUxMiIsInR5cCI6IkpXVCJ9.eyJhdWQiOiJkZWZhdWx0IiwiZXhwIjoxNzA0OTYyMzQ1LCJpYXQiOjE3MDQ5NDc5NDUsImlzcyI6IiIsImp0aSI6IjJjYTU2YWNjLTRhMmMtNDI1Ny04MzJjLTExNjllZTRjMWVkOCIsIm5iZiI6MTcwNDk0Nzk0NCwicm9sZXMiOlsiRFRSX1dTX1RFQ0hOSUNJQU4iLCJDUk1fQ1JFQVRFX1RJQ0tFVCIsIkNSTV9ERVZFTE9QTUVOVF8zX0lNRU1CRVIiLCJSUFRfT0JTRVJWSVVNX0FETUlOIiwiRFRSX0RFVklDRV9SRVFVRVNUIl0sInN1YiI6ImFiNTVmYTU0LTdlNTItNGU5OS1iMTFlLWY0YjQ2Mjg2OWE5NSIsInR5cCI6ImFjY2Vzc190b2tlbiJ9.AYdaX29vrqRGKI3xlMYU2CijnPGOsLqXkrn8Mpv77n2fjXNtCAdKGTDnRmXGm8pIDYaO_F-7pCcnAjz51i-CZXz4AeFUBNvs37SMQ4b2ZJcWxy3M-_9d7rigtM9xOKansHh2xQpjrMTlP7-fS5YD82FGEZetgJPkOshhHV84v2gT57QC';
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
}
