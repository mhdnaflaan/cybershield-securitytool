<?php

namespace App\Http\Controllers;

use App\Models\Scan;
use App\Models\User;
use App\Models\Feedback;
use App\Jobs\MonitorSslCertificateJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
class AdminController extends Controller
{
    /**
     * Admin Dashboard with Analytics
     */
    public function index()
    {
        
        $totalUsers = User::count();
        $activeUsers = User::where('is_active', true)->count();
        $blockedUsers = User::where('is_active', false)->count();
        $totalScans = Scan::count();

        // Tool-specific stats
        $totalPasswordScans = Scan::where('tool_name', 'password_analyzer')->count();
        $totalUrlScans = Scan::where('tool_name', 'url_checker')->count();
        $totalSslScans = Scan::where('tool_name', 'ssl_checker')->count();
        $totalHashScans = Scan::where('tool_name', 'hash_tool')->count();
        $totalQrScans = Scan::where('tool_name', 'qr_checker')->count();
        $totalSmishingScans = Scan::where('tool_name', 'smishing_analyzer')->count();
        $totalMetadataScans = Scan::where('tool_name', 'metadata_remover')->count();

        // Student tool stats
        $totalDnsScans = Scan::where('tool_name', 'dns_lookup')->count();
        $totalWhoisScans = Scan::where('tool_name', 'whois_lookup')->count();
        $totalIpScans = Scan::where('tool_name', 'ip_reputation')->count();
        $totalCveScans = Scan::where('tool_name', 'cve_lookup')->count();
        $totalMetadataExtractorScans = Scan::where('tool_name', 'metadata_extractor')->count();

        
        $scansPerDay = Scan::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();

        
        $toolUsage = Scan::selectRaw('tool_name, COUNT(*) as count')
            ->groupBy('tool_name')
            ->get();

        
        $activeUsersList = User::withCount('scans')
            ->orderBy('scans_count', 'desc')
            ->limit(5)
            ->get();

        
        $recentScans = Scan::with('user')
            ->orderBy('created_at', 'desc')
            ->limit(15)
            ->get();

        
        $recentUsers = User::orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        
        $recentFeedback = Feedback::with('user')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

       $pendingFeedbackCount= Feedback::where('status','pending')->count();

       $usersPerDay = User::selectRaw('DATE(created_at) as date, COUNT(*) as count')
    ->where('created_at', '>=', now()->subDays(30))
    ->groupBy('date')
    ->orderBy('date', 'asc')
    ->get();
      
        $systemStatus = [
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
            'environment' => config('app.env'),
            'debug_mode' => config('app.debug') ? 'ON' : 'OFF',
            'db_connected' => $this->checkDatabaseConnection(),
            'cache_driver' => config('cache.default'),
            'queue_driver' => config('queue.default'),
            'last_scan' => Scan::latest()->first()?->created_at?->diffForHumans() ?? 'No scans',
        ];

        return view('admin.dashboard', compact(
            'totalUsers',
            'activeUsers',
            'blockedUsers',
            'totalScans',
            'totalPasswordScans',
            'totalUrlScans',
            'totalSslScans',
            'totalHashScans',
            'totalQrScans',
            'totalSmishingScans',
            'totalMetadataScans',
            'totalDnsScans',
            'totalWhoisScans',
            'totalIpScans',
            'totalCveScans',
            'totalMetadataExtractorScans',
            'scansPerDay',
            'toolUsage',
            'activeUsersList',
            'recentScans',
            'recentUsers',
            'recentFeedback',
            'pendingFeedbackCount',
            'systemStatus',
            'usersPerDay',
        ));
    }

