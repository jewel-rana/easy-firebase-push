<?php

use Illuminate\Support\Facades\Route;
use Rajtika\Firebase\Services\Firebase;

// Route::get('firebase', function(){
//     return 'Congratulation!! Your firebase initialized.' . config('firebase_api_key');
// });
// Route::get('firebase/send', function(){
//     $token = request('token');
//     if($token) {
//         $response = Firebase::to($token)
//             ->setID(1)
//             ->setTitle('Hello test')
//             ->setBody('You just received my notification, right?')
//             ->send();

//         dd($response);
//     }
//     dd('Token not provided');
// });
