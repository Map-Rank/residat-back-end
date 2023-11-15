<?php

namespace App\Models;


class UtilService
{

    public static int $success = 1;
    public static int $bad_params = 0;
    public static int $ERROR = -1;

    public static function showResponse(mixed $data, int $code, String $message, int $type){
        if($type == UtilService::$success){
            return response()->json(['data'=> $data, 'message'=> $message, 'code'=> $code, 'success'=> true], 200);
        }
        else if($type == UtilService::$bad_params){
            return response()->json(['data'=> $data, 'message'=> $message, 'code'=> $code, 'success'=> false], 400);
        }
        else{
            return response()->json(['data'=> $data, 'message'=> $message, 'code'=> $code, 'success'=> false], 500);
        }
    }
}
