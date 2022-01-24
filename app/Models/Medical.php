<?php
 namespace App\Models;
 
 use Illuminate\Database\Eloquent\Model;
 class Medical extends Model
 {
     protected $fillable = [
         'medical_name',
         'medical_address',
         'email',
         'mobile'
    ];
 }