<?php

namespace App\Services;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Ramsey\Uuid\Uuid;

class TokenService
{
    private string $secret;
    private string $algorithm;
    
    public function __construct()
    {
        $this->secret = $_ENV['JWT_SECRET'] ?? 'your-secret-key-here';
        $this->algorithm = $_ENV['JWT_ALGORITHM'] ?? 'HS256';
    }
    
    public function generateAccessToken(array $user): string
    {
        $payload = [
            'iss' => $_ENV['APP_URL'] ?? 'http://localhost',
            'aud' => $_ENV['APP_URL'] ?? 'http://localhost',
            'iat' => time(),
            'exp' => time() + ($_ENV['JWT_EXPIRATION'] ?? 3600),
            'sub' => $user['id'],
            'user' => [
                'id' => $user['id'],
                'nome' => $user['nome'],
                'email' => $user['email'],
                'is_superuser' => $user['is_superuser'] ?? false
            ]
        ];
        
        return JWT::encode($payload, $this->secret, $this->algorithm);
    }
    
    public function generateRefreshToken(string $userId): string
    {
        $payload = [
            'iss' => $_ENV['APP_URL'] ?? 'http://localhost',
            'aud' => $_ENV['APP_URL'] ?? 'http://localhost',
            'iat' => time(),
            'exp' => time() + (30 * 24 * 60 * 60), // 30 dias
            'sub' => $userId,
            'type' => 'refresh'
        ];
        
        return JWT::encode($payload, $this->secret, $this->algorithm);
    }
    
    public function generatePasswordRecoveryToken(string $userId): string
    {
        $payload = [
            'iss' => $_ENV['APP_URL'] ?? 'http://localhost',
            'aud' => $_ENV['APP_URL'] ?? 'http://localhost',
            'iat' => time(),
            'exp' => time() + (60 * 60), // 1 hora
            'sub' => $userId,
            'type' => 'password_recovery'
        ];
        
        return JWT::encode($payload, $this->secret, $this->algorithm);
    }
    
    public function validateToken(string $token): ?array
    {
        try {
            $decoded = JWT::decode($token, new Key($this->secret, $this->algorithm));
            return (array) $decoded;
        } catch (\Exception $e) {
            return null;
        }
    }
    
    public function validatePasswordRecoveryToken(string $token): ?string
    {
        $decoded = $this->validateToken($token);
        
        if (!$decoded) {
            return null;
        }
        
        if ($decoded['type'] !== 'password_recovery') {
            return null;
        }
        
        return $decoded['sub'];
    }
    
    public function generateApiKey(): string
    {
        return 'ross_' . Uuid::uuid4()->toString();
    }
    
    public function generateUuid(): string
    {
        return Uuid::uuid4()->toString();
    }
    
    public function generateShortToken(int $length = 6): string
    {
        $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $token = '';
        
        for ($i = 0; $i < $length; $i++) {
            $token .= $characters[rand(0, strlen($characters) - 1)];
        }
        
        return $token;
    }
}

