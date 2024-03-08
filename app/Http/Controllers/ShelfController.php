<?php

namespace App\Http\Controllers;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use DB;
class ShelfController extends Controller
{
    //
    public function getProducts(Request $request){

        $token = 'eyJhbGciOiJFUzUxMiIsInR5cCI6IkpXVCJ9.eyJhdWQiOiJkZWZhdWx0IiwiZXhwIjoxNzA1NTY3OTIxLCJpYXQiOjE3MDU1NTM1MjEsImlzcyI6IiIsImp0aSI6IjA3ZGI4MzQ4LTQyNjktNGFhZC1hNTk4LTM3Yjg5NzBhMWM4ZSIsIm5iZiI6MTcwNTU1MzUyMCwicm9sZXMiOlsiRFRSX1dTX1RFQ0hOSUNJQU4iLCJDUk1fQ1JFQVRFX1RJQ0tFVCIsIkNSTV9ERVZFTE9QTUVOVF8zX0lNRU1CRVIiLCJSUFRfT0JTRVJWSVVNX0FETUlOIiwiRFRSX0RFVklDRV9SRVFVRVNUIl0sInN1YiI6ImFiNTVmYTU0LTdlNTItNGU5OS1iMTFlLWY0YjQ2Mjg2OWE5NSIsInR5cCI6ImFjY2Vzc190b2tlbiJ9.ARoBliXIR5_V5yERRXZb1dmN2nxYa-ol6s1RkC0ZqpfUzTXiEL-e0KsgbIcUrcMIZVw-PuOg-5RmIdEIy3duWuQrAIr7Y_lPkAJPlY5FAnp61vOk6dP6q0BExs1zPBvt2dr2sBFz_yzhsu9RMz6At6he93v2Wr5xE_PmOKUHmrHMW3Yk';
        $client = new Client([
            'verify' => false,
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/json', // Adjust as needed based on the API requirements
            ],
        ]);

        $response = $client->request('GET', 'https://ws-inv-be.websprix.com/api/v2/products/products');
        $products = json_decode($response->getBody());


        if ($products === null){
            $response = [
                "status" => "Failed",
                "message" => "Products not found",
            ];
            return response()->json($response , 404);
        }
        
        $response = [
            "status" => "OK",
            "cnt" => count($products),
            "data" => $products  
        ];
        return response()->json($response , 200);
    }


    public function getShelf(Request $request){
        $storeId = $request->input('store_id');
        $productId = $request->input('product_id') ? $request->input('product_id') : null ;
        if (!isset($productId) || $productId === null ){
            $shelf = DB::table('shelves')
            ->where("shelves.store_id" , '=' , $storeId)
            ->get();
        }else{
            $shelfs = DB::table('shelves')
            ->leftJoin("product_shelves as ps", "shelves.id" , "=" , "ps.shelf_id")
            ->select(
                "shelves.id",
                "shelves.label",
                "shelves.store_id",
                "ps.shelf_id",
                "ps.product_id"
                )
            ->where("shelves.store_id" , "=" , $storeId)    
            ->where("ps.product_id", "=", $productId)
            ->get();

            $response = [
                "status" => "OK",
                "cnt" => count($shelfs),
                "data" => $shelfs 
            ];
            return response()->json($response , 200);
            
        }
        
        // dd($shelf);
        $shelfHash = [];
        $product_shelves = DB::table('product_shelves')
        ->select('shelf_id' , 'product_id')
        ->get();

        foreach($product_shelves as $ps){
            $shelfId = $ps->shelf_id;
            $productId = $ps->product_id;

            if (!isset($shelfHash[$shelfId])) {
                $shelfHash[$shelfId] = [];
            }
            $shelfHash[$shelfId][] = $productId;
        }
        foreach($shelf as $sh){
            if (isset($shelfHash[$sh->id])){
                $sh->products_list = $shelfHash[$sh->id];  
            }
        }

        // dd($shelf);
        if ($shelf === null){
            $response = [
                "status" => "Failed",
                "message" => "Store not found",
            ];
            return response()->json($response , 404);
        }
        
        $response = [
            "status" => "OK",
            "cnt" => count($shelf),
            "data" => $shelf 
        ];
        return response()->json($response , 200);
}

}