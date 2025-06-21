<?php
namespace App\Controller\Component;

use Cake\Controller\Component;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Cake\Http\Exception\UnauthorizedException;

class JwtAuthComponent extends Component
{
    protected $_defaultConfig = [];

    public function getUserFromToken($request)
    {
        $authHeader = $request->getHeaderLine('Authorization');
        if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
            throw new UnauthorizedException('トークンがありません');
        }

        $token = str_replace('Bearer ', '', $authHeader);

        try {
            $secret = Configure::read('Security.jwtSecret');
            $decoded = JWT::decode($token, new Key($secret, 'HS256'));
            return [
                'id' => $decoded->sub
            ];
        } catch (\Exception $e) {
            throw new UnauthorizedException('トークンが無効です');
        }
    }
}
