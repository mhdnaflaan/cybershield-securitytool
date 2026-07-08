<?php

namespace App\Http\Controllers;

use App\Models\Scan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class PasswordController extends Controller
{
    
    public function index()
    {
        return view('password-checker');
    }

    public function check(Request $request)
    {
        $password = $request->input('password');
        if (!$password) {
            return back()->withErrors(['password' => 'Please enter a password.']);
        }

        
        $strength = $this->calculateStrength($password);

    
        $crackTime = $this->calculateCrackTime($password);

        
        $breachResult = $this->checkHibpBreach($password);
        $breachMessage = $breachResult['message'];
        $breachIcon = $breachResult['icon']; 
        $breachClass = $breachResult['class']; 

        
        $resultData = [
            'strength'      => $strength,
            'crack_time'    => $crackTime,
            'breach_status' => $breachMessage,
            'is_pwned'      => $breachResult['is_pwned'] ?? false,
        ];

        
        if (auth()->check()) {
            Scan::create([
                'user_id'     => auth()->id(),
                'tool_name'   => 'password_analyzer',
                'input_data'  => $password,
                'result_data' => $resultData,
            ]);
        }

        
        return view('password-checker', [
            'result'        => true,
            'password'      => $password,
            'strength'      => $strength,
            'crackTime'     => $crackTime,
            'breachMessage' => $breachMessage,
            'breachIcon'    => $breachIcon,
            'breachClass'   => $breachClass,
        ]);
    }

    
    private function calculateStrength($password)
    {
        $score = 0;
        if (strlen($password) >= 8) $score++;
        if (strlen($password) >= 12) $score++;
        if (preg_match('/[A-Z]/', $password)) $score++;
        if (preg_match('/[a-z]/', $password)) $score++;
        if (preg_match('/[0-9]/', $password)) $score++;
        if (preg_match('/[^A-Za-z0-9]/', $password)) $score++;

        if ($score <= 2) return 'Weak';
        if ($score <= 4) return 'Medium';
        return 'Strong';
    }

    
    private function calculateCrackTime($password)
    {
        $length = strlen($password);
        $charset = 0;
        if (preg_match('/[a-z]/', $password)) $charset += 26;
        if (preg_match('/[A-Z]/', $password)) $charset += 26;
        if (preg_match('/[0-9]/', $password)) $charset += 10;
        if (preg_match('/[^A-Za-z0-9]/', $password)) $charset += 33;
        if ($charset == 0) $charset = 26;

        $combinations = pow($charset, $length);
        $guessesPerSecond = 10000000000; 
        $seconds = $combinations / $guessesPerSecond;

        if ($seconds < 60) return 'Less than 1 minute';
        if ($seconds < 3600) return round($seconds / 60) . ' minutes';
        if ($seconds < 86400) return round($seconds / 3600) . ' hours';
        if ($seconds < 31536000) return round($seconds / 86400) . ' days';
        return round($seconds / 31536000) . ' years';
    }

    private function checkHibpBreach($password)
    {
        
        $sha1Hash = strtoupper(sha1($password));

        
        $prefix = substr($sha1Hash, 0, 5);
        $suffix = substr($sha1Hash, 5);

        try {
            
            $response = Http::timeout(5)->get("https://api.pwnedpasswords.com/range/" . $prefix);

            
            if ($response->successful() && str_contains($response->body(), $suffix)) {
                return [
                    'is_pwned' => true,
                    'message' => '⚠️ This password has appeared in a data breach! Do not use it.',
                    'icon' => '⚠️',
                    'class' => 'text-red-600'
                ];
            } else {
                return [
                    'is_pwned' => false,
                    'message' => '✅ This password has not been found in any known breaches.',
                    'icon' => '✅',
                    'class' => 'text-green-600'
                ];
            }
        } catch (\Exception $e) {
            return [
                'is_pwned' => null,
                'message' => '⚠️ Could not check breach database. Please check your internet connection.',
                'icon' => '⚠️',
                'class' => 'text-yellow-600'
            ];
        }
    }
}