<?php
namespace App\Controller\Api;

use App\Controller\AppController;
use Cake\Auth\DefaultPasswordHasher;
use Firebase\JWT\JWT;
use Cake\Core\Configure;

class LoginController extends AppController
{
    public function initialize(): void
    {
        parent::initialize();
        $this->loadComponent('RequestHandler');
        $this->RequestHandler->renderAs($this, 'json');
        $this->loadModel('Users');
    }

    public function index()
    {
        $this->request->allowMethod(['post']);
        $data = $this->request->getData();

        $user = $this->Users->findByEmail($data['email'])->first();

        if ($user && (new DefaultPasswordHasher)->check($data['password'], $user->password)) {
            // ✅ JWT トークンを発行
            $payload = [
                'sub' => $user->id,
                'exp' => time() + 604800, // 1週間
            ];
            $jwt = JWT::encode($payload, Configure::read('Security.jwtSecret'), 'HS256');

            $this->set([
                'success' => true,
                'data' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'token' => $jwt
                ],
                '_serialize' => ['success', 'data']
            ]);
            return;
        }

        $this->set([
            'success' => false,
            'message' => 'Invalid email or password',
            '_serialize' => ['success', 'message']
        ]);
        return;
    }
}
