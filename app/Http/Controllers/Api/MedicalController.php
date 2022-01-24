<?php
   
namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\Medical;



   
class MedicalController extends Controller 
{

    public function getMedicals(Request $request){
        try {
            //code...
            $lastData = Medical::all();
            return [ 
                'status' => 'SUCCESS' , 
                'statusCode' => 200,
                'message' => "",
                'validation_status' => true,
                'data' => $lastData
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

    public function createMedical(Request $request) {
        try {
            //code...
            $lastData = Medical::create($request->all());
                
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
    
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request){
        try{
            $controller = new Auth\RegisterController();
            $reqData = $request->all();
            $isValid = Validator::make($reqData, [
                 'user_state' => 'required',
                 'user_district'=> 'required',
                 'user_pincode'=> 'required',
                 'user_location'=> 'required',
                 'user_address'=> 'required',
                 'user_name'=> 'required',
                //  'user_guardian_name'=> 'required',
                 'user_mobile'=> 'required',
                 'user_whatspp_mobile' => 'required',
                //  'user_email' => 'required|unique:addresses',
                 'user_country' => 'required',
                 'user_status' => 'required'
            ]);
            // checking for validation
            if ($isValid->fails()) {
                $errors = $isValid->errors();
                if ( ! empty( $errors ) ) {
                    $errorsData = '';
                    foreach ( $errors->all() as $error ) {
                        $errorsData .= '<div class="error">' . $error . '</div>';
                    }
                    return [
                        'status' => 'FAILED' , 
                        'statusCode' => 200,
                        'message' => 'Please provide some valid data, this user is already exist.',
                        'validation_message' => $errorsData,
                        'validation_status' => true
                    ];
                }
            }else{
                if(!isset($reqData['user_email'])) {
                    $request['user_email'] = $reqData['user_mobile'].'@handshakeyou.com';
                    $reqData['user_email'] = $reqData['user_mobile'].'@handshakeyou.com';
                }
                $data = [
                    'name' => $reqData['user_name'],
                    'email' => $reqData['user_email'],
                    'password' => 'Customer@123',
                    'role' => 'Customer',
                    'created_by'=>$reqData['created_by']
                ];
                $isGymCreated = $controller->create($data);
                if($isGymCreated){
                    $lastId = $isGymCreated['id'];
                    // once validation has done creating or saving the trainer details
                    $request['user_detail_id'] = $lastId;
                    Address::create($request->all());
                    return [
                        'status' => 'SUCCESS' , 
                        'statusCode' => 200,
                        'message' => 'success Greate! user created successfully.',
                        'validation_status' => false
                    ];
                }else{
                    return [
                        'status' => 'FAILED' , 
                        'statusCode' => 200,
                        'message' => 'Sorry user creation failed',
                        'validation_status' => false
                    ];
                }
                
            }
        }catch(Exception $err){
            return [ 
                'status' => 'FAILED' , 
                'statusCode' => 200,
                'message' => $err,
                'validation_status' => false
            ];
        }
        
    }
    
    
     /**
     * Display all the active users .
     *
     */
    public function getActiveAddressDetails(Request $request){
        try{
            
            $UserDetails =  Address::where('gym_status', 'NOTVERIFIED')->get();
            if(!empty($UserDetails)){
                 return [
                    'data' => $UserDetails, 
                    'status' => 'SUCCESS' , 
                    'statusCode' => 200,
                    'message' => ''
                ];
            }else{
                return [
                    'data' => [], 
                    'status' => 'FAILED' , 
                    'statusCode' => 200,
                    'message' => 'No UserDetails Found with this code.'
                ];
            }
        }catch(Exception $err){
            return [
                'data' => [], 
                'status' => 'FAILED' , 
                'statusCode' => 200,
                'message' => $err
            ];
        }
    }
    
   
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request){
        try{
            $UserDetails_id = $request->get('gym_detail_id');
            if($UserDetails_id){
                $UserDetails =  Address::where('gym_id',$UserDetails_id)->delete();
                $user =  User::where('id',$UserDetails_id)->delete();
                if($UserDetails && $user){
                     return [ 
                        'status' => 'SUCCESS' , 
                        'statusCode' => 200,
                        'message' => 'UserDetails removed successfuly...'
                    ];
                }else{
                    return [
                        'status' => 'FAILED' , 
                        'statusCode' => 200,
                        'message' => 'No UserDetails Found with this code.'
                    ];
                }
            }else{
                return [
                    'status' => 'FAILED' , 
                    'statusCode' => 200,
                    'message' => 'Inavlid UserDetails'
                ];
            }
            
            
        }catch(Exception $err){
            return [
                    'data' => [], 
                    'status' => 'FAILED' , 
                    'statusCode' => 200,
                    'message' => $err
                ];
        }
    }
     
     
    public function getAllAddress(Request $request){
         try{
            $getData = $request->all();
            $where = [];
            $select = '*';
            if($getData['role'] === 'SuperAdmin'){
                $where = [];
            }else if($getData['role'] === 'VerificationOfiicer'){
                $where['user_status'] = 'NOTVERIFIED';
                if(isSet($getData['user_id'])){
                    $where['created_by'] = (int)$getData['user_id'];
                }
            }else {
                // $where['user_status'] = 'VERIFIED';
                if(isSet($getData['user_id'])){
                    $where['created_by'] = (int)$getData['user_id'];
                }
            }
            if(isset($getData['fields'])){
                $select = explode(',', $getData['fields']);
            }
            // return $select;
            $trainers = Address::select($select)->where($where)->get();
            if(count($trainers)){
                 return [
                    'data' => $trainers, 
                    'status' => 'SUCCESS' , 
                    'statusCode' => 200
                ];
            }else{
                return [
                    'data' => [], 
                    'status' => 'SUCCESS' , 
                    'statusCode' => 200
                ];
            }
        }catch(Exception $err){
            print_r($err);
            return [
                'data' => [], 
                'status' => 'SUCCESS' , 
                'statusCode' => 200,
                'message' => $err
            ];
        }
    } 
    
    
    
    
    public function getAddressProfile(Request $request){
        $authorization_token = $request->header('Authorization');
        if($authorization_token){
            $isLoggedIn =  User::where('access_token', $authorization_token)->first();
            if(!empty($isLoggedIn)){
                $userDetails = Address::where('user_detail_id' , $isLoggedIn->id)->first();
                if(!empty($userDetails)){
                    return response()->json([
                        'status' => 'SUCCESS' , 
                        'statusCode' => 200,
                        'message' => 'Valid user',
                        'user' => $userDetails
                    ]);
                }
                return $isLoggedIn;
                
            }else{
                return response()->json([
                    'status' => 'FAILED' , 
                    'statusCode' => 403,
                    'message' => 'Unauthorised access',
                    'redirectTo' => '/login.html'
                ]);
            }
        }else{
            return response()->json([
                'status' => 'FAILED' , 
                'statusCode' => 403,
                'message' => 'Authorization token missmatch.',
                'redirectTo' => '/login.html'
            ]);
        }
    }
    
    
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function updateAddress(Request $request, $id)
    {
        try{
            $reqData = $request->all();
            $isValid = Validator::make($reqData, [
                'updateData' => 'required'
            ]);
            // checking for validation
            if ($isValid->fails()) {
                $errors = $isValid->errors();
                if ( ! empty( $errors ) ) {
                    $errorsData = '';
                    foreach ( $errors->all() as $error ) {
                        $errorsData .= '<div class="error">' . $error . '</div>';
                    }
                    return [
                        'status' => 'FAILED' , 
                        'statusCode' => 200,
                        'message' => 'Please provide some valid data, this user is already exist.',
                        'validation_message' => $errorsData,
                        'validation_status' => true
                    ];
                }
            }else{
                $result = Address::where('address_id',(int)$id)->update($request['updateData']);
            
                if(isset($result)){
                    return response()->json([
                        'status' => 'SUCCESS' , 
                        'statusCode' => 200,
                        'message' => 'Updated Successfully'
                    ]);
                }else{
                     return response()->json([
                        'status' => 'SUCCESS' , 
                        'statusCode' => 200,
                        'message' => 'Sorry, failed to update.'
                    ]);
                }
            }
            
        }catch(Exception $err){
            return [
                'data' => [], 
                'status' => 'SUCCESS' , 
                'statusCode' => 200,
                'message' => $err
            ];
        }
        
    }
}