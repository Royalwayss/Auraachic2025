<?php

namespace App\Services\Front;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class SmsService {
    protected $auth;
    protected $senderid;
    protected $baseUrl;

    public function __construct() {
        $this->auth = config('sms.auth');      // Get from config
        $this->senderid = config('sms.senderid');
        $this->baseUrl = config('sms.base_url');
    }

    public function sendSms($mobile, $message) { return ''; die(); exit;
        $encodedMessage = urlencode($message);
        $url = "{$this->baseUrl}?auth={$this->auth}&msisdn={$mobile}&senderid={$this->senderid}&message={$encodedMessage}";

        $response = Http::get($url);  // Laravel's cleaner way instead of curl

        Log::info("SMS sent to $mobile: $message");

        return $response->body();  // Return API response
    }

    public function sendOtp($mobile) {  return ''; die(); exit;
        $otp = rand(100000, 999999);
        cache()->put('otp_' . $mobile, $otp, now()->addMinutes(5));  // Store OTP for 5 mins

        $message = "Dear ON-VERS customer, Your one-time password is $otp and is valid for 2 mins. https://onvers.rtpltech.in/men";
        return $this->sendSms($mobile, $message);
    }
}
