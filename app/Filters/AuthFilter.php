<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Config\JWT;

class AuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $jwt = new JWT();
        $header = $request->getHeader('Authorization');
        
        if (!$header) {
            return service('response')->setJSON([
                'status' => 401,
                'message' => 'Token manquant'
            ])->setStatusCode(401);
        }

        $token = str_replace('Bearer ', '', $header->getValue());
        $decoded = $jwt->decode($token);

        if (!$decoded) {
            return service('response')->setJSON([
                'status' => 401,
                'message' => 'Token invalide ou expiré'
            ])->setStatusCode(401);
        }

        return $request;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Après traitement
    }
}