<?php
   
namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\Desease;

class DeseaseController extends Controller {
    public function store(Request $request){
        try {
            //code...
            $lastData = Desease::create($request->all());
                
            return [
                'status' => 'SUCCESS' , 
                'statusCode' => 200,
                'message' => 'Thank you, for you payment',
                'validation_status' => false,
                'last_note' => $lastData
            ];
        } catch (Exception $err) {
            //throw $th;
            return [ 
                'status' => 'FAILED' , 
                'statusCode' => 200,
                'message' => $err,
                'validation_status' => false
            ];
        }
    }

    public function getAll(Request $request){
        try {
            //code...
            $lastData = Desease::all();
                
            return [
                'status' => 'SUCCESS' , 
                'statusCode' => 200,
                'message' => '',
                'validation_status' => false,
                'last_note' => $lastData
            ];
        } catch (Exception $err) {
            //throw $th;
            return [ 
                'status' => 'FAILED' , 
                'statusCode' => 200,
                'message' => $err,
                'validation_status' => false
            ];
        }
    }
}