<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Facade;
use DB;

class CsvController extends Controller
{
    public function parseCsv()
    {
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

                DB::table('observium_devices')->insert([
                    'site' => $site,
                    'device_name' => $device,
                    'bandwidth' => (double)$allocated_bw,
                    'observium_device_id' => $device_id,
                    'observium_port_id' => $port_id,
                    'created_at' => now(),
                    'updated_at' => now(), 
                ]);


                
          
        }
        
    }
}
