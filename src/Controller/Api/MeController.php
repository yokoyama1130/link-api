<?php
namespace App\Controller\Api;

use App\Controller\AppController;

class MeController extends AppController
{
    public function initialize(): void
    {
        parent::initialize();
        $this->loadComponent('RequestHandler');
        $this->loadComponent('JwtAuth');
        $this->loadModel('Users');
        $this->loadComponent('Authentication.Authentication');
    }

    public function index()
    {
        $this->request->allowMethod(['get']);

        // トークン検証
        $userData = $this->JwtAuth->getUserFromToken($this->request);
        $user = $this->Users->get($userData['id']);

        // index() 内
        $user = $this->Authentication->getIdentity();
        if (!$user) {
            throw new UnauthorizedException('ログインしてください');
        }

        $this->set([
            'success' => true,
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
            '_serialize' => ['success', 'data'],
        ]);
    }
}
