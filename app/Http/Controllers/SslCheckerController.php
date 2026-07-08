<?php

namespace App\Http\Controllers;

use App\Models\Scan;
use App\Services\Contracts\SslServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;


class SslCheckerController extends Controller
{
    
    public function index()
    {
        return view('ssl-checker');
    }

   
    public function check(Request $request, SslServiceInterface $ssl)
    {
        // Validate input
        $request->validate([
            'domain' => 'required|string|max:255|regex:/^[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,}$/',
        ]);

        // Clean domain
        $domain = $request->input('domain');
        $domain = preg_replace('#^https?://#', '', $domain);
        $domain = rtrim($domain, '/');

        try {
            // Perform the SSL check - returns SslReportDto
            $report = $ssl->check($domain);

            // Convert DTO to array for view and database
            $resultData = $report->toArray();
            $resultData['grade'] = $report->getGrade();
            $resultData['domain'] = $domain;

            // Log the check
            Log::info('SSL Check Completed', [
                'domain' => $domain,
                'grade' => $resultData['grade'],
                'has_ssl' => $report->hasSsl,
                'warnings' => $report->warnings,
            ]);

            // Save to database if user is logged in
            if (auth()->check()) {
                Scan::create([
                    'user_id' => auth()->id(),
                    'tool_name' => 'ssl_checker',
                    'input_data' => $domain,
                    'result_data' => $resultData,
                ]);
            }

            // Return view with results
            return view('ssl-checker', ['result' => $resultData]);

        } catch (\Exception $e) {
            // Log the error
            Log::error('SSL Check Failed', [
                'domain' => $domain,
                'error' => $e->getMessage(),
            ]);

            // Return with error message
            return back()
                ->withInput()
                ->withErrors(['domain' => 'SSL check failed: ' . $e->getMessage()]);
        }
    }
}