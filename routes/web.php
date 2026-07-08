<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\EncoderController;
use App\Http\Controllers\HashToolController;
use App\Http\Controllers\MetadataExtractorController;
use App\Http\Controllers\PasswordController;
use App\Http\Controllers\PdfController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SslCheckerController;
use App\Http\Controllers\UrlCheckerController;
use App\Http\Controllers\ProfileController as UserProfileController;
use App\Http\Controllers\QrCodeController;
use App\Http\Controllers\SmishingController;
use Illuminate\Support\Facades\Route;
use App\Models\Scan;
use App\Jobs\MonitorSslCertificateJob;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\MetadataRemoverController;
use App\Http\Controllers\FeedbackController;
use App\Models\Feedback;




Route::get('/', function () {
    return view('welcome');
})->name('home');


require __DIR__.'/auth.php';


Route::middleware(['auth','user.active','throttle:60,1'])->group(function () {

    // Dashboard (with recent scans)
    Route::get('/dashboard', function () {
        $recentScans = Scan::where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
        return view('dashboard', compact('recentScans'));
    })->name('dashboard');

 
    // Password Analyzer
    Route::get('/password-checker', [PasswordController::class, 'index'])->name('password.checker');
    Route::post('/password-checker', [PasswordController::class, 'check'])->name('password.checker.check');

    // URL Safety Checker
    Route::get('/url-checker', [UrlCheckerController::class, 'index'])->name('url.checker');
    Route::post('/url-checker', [UrlCheckerController::class, 'check'])->name('url.checker.check');

    // Hash Tool
    Route::get('/hash-tool', [HashToolController::class, 'index'])->name('hash.tool');
    Route::post('/hash-tool/generate', [HashToolController::class, 'generate'])->name('hash.tool.generate');
    Route::post('/hash-tool/identify', [HashToolController::class, 'identify'])->name('hash.tool.identify');

    // SSL Checker
    Route::get('/ssl-checker', [SslCheckerController::class, 'index'])->name('ssl.checker');
    Route::post('/ssl-checker', [SslCheckerController::class, 'check'])->name('ssl.checker.check');
    //smishing tool
    Route::get('/smishing-analyzer',[SmishingController::class,'index'])->name('smishing.analyzer');
    Route::post('/smishing-analyzer',[SmishingController::class,'analyze'])->name('smishing.analyzer');

    Route::get('/qr-checker',[QrCodeController::class,'index'])->name('qr.checker');
    Route::post('/qr-checker',[QrCodeController::class,'check'])->name('qr.checker.check');
    
    
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile');

   
    Route::get('/pdf/report/{id}', [PdfController::class, 'generate'])->name('pdf_report');
    Route::get('/pdf/all', [PdfController::class, 'generateAll'])->name('pdf_all');
     
      // Metadata Remover (User Tool)
    Route::get('/metadata-remover', [MetadataRemoverController::class, 'index'])->name('metadata.remover');
    Route::post('/metadata-remover', [MetadataRemoverController::class, 'remove'])->name('metadata.remove');
    Route::get('/metadata-remover/download/{filename}', [MetadataRemoverController::class, 'download'])->name('metadata.download');
   
    Route::get('/docs', function () {
        return view('docs.user_manual');
    })->name('docs');

   
    Route::middleware(['admin'])->group(function () {
    // Dashboard
    Route::get('/admin', [AdminController::class, 'index'])->name('admin.dashboard');
   
    // User Management
    Route::get('/admin/users', [AdminController::class, 'users'])->name('admin.users');
    Route::get('/admin/users/{id}', [AdminController::class, 'userDetails'])->name('admin.user_details');
    Route::delete('/admin/users/{id}', [AdminController::class, 'deleteUser'])->name('admin.user.delete');
    Route::post('/admin/users/{id}/toggle', [AdminController::class, 'toggleUser'])->name('admin.user.toggle');
   
    // Scan Management
    Route::get('/admin/scans', [AdminController::class, 'allScans'])->name('admin.scans');
    Route::delete('/admin/scans/{id}', [AdminController::class, 'deleteScan'])->name('admin.scan.delete');
   
    // Export
    Route::get('/admin/export/users', [AdminController::class, 'exportUsers'])->name('admin.export.users');
    Route::get('/admin/export/scans', [AdminController::class, 'exportScans'])->name('admin.export.scans');

    // System Health
    Route::get('/admin/health', [AdminController::class, 'systemHealth'])->name('admin.health');

   // System Logs
    Route::get('/admin/logs', [AdminController::class, 'systemLogs'])->name('admin.logs');
    Route::post('/admin/logs/clear', [AdminController::class, 'clearLogs'])->name('admin.logs.clear');

    // chase
     Route::post('/admin/cache/clear', [AdminController::class, 'clearCache'])->name('admin.cache.clear');

    Route::post('/admin/ssl/monitor', function () {
    dispatch(new MonitorSslCertificateJob());
    return back()->with('success', 'SSL monitoring job dispatched! Check your email notifications.');
    })->name('admin.ssl.monitor');
 
         Route::get('/admin/feedback', function () {
        $feedbacks = Feedback::with('user')->orderBy('created_at', 'desc')->paginate(20);
        return view('admin.feedback', compact('feedbacks'));
    })->name('admin.feedback');
   
    Route::put('/admin/feedback/{id}', function ($id) {
        $feedback = Feedback::findOrFail($id);
        $feedback->status = 'resolved';
        $feedback->save();
        return redirect()->route('admin.feedback')->with('success', 'Feedback resolved.');
    })->name('admin.feedback.update');
   
    Route::get('/admin/feedback/{id}/view', function ($id) {
        $feedback = Feedback::with('user')->findOrFail($id);
        $feedback->status = 'read';
        $feedback->save();
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
    })->name('admin.feedback.view');


     });

     Route::middleware(['auth', 'student'])->group(function () {
    Route::get('/student/dashboard', [StudentController::class, 'dashboard'])->name('student.dashboard');
    Route::get('/student/docs', function(){ 
        return view('student.docs');
    })->name('student.docs');
    Route::match(['get','post'],'/student/ip-reputation', [StudentController::class, 'ipReputation'])->name('student.ip-reputation');
    Route::match(['get','post'],'/student/dns-lookup', [StudentController::class, 'dnsLookup'])->name('student.dns-lookup');
    Route::match(['get','post'],'/student/whois-lookup', [StudentController::class, 'whoisLookup'])->name('student.whois-lookup');
    Route::match(['get','post'],'/student/cve-lookup', [StudentController::class, 'cveLookup'])->name('student.cve-lookup');
    Route::get('/student/metadata-extractor', [MetadataExtractorController::class, 'index'])->name('student.metadata');
    Route::post('/student/metadata-extractor', [MetadataExtractorController::class, 'extract'])->name('student.metadata.extract');
    Route::get('/student/metadata-encoder', [EncoderController::class, 'index'])->name('student.encoder');
    Route::post('/student/encoder', [EncoderController::class, 'encode'])->name('student.encoder.process');

 
    
    
    });
        // Public Pages
Route::get('/privacy', function () {
    return view('pages.privacy');
})->name('pages.privacy');

Route::get('/terms', function () {
    return view('pages.terms');
})->name('pages.terms');

Route::get('/about', function () {
    return view('pages.about');
})->name('pages.about');

// FAQ Page
Route::get('/faq', function () {
    return view('pages.faq');
})->name('pages.faq');

// Feedback Routes
Route::get('/feedback', [FeedbackController::class, 'index'])->name('pages.feedback');
Route::post('/feedback', [FeedbackController::class, 'store'])->name('feedback.store');
    

});