<?php

namespace App\Controllers;

use App\Models\UserModel;
use Config\JWT;
use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;

class AuthController extends ResourceController
{
    use ResponseTrait;

    protected $jwt;
    protected $userModel;

    public function __construct()
    {
        $this->jwt = new JWT();
        $this->userModel = new UserModel();
    }

    public function register()
    {
        $data = [
            'username' => $this->request->getVar('username'),
            'email' => $this->request->getVar('email'),
            'password' => $this->request->getVar('password'),
            'created_at' => date('Y-m-d H:i:s')
        ];

        // Validation
        if (!$this->validate([
            'username' => 'required|min_length[3]',
            'email' => 'required|valid_email|is_unique[users.email]',
            'password' => 'required|min_length[6]'
        ])) {
            return $this->failValidationErrors($this->validator->getErrors());
        }

        if ($this->userModel->save($data)) {
            return $this->respondCreated([
                'status' => 201,
                'message' => 'Utilisateur créé avec succès'
            ]);
        } else {
            return $this->failServerError('Erreur lors de la création de l\'utilisateur');
        }
    }

    public function login()
    {
        $email = $this->request->getVar('email');
        $password = $this->request->getVar('password');

        $user = $this->userModel->getUserByEmail($email);

        if (!$user || !password_verify($password, $user['password'])) {
            return $this->failUnauthorized('Email ou mot de passe incorrect');
        }

        $payload = [
            'iat' => time(),
            'exp' => time() + 3600, // 1 heure
            'uid' => $user['id'],
            'email' => $user['email']
        ];

        $token = $this->jwt->encode($payload);

        return $this->respond([
            'status' => 200,
            'message' => 'Connexion réussie',
            'token' => $token,
            'user' => [
                'id' => $user['id'],
                'username' => $user['username'],
                'email' => $user['email']
            ]
        ]);
    }

    public function logout()
    {
        // Avec JWT stateless, on ne fait que supprimer le token côté client
        return $this->respond([
            'status' => 200,
            'message' => 'Déconnexion réussie'
        ]);
    }

    protected function validateToken()
    {
        $header = $this->request->getHeader('Authorization');
        if (!$header) {
            return false;
        }

        $token = str_replace('Bearer ', '', $header->getValue());
        $decoded = $this->jwt->decode($token);

        return $decoded ? $decoded : false;
    }
}