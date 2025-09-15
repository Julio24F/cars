<?php

namespace Config;

use Config\Services;

class JWT
{
    private $key = 'votre_cle_secrete_jwt_tres_securisee'; // Changez cette clé en production
    private $algorithm = 'HS256';

    public function encode($payload)
    {
        return \Firebase\JWT\JWT::encode($payload, $this->key, $this->algorithm);
    }

    public function decode($token)
    {
        try {
            return \Firebase\JWT\JWT::decode($token, new \Firebase\JWT\Key($this->key, $this->algorithm));
        } catch (\Exception $e) {
            return false;
        }
    }
}