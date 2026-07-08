<?php

namespace App\Http\Controllers;

use App\Models\Scan;
use Illuminate\Http\Request;

class SmishingController extends Controller
{
    public function index()
    {
        return view('smishing-analyzer');
    }

    public function analyze(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:10000',
        ]);

        $message = $request->input('message');

        // Analyze the message
        $analysis = $this->analyzeMessage($message);

        // Save to database
        if (auth()->check()) {
            Scan::create([
                'user_id' => auth()->id(),
                'tool_name' => 'smishing_analyzer',
                'input_data' => substr($message, 0, 255), // Store first 255 chars
                'result_data' => $analysis,
            ]);
        }

        return view('smishing-analyzer', ['analysis' => $analysis, 'message' => $message]);
    }

    private function analyzeMessage($message)
    {
        $message = strip_tags($message);
        $messageLower = strtolower($message);

        // 1. Check for suspicious keywords
        $suspiciousKeywords = [
            'urgent' => 'Urgency tactic: "urgent" used',
            'immediate' => 'Urgency tactic: "immediate" used',
            'verify' => 'Suspicious: "verify" used',
            'login' => 'Suspicious: "login" used',
            'account' => 'Suspicious: "account" used',
            'bank' => 'Suspicious: "bank" used',
            'paypal' => 'Impersonation: "paypal" used',
            'amazon' => 'Impersonation: "amazon" used',
            'apple' => 'Impersonation: "apple" used',
            'microsoft' => 'Impersonation: "microsoft" used',
            'netflix' => 'Impersonation: "netflix" used',
            'click here' => 'Suspicious link: "click here" used',
            'link' => 'Suspicious: "link" mentioned',
            'security' => 'Suspicious: "security" used',
            'alert' => 'Suspicious: "alert" used',
            'suspended' => 'Suspicious: "suspended" used',
            'locked' => 'Suspicious: "locked" used',
            'free' => 'Suspicious: "free" used',
            'win' => 'Suspicious: "win" used',
            'prize' => 'Suspicious: "prize" used',
            'lottery' => 'Suspicious: "lottery" used',
            'update' => 'Suspicious: "update" used',
            'confirm' => 'Suspicious: "confirm" used',
            'details' => 'Suspicious: "details" used',
            'password' => 'Suspicious: "password" used',
            'credit card' => 'Sensitive: "credit card" mentioned',
            'ssn' => 'Sensitive: "ssn" mentioned',
            'bank account' => 'Sensitive: "bank account" mentioned',
            'wire transfer' => 'Sensitive: "wire transfer" mentioned',
        ];

        $detectedKeywords = [];
        foreach ($suspiciousKeywords as $keyword => $warning) {
            if (str_contains($messageLower, $keyword)) {
                $detectedKeywords[] = $warning;
            }
        }

        // 2. Check for suspicious links
        $suspiciousLinks = $this->extractLinks($message);
        $linkAnalysis = $this->analyzeLinks($suspiciousLinks);

        // 3. Check for urgency tactics
        $urgencyKeywords = ['urgent', 'immediate', 'now', 'today', 'asap', 'quickly', 'fast', 'soon', 'instantly'];
        $urgencyDetected = [];
        foreach ($urgencyKeywords as $keyword) {
            if (str_contains($messageLower, $keyword)) {
                $urgencyDetected[] = ucfirst($keyword);
            }
        }

        // 4. Check for grammar/spelling errors (simplified)
        $grammarIssues = $this->checkGrammar($message);

        // 5. Calculate risk score
        $riskScore = $this->calculateRiskScore(
            count($detectedKeywords),
            count($suspiciousLinks),
            count($urgencyDetected),
            count($grammarIssues)
        );

        // 6. Determine threat level
        $threatLevel = $this->getThreatLevel($riskScore);

        // 7. Generate recommendations
        $recommendations = $this->generateRecommendations(
            $detectedKeywords,
            $suspiciousLinks,
            $urgencyDetected,
            $grammarIssues,
            $threatLevel
        );

        return [
            'risk_score' => $riskScore,
            'threat_level' => $threatLevel,
            'detected_keywords' => $detectedKeywords,
            'suspicious_links' => $suspiciousLinks,
            'link_analysis' => $linkAnalysis,
            'urgency_detected' => $urgencyDetected,
            'grammar_issues' => $grammarIssues,
            'recommendations' => $recommendations,
            'message_length' => strlen($message),
            'word_count' => str_word_count($message),
            'analyzed_at' => now()->format('Y-m-d H:i:s'),
        ];
    }

    private function extractLinks($message)
    {
        $links = [];
        $regex = '/(https?:\/\/[^\s]+)/i';
        preg_match_all($regex, $message, $matches);
        if (!empty($matches[0])) {
            foreach ($matches[0] as $link) {
                $links[] = trim($link);
            }
        }
        return $links;
    }

    private function analyzeLinks($links)
    {
        $analysis = [];
        foreach ($links as $link) {
            $suspicious = false;
            $reason = [];

            // Check for suspicious domains
            $suspiciousDomains = ['bit.ly', 'tinyurl', 'shorturl', 'tiny.cc', 'ow.ly', 'is.gd', 't.co'];
            foreach ($suspiciousDomains as $domain) {
                if (str_contains($link, $domain)) {
                    $suspicious = true;
                    $reason[] = 'Uses URL shortener: ' . $domain;
                }
            }

            // Check for IP address instead of domain
            if (preg_match('/https?:\/\/\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}/i', $link)) {
                $suspicious = true;
                $reason[] = 'Uses IP address instead of domain name';
            }

            // Check for suspicious TLDs
            $suspiciousTlds = ['.top', '.xyz', '.click', '.online', '.site', '.space'];
            foreach ($suspiciousTlds as $tld) {
                if (str_contains($link, $tld)) {
                    $suspicious = true;
                    $reason[] = 'Uses suspicious TLD: ' . $tld;
                }
            }

            $analysis[] = [
                'url' => $link,
                'suspicious' => $suspicious,
                'reasons' => $reason,
            ];
        }
        return $analysis;
    }

    private function checkGrammar($message)
    {
        $issues = [];

        // Check for all caps
        if (preg_match('/[A-Z]{5,}/', $message)) {
            $issues[] = 'Contains long blocks of uppercase text (shouting)';
        }

        // Check for excessive exclamation marks
        if (preg_match('/!{3,}/', $message)) {
            $issues[] = 'Contains excessive exclamation marks (urgency tactic)';
        }

        // Check for common misspellings
        $commonTypos = ['recieve' => 'receive', 'untill' => 'until', 'alot' => 'a lot', 'seperate' => 'separate'];
        foreach ($commonTypos as $typo => $correct) {
            if (str_contains(strtolower($message), $typo)) {
                $issues[] = "Possible typo: '{$typo}' should be '{$correct}'";
            }
        }

        return $issues;
    }

    private function calculateRiskScore($keywordCount, $linkCount, $urgencyCount, $grammarCount)
    {
        $score = 0;

        // Keywords: up to 40 points
        $score += min($keywordCount * 8, 40);

        // Suspicious links: up to 25 points
        $score += min($linkCount * 12, 25);

        // Urgency tactics: up to 15 points
        $score += min($urgencyCount * 5, 15);

        // Grammar issues: up to 20 points
        $score += min($grammarCount * 5, 20);

        return min($score, 100);
    }

    private function getThreatLevel($score)
    {
        if ($score >= 60) {
            return ['level' => 'High Risk', 'color' => 'red', 'icon' => '🔴'];
        } elseif ($score >= 30) {
            return ['level' => 'Medium Risk', 'color' => 'yellow', 'icon' => '🟡'];
        } else {
            return ['level' => 'Low Risk', 'color' => 'green', 'icon' => '🟢'];
        }
    }

    private function generateRecommendations($keywords, $links, $urgency, $grammar, $threatLevel)
    {
        $recommendations = [];

        if ($threatLevel['level'] === 'High Risk') {
            $recommendations[] = '❌ DO NOT click any links or reply to this message.';
            $recommendations[] = '❌ DO NOT provide any personal or financial information.';
            $recommendations[] = '📱 Mark as spam/phishing in your messaging app.';
            $recommendations[] = '📧 If from email, forward it to the legitimate company\'s abuse team.';
        } elseif ($threatLevel['level'] === 'Medium Risk') {
            $recommendations[] = '⚠️ Be cautious. Do not click links without verifying.';
            $recommendations[] = '🔍 Hover over links (on desktop) to see the actual destination.';
            $recommendations[] = '📧 Contact the legitimate company directly via their official website (not from this message).';
        } else {
            $recommendations[] = '✅ This message appears safe but stay vigilant.';
            $recommendations[] = '🔍 Always verify unsolicited messages that ask for personal info.';
        }

        if (!empty($keywords)) {
            $recommendations[] = '🔑 Be wary of messages that pressure you to act immediately.';
        }

        if (!empty($links)) {
            $recommendations[] = '🔗 Always check links before clicking. Legitimate companies rarely use link shorteners.';
        }

        if (!empty($urgency)) {
            $recommendations[] = '⏰ Scammers often create false urgency to make you act without thinking.';
        }

        return $recommendations;
    }
}