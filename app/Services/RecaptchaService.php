<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RecaptchaService
{
    protected string $siteKey;
    protected string $secretKey;
    protected float $v3Threshold;

    public function __construct()
    {
        $this->siteKey = config('services.recaptcha.site_key', '');
        $this->secretKey = config('services.recaptcha.secret_key', '');
        $this->v3Threshold = (float) config('services.recaptcha.v3_threshold', 0.5);
    }

    /**
     * Verify reCAPTCHA v3 token
     */
    public function verifyV3(?string $token, ?string $action = null): array
    {
        if (empty($token)) {
            return ['success' => false, 'error' => 'Token is missing'];
        }

        try {
            $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
                'secret' => $this->secretKey,
                'response' => $token,
            ]);

            $result = $response->json();

            if (!$result || !isset($result['success'])) {
                Log::warning('reCAPTCHA v3 verification failed: Invalid response', ['response' => $result]);
                return ['success' => false, 'error' => 'Invalid response from reCAPTCHA'];
            }

            if (!$result['success']) {
                $errors = $result['error-codes'] ?? [];
                Log::warning('reCAPTCHA v3 verification failed', ['errors' => $errors]);
                return ['success' => false, 'error' => 'Verification failed', 'errors' => $errors];
            }

            $score = $result['score'] ?? 0;
            $actionMatch = $action ? ($result['action'] ?? '') === $action : true;

            return [
                'success' => true,
                'score' => $score,
                'action' => $result['action'] ?? null,
                'action_match' => $actionMatch,
                'low_score' => $score < $this->v3Threshold,
            ];
        } catch (\Exception $e) {
            Log::error('reCAPTCHA v3 verification exception', ['error' => $e->getMessage()]);
            return ['success' => false, 'error' => 'Verification exception'];
        }
    }

    /**
     * Verify reCAPTCHA v2 token
     */
    public function verifyV2(?string $token): array
    {
        if (empty($token)) {
            return ['success' => false, 'error' => 'Token is missing'];
        }

        try {
            $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
                'secret' => $this->secretKey,
                'response' => $token,
            ]);

            $result = $response->json();

            if (!$result || !isset($result['success'])) {
                Log::warning('reCAPTCHA v2 verification failed: Invalid response', ['response' => $result]);
                return ['success' => false, 'error' => 'Invalid response from reCAPTCHA'];
            }

            if (!$result['success']) {
                $errors = $result['error-codes'] ?? [];
                Log::warning('reCAPTCHA v2 verification failed', ['errors' => $errors]);
                return ['success' => false, 'error' => 'Verification failed', 'errors' => $errors];
            }

            return [
                'success' => true,
                'hostname' => $result['hostname'] ?? null,
            ];
        } catch (\Exception $e) {
            Log::error('reCAPTCHA v2 verification exception', ['error' => $e->getMessage()]);
            return ['success' => false, 'error' => 'Verification exception'];
        }
    }

    /**
     * Get site key for frontend
     */
    public function getSiteKey(): string
    {
        return $this->siteKey;
    }
}