    /**
     * Check database connection
     */
    private function checkDatabaseConnection()
    {
        try {
            DB::connection()->getPdo();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }


    /**
     * List all users with their scan counts
     */
    public function users()
    {
        $users = User::withCount('scans')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.users', compact('users'));
    }

    /**
     * View individual user details with their scan history
     */
    public function userDetails($id)
    {
        $user = User::with('scans')->findOrFail($id);
        return view('admin.user_details', compact('user'));
    }

    /**
     * Delete a user and all their associated scans
     */
    public function deleteUser($id)
    {
        $user = User::findOrFail($id);

        // Prevent admin from deleting themselves
        if ($user->id === auth()->id()) {
            return redirect()->route('admin.users')
                ->with('error', 'You cannot delete your own account.');
        }

        // Delete all scans associated with this user
        Scan::where('user_id', $id)->delete();

        $user->delete();

        return redirect()->route('admin.users')
            ->with('success', "User '{$user->name}' deleted successfully.");
    }

    /**
     * Block or Unblock a user (toggle is_active)
     */
    public function toggleUser($id)
    {
        $user = User::findOrFail($id);

        // Prevent admin from blocking themselves
        if ($user->id === auth()->id()) {
            return redirect()->route('admin.users')
                ->with('error', 'You cannot block your own account.');
        }

        $user->is_active = !$user->is_active;
        $user->save();

        $status = $user->is_active ? 'unblocked' : 'blocked';

        return redirect()->route('admin.users')
            ->with('success', "User '{$user->name}' {$status} successfully.");
    }

    

    /**
     * List all scans with filtering options
     */
    public function allScans(Request $request)
    {
        $query = Scan::with('user')->orderBy('created_at', 'desc');

        // Filter by tool
        if ($request->has('tool') && $request->tool != '') {
            $query->where('tool_name', $request->tool);
        }

        // Filter by user
        if ($request->has('user_id') && $request->user_id != '') {
            $query->where('user_id', $request->user_id);
        }

        // Filter by date
        if ($request->has('date') && $request->date != '') {
            $query->whereDate('created_at', $request->date);
        }

        // Filter by risk level (for URL scans)
        if ($request->has('risk_level') && $request->risk_level != '') {
            $query->where('result_data->risk_level', $request->risk_level);
        }

        $scans = $query->paginate(20);
        $users = User::all();
        $tools = [
            'password_analyzer',
            'url_checker',
            'ssl_checker',
            'hash_tool',
            'qr_checker',
            'smishing_analyzer',
            'metadata_remover',
            'dns_lookup',
            'whois_lookup',
            'ip_reputation',
            'cve_lookup',
            'metadata_extractor'
        ];

        return view('admin.scans', compact('scans', 'users', 'tools'));
    }

    /**
     * Delete a single scan
     */
    public function deleteScan($id)
    {
        $scan = Scan::findOrFail($id);
        $scan->delete();

        return redirect()->route('admin.scans')
            ->with('success', 'Scan deleted successfully.');
    }

    /**
     * Bulk delete scans
     */
    public function bulkDeleteScans(Request $request)
    {
        $ids = $request->input('ids', []);
        if (empty($ids)) {
            return redirect()->route('admin.scans')
                ->with('error', 'No scans selected.');
        }

        Scan::whereIn('id', $ids)->delete();

        return redirect()->route('admin.scans')
            ->with('success', count($ids) . ' scans deleted successfully.');
    }

    

    /**
     * Export all users as CSV
     */
    public function exportUsers()
    {
        $users = User::withCount('scans')->get();

        $filename = 'users_export_' . date('Y-m-d') . '.csv';

        $handle = fopen('php://output', 'w');

        // Headers
        fputcsv($handle, [
            'ID',
            'Name',
            'Email',
            'Role',
            'Status',
            'Scans Count',
            'Joined Date',
            'Last Login'
        ]);

        // Data
        foreach ($users as $user) {
            fputcsv($handle, [
                $user->id,
                $user->name,
                $user->email,
                $user->role,
                $user->is_active ? 'Active' : 'Blocked',
                $user->scans_count ?? 0,
                $user->created_at->format('Y-m-d'),
                $user->last_login_at ?? 'Never',
            ]);
        }

        fclose($handle);

        return response()->stream(
            function () use ($handle) {
                // Stream is already sent
            },
            200,
            [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ]
        );
    }

    /**
     * Export all scans as CSV
     */
    public function exportScans()
    {
        $scans = Scan::with('user')
            ->orderBy('created_at', 'desc')
            ->get();

        $filename = 'scans_export_' . date('Y-m-d') . '.csv';

        $handle = fopen('php://output', 'w');

        // Headers
        fputcsv($handle, [
            'ID',
            'User',
            'Tool',
            'Input',
            'Result',
            'Date'
        ]);

        // Data
        foreach ($scans as $scan) {
            $result = '';

            if ($scan->tool_name == 'password_analyzer') {
             $result = $scan->result_data['strength'] ?? 'N/A';
            } elseif ($scan->tool_name == 'url_checker') {
                $result = $scan->result_data['risk_level'] ?? 'N/A';
            } elseif ($scan->tool_name == 'ssl_checker') {
                $result = $scan->result_data['grade'] ?? 'N/A';
            } else {
                $result = 'Completed';
            }

            fputcsv($handle, [
                $scan->id,
                $scan->user->name ?? 'Unknown',
                str_replace('_', ' ', $scan->tool_name),
                substr($scan->input_data, 0, 50),
                $result,
                $scan->created_at->format('Y-m-d H:i'),
            ]);
        }

        fclose($handle);

        return response()->stream(
            function () use ($handle) {
                // Stream is already sent
            },
            200,
            [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ]
        );
    }

    // ============================================
    // SSL MONITORING (Background Job)
    // ============================================

    /**
     * Dispatch SSL monitoring job
     */
    public function monitorSSL()
    {
        dispatch(new MonitorSslCertificateJob());

        return redirect()->route('admin.dashboard')
            ->with('success', 'SSL monitoring job dispatched! Users with expiring SSL certificates will be notified via email.');
    }

    
    /**
     * View system logs
     */
    public function systemLogs()
    {
        $logFile = storage_path('logs/laravel.log');

        if (!file_exists($logFile)) {
            return view('admin.logs', ['logs' => [], 'error' => 'Log file not found.']);
        }

        // Get last 100 lines of log
        $logs = $this->tailFile($logFile, 100);

        return view('admin.logs', compact('logs'));
    }

    /**
     * Helper to tail a file
     */
    private function tailFile($file, $lines = 100)
    {
        $handle = fopen($file, "r");
        $buffer = [];
        $pos = -1;
        $line = '';
        $count = 0;

        while ($count < $lines && fseek($handle, $pos, SEEK_END) !== -1) {
            $char = fgetc($handle);
            if ($char === "\n") {
                $count++;
                if ($count > 1) {
                    $buffer[] = trim($line);
                }
                $line = '';
            } else {
                $line = $char . $line;
            }
            $pos--;
        }

        fclose($handle);

        if (!empty($line)) {
            $buffer[] = trim($line);
        }

        return array_reverse($buffer);
    }

    
     //Clear logs
     
    public function clearLogs()
    {
        $logFile = storage_path('logs/laravel.log');
        if (file_exists($logFile)) {
            file_put_contents($logFile, '');
            return response()->json(['success' => true]);
        }
        return response()->json(['success' => false, 'error' => 'Log file not found']);
    }

    //system health
    public function systemHealth()
    {
        $health = [
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
            'environment' => config('app.env'),
            'debug_mode' => config('app.debug') ? 'ON' : 'OFF',
            'db_connected' => $this->checkDatabaseConnection(),
            'db_connection' => config('database.default'),
            'cache_driver' => config('cache.default'),
            'session_driver' => config('session.driver'),
            'queue_driver' => config('queue.default'),
            'timezone' => config('app.timezone'),
            'total_users' => User::count(),
            'total_scans' => Scan::count(),
            'last_scan' => Scan::latest()->first()?->created_at?->diffForHumans() ?? 'No scans',
            'gd_loaded' => extension_loaded('gd'),
            'curl_loaded' => extension_loaded('curl'),
            'openssl_loaded' => extension_loaded('openssl'),
            'zip_loaded' => extension_loaded('zip'),
        ];

        return view('admin.health', compact('health'));
    }

    
     // Clear system cache
     
    public function clearCache()
    {
        Artisan::call('config:clear');
        Artisan::call('cache:clear');
        Artisan::call('route:clear');
        Artisan::call('view:clear');
        Artisan::call('optimize:clear');

        return redirect()->route('admin.health')
            ->with('success', 'All caches cleared successfully!');
    }

    
    
     //View all feedback
     
    public function feedback()
    {
        $feedbacks = Feedback::with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.feedback', compact('feedbacks'));
    }

    
    // View single feedback
     
    public function viewFeedback($id)
    {
        $feedback = Feedback::with('user')->findOrFail($id);
       
        // Mark as read
        if ($feedback->status === 'pending') {
            $feedback->status = 'read';
            $feedback->save();
        }

        return response()->json([
            'user_name' => $feedback->user->name ?? 'Unknown',
            'type_label' => ucfirst(str_replace('_', ' ', $feedback->type)),
            'type_class' => $feedback->type == 'bug_report' ? 'bg-red-100 text-red-600' :
                           ($feedback->type == 'feature_request' ? 'bg-purple-100 text-purple-600' :
                           ($feedback->type == 'support' ? 'bg-yellow-100 text-yellow-600' : 'bg-blue-100 text-blue-600')),
            'subject' => $feedback->subject,
            'message' => $feedback->message,
            'status' => ucfirst($feedback->status),
            'created_at' => $feedback->created_at->format('Y-m-d H:i:s'),
        ]);
    }

    
     //Resolve feedback
     
    public function resolveFeedback($id)
    {
        $feedback = Feedback::findOrFail($id);
        $feedback->status = 'resolved';
        $feedback->save();

        return redirect()->route('admin.feedback')
            ->with('success', 'Feedback resolved successfully.');
    }
}