<?php

namespace App\Http\Controllers;
use App\Models\Scan;
use Illuminate\Http\Request;

class DashboradController extends Controller
{
    public function index(){
        $recentScans = Scan::where('user_id', auth()->id())
                        ->orderBy('created_at', 'desc')
                        ->take(5)
                        ->get();
                         
        return view('dashboard' , compact('recentScans'));
    }
}
