<?php

namespace App\DTOs;

class SslReportDto
{
    public function __construct(
        public bool $hasSsl,
        public bool $isValid,
        public ?string $protocolVersion,
        public ?string $cipherSuite,
        public ?string $issuer,
        public ?string $subject,
        public ?string $expiryDate,
        public ?int $daysLeft,
        public ?array $certificateChain,
        public ?array $headers,
        public ?array $warnings,
        public ?string $error,
        public ?array $rawData,
    ) {}

    public function toArray(): array
    {
        return [
            'has_ssl' => $this->hasSsl,
            'valid' => $this->isValid,
            'protocol_version' => $this->protocolVersion,
            'cipher_suite' => $this->cipherSuite,
            'issuer' => $this->issuer,
            'subject' => $this->subject,
            'expiry_date' => $this->expiryDate,
            'days_left' => $this->daysLeft,
            'certificate_chain' => $this->certificateChain,
            'headers' => $this->headers,
            'warnings' => $this->warnings,
            'error' => $this->error,
            'raw_data' => $this->rawData,
        ];
    }

    public function getGrade(): string
    {
        if (!$this->hasSsl || !$this->isValid) {
            return 'F';
        }

        $score = 0;
        $maxScore = 10;

        // SSL points (max 5)
        $score += 3;

        if ($this->daysLeft !== null && $this->daysLeft > 30) {
            $score += 1;
        }

        if ($this->issuer && !str_contains(strtolower($this->issuer), 'self-signed')) {
            $score += 1;
        }

        // Headers points (max 5)
        $headers = $this->headers ?? [];
        if ($headers['hsts'] ?? false) $score += 1.5;
        if ($headers['csp'] ?? false) $score += 1;
        if ($headers['x_frame_options'] ?? false) $score += 1;
        if ($headers['x_content_type_options'] ?? false) $score += 0.75;
        if ($headers['referrer_policy'] ?? false) $score += 0.5;
        if ($headers['permissions_policy'] ?? false) $score += 0.25;

        $percentage = ($score / $maxScore) * 100;

        if ($percentage >= 90) return 'A+';
        if ($percentage >= 80) return 'A';
        if ($percentage >= 70) return 'B';
        if ($percentage >= 60) return 'C';
        if ($percentage >= 45) return 'D';
        return 'F';
    }
}