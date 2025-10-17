<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Http;

class WhatsappApi extends Model
{
    public static function whatsappInfo($template)
    {
        $authKey = '13dd1772186280d57';
        $baseURL = 'https://console.steviadigital.com/restapi/request.php';

        $templates = [
            'order_confirmed' => 7509,
            'order_on_the_way' => 7515,
            'order_cancelled'   => 7516,
            'delivered'   => 7517,
        ];

        return [
            'baseURL' => $baseURL,
            'authKey' => $authKey,
            'wid'     => $templates[$template] ?? null,
        ];
    }

    public static function sendWhatsappMessage($template, $mobile, $parameters = [])
    { return true;
        $whatsappInfo = self::whatsappInfo($template);

        if (!$whatsappInfo['wid']) {
            \Log::error("WhatsApp Template ID not found for template: $template");
            return false;
        }

        $queryParams = [
            'authkey'      => $whatsappInfo['authKey'],
            'mobile'       => $mobile,
            'country_code' => '91',
            'wid'          => $whatsappInfo['wid'],
        ];

        // Dynamically add parameters as 1, 2, 3...
        foreach ($parameters as $index => $value) {
            $queryParams[(string)($index + 1)] = $value;
        }

        $response = Http::get($whatsappInfo['baseURL'], $queryParams);

        if ($response->successful()) {
            return true;
        } else {
            \Log::error('WhatsApp API Error: ' . $response->body());
            return false;
        }
    }
}
