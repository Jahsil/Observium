<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
// use GuzzleHttp\Client;
use GuzzleHttp\Client;

class FakeStoreController extends Controller
{
    //
    public function index(){
        $client = new Client(['verify' => false]);
        $response = $client->request('GET', 'https://fakestoreapi.com/products');
        $products = json_decode($response->getBody());

        // dd($products);
        $response = [
            "status" => "OK",
            "data" => $products  
        ];
        return response()->json($response, 200);
    }

}
