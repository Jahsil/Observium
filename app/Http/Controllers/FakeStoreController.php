<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client; 
// use GuzzleHttp\Client;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FakeStoreController extends Controller
{
    //
    public function index(Request $request){

        $user = Auth::guard('api')->user();
        $token = $user->accessToken;

        $token = $request->bearerToken();
        $jwtAuth = app('tymon.jwt.auth');
        $payload = $jwtAuth->parseToken()->getPayload();
        dd(auth()->user()->bearerToken);

   

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
