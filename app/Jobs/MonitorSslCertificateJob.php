<?php

namespace App\Jobs;

use App\Models\Scan;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class MonitorSslCertificateJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        // Get all SSL scans with expiry in next 30 days
        $sslScans = Scan::where('tool_name', 'ssl_checker')
            ->where('created_at', '>=', now()->subDays(30))
            ->get();

        $notifiedUsers = [];

        foreach ($sslScans as $scan) {
            $domain = $scan->input_data;
            $daysLeft = $scan->result_data['ssl']['days_left'] ?? null;

            if ($daysLeft !== null && $daysLeft > 0 && $daysLeft <= 7) {
                $user = $scan->user;

                if ($user && !in_array($user->id, $notifiedUsers)) {
                    $notifiedUsers[] = $user->id;

                    Mail::raw(
                        " SSL Certificate Expiry Alert\n\n" .
                        "Your SSL certificate for {$domain} expires in {$daysLeft} days!\n\n" .
                        "Please renew it as soon as possible to keep your website secure.\n\n" .
                        "— CyberShield.lk Security Team",
                        function ($message) use ($user) {
                            $message->to($user->email)
                                ->subject(' SSL Certificate Expiry Alert');
                        }
                    );
                }
            }
        }
    }
}