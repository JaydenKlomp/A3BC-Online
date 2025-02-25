<?php

namespace App\Controllers;
use App\Models\UserModel;
use CodeIgniter\Controller;

class Auth extends Controller
{
    public function login()
    {
        return view('auth/login');
    }

    public function register()
    {
        return view('auth/register');
    }

    public function attemptLogin()
    {
        $session = session();
        $model = new UserModel();

        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');

        $user = $model->where('email', $email)->first();

        if (!$user || !password_verify($password, $user['password'])) {
            return redirect()->to('/login')->with('error', 'Invalid credentials.');
        }

        if ($user['is_verified'] == 0) {
            return redirect()->to('/login')->with('error', 'Please verify your email first.');
        }

        // Sla de rol op in de sessie
        $session->set([
            'user_id' => $user['id'],
            'username' => $user['username'],
            'role' => $user['role'],
            'profile_picture' => $user['profile_picture'] ?? 'default.jpg',
        ]);

        return redirect()->to('/');
    }

    public function attemptRegister()
    {
        $model = new UserModel();

        $username = $this->request->getPost('username');
        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');
        $confirmPassword = $this->request->getPost('confirm_password');

        if ($password !== $confirmPassword) {
            return redirect()->to('/register')->with('error', 'Passwords do not match.');
        }

        $existingUser = $model->where('email', $email)->orWhere('username', $username)->first();

        if ($existingUser) {
            return redirect()->to('/register')->with('error', 'Email or Username already taken.');
        }

        $verificationToken = bin2hex(random_bytes(32));
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        $model->save([
            'username' => $username,
            'email' => $email,
            'password' => $hashedPassword,
            'verification_token' => $verificationToken,
            'is_verified' => 0,
            'role' => 'user' // Standaard nieuwe gebruiker krijgt 'user' rol
        ]);

        $this->sendVerificationEmail($email, $verificationToken);

        return redirect()->to('/email/confirm');
    }

    private function sendVerificationEmail($email, $token)
    {
        $apiKey = getenv("POSTMARK_API_KEY"); // API key uit .env bestand
        $senderEmail = getenv("EMAIL_FROM_EMAIL");
        $senderName = getenv("EMAIL_FROM_NAME");

        $confirmationLink = site_url("auth/verify/$token");
        $message = "<p>Click the link below to verify your email:</p>
                    <p><a href='$confirmationLink'>$confirmationLink</a></p>";

        $postData = [
            "From" => "$senderName <$senderEmail>",
            "To" => $email,
            "Subject" => "Confirm Your Email",
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

        if ($httpCode !== 200) {
            log_message('error', "Postmark API Error: $response");
        } else {
            log_message('info', "Verification email sent successfully to $email.");
        }
    }

    public function verifyEmail($token)
    {
        $model = new UserModel();
        $user = $model->getUserByToken($token);

        if (!$user) {
            return redirect()->to('/login')->with('error', 'Invalid verification link.');
        }

        $model->update($user['id'], [
            'is_verified' => 1,
            'verification_token' => null
        ]);

        return redirect()->to('/email/confirmed');
    }

    public function showConfirmEmailPage()
    {
        return view('email/confirm');
    }

    public function showEmailConfirmedPage()
    {
        return view('email/confirmed');
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/');
    }
}
