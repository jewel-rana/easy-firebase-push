<?php
Route::get('firebase', function(){
	return 'Congratulation!! Your firebase initialized.' . config('firebase_api_key');
});