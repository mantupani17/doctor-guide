<?php

use Illuminate\Http\Request;
use Illuminate\Http\Response;

Route::group(['middleware' => []], function(){
    /**
     * Medical API's
     */
    Route::get('/medical', 'Api\MedicalController@getMedicals');
    Route::post('/medical', 'Api\MedicalController@createMedical');
    // Route::get('/address/active' , 'AddressController@getActiveAddressDetails');
    // Route::get('/auth/gym/profile' , 'GymController@getUserProfile');

    /**
     * Desease API's
     */
    Route::get('/desease', 'Api\DeseaseController@getAll');
    Route::post('/desease', 'Api\DeseaseController@store');
}); 