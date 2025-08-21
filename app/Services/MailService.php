<?php

namespace App\Services;

use Illuminate\Support\Facades\Mail;

class MailService
{
    /**
     * ส่งอีเมลง่าย ๆ ด้วยข้อความแบบ raw
     *
     * @param string|array $to  ผู้รับ (string หรือ array)
     * @param string       $subject หัวข้อ
     * @param string       $body เนื้อหา
     * @return void
     */
    public static function sendRaw($to, string $subject, string $body): void
    {
        Mail::raw($body, function ($message) use ($to, $subject) {
            $message->to($to)->subject($subject);
        });
    }
}
