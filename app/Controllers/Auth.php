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

        if ($user && password_verify($password, $user['password'])) {
            $session->set([
                'user_id' => $user['id'],
                'username' => $user['username'], // ✅ Store username in session
            ]);
            return redirect()->to('/');
        } else {
            return redirect()->to('/login')->with('error', 'Invalid credentials.');
        }
    }

    public function attemptRegister()
    {
        $model = new UserModel();

        $username = $this->request->getPost('username');
        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');
        $confirmPassword = $this->request->getPost('confirm_password');

        // Check if passwords match
        if ($password !== $confirmPassword) {
            return redirect()->to('/register')->with('error', 'Passwords do not match.');
        }

        // Check if email or username already exists
        $existingUser = $model->where('email', $email)->orWhere('username', $username)->first();

        if ($existingUser) {
            if ($existingUser['email'] === $email) {
                return redirect()->to('/register')->with('error', 'Email is already in use.');
            } elseif ($existingUser['username'] === $username) {
                return redirect()->to('/register')->with('error', 'Username is already taken.');
            }
        }

        // If unique, hash password and save user
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        $model->save([
            'username' => $username,
            'email' => $email,
            'password' => $hashedPassword
        ]);

        return redirect()->to('/login')->with('success', 'Account created. You can now log in.');
    }


    public function logout()
    {
        session()->destroy();
        return redirect()->to('/');
    }
}
