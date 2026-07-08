<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Scan;

class EncoderController extends Controller
{
       public function index()
       {
        return view ('student.encoder');
       }

       public function encode (Request $request)
       {
        $request -> validate([

            'text'=> 'required | string',
             'type' => 'required |in:base64_encode,base64_decode,url_encode,url_decode',



        ]);

        $text = $request->input('text');
        $type = $request-> input('type');
        $result='';
        $method ='';

        switch($type){

        case 'base64_encode' :
            $result = base64_encode($text);
            $method ='Base64 Encode';
            break;

        case 'base64_decode' :
            $result = base64_decode($text , true)?: 'Invalid Base64 String';
            $method = 'Base64 Decode';
            break;
        
        case 'url_encode' :
            $result = urlencode($text);
            $method ='Url Encode';
            break;

        case 'url_decode' :
            $result = urldecode($text);
            $method ='Url Decode';
            break;

        }

        if(auth()->check()){
            Scan::create([
                'user_id'=> auth()->id(),
                'tool_name' =>'encoder_tool',
                'input_data' =>$text,
                'result_data' => [
                    'type' => $method,
                    'input' => $text,
                    'output' => $result,
                ],

            ]);


        }

        return view ('student.encoder',[
            'result' => $result,
            'input' => $text,
            'method' => $method,
        ]);

       }
}
