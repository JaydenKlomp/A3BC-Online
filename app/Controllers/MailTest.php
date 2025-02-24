<?php

namespace App\Controllers;
use CodeIgniter\Controller;

class MailTest extends Controller
{
    public function sendTestEmail()
    {
        $apiKey = getenv("POSTMARK_API_KEY"); // Load from .env
        $senderEmail = getenv("EMAIL_FROM_EMAIL");
        $senderName = getenv("EMAIL_FROM_NAME");
        $receiverEmail = "j.klomp@a3bc.eu"; // Postmark Sandbox allows only this domain
        $subject = "Test Email from CodeIgniter (Postmark)";
        $message = "<p>This is a test email sent via Postmark API.</p>";

        $postData = [
            "From" => "$senderName <$senderEmail>",
            "To" => $receiverEmail,
            "Subject" => $subject,
            "HtmlBody" => $message,
            "MessageStream" => "outbound"
        ];

        $ch = curl_init("https://api.postmarkapp.com/email");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Accept: application/json",
            "X-Postmark-Server-Token: $apiKey",
            "Content-Type: application/json"
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode === 200) {
            echo "✅ Email Sent Successfully!";
        } else {
            echo "❌ Email Sending Failed: " . $response;
        }
    }
}
