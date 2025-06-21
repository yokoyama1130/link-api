<?php
namespace App\Controller\Api;

use App\Controller\AppController;
use Cake\Auth\DefaultPasswordHasher;
use Cake\ORM\TableRegistry;

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
            $this->set([
                'success' => true,
                'data' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email
                ],
                '_serialize' => ['success', 'data']
            ]);
        } else {
            $this->set([
                'success' => false,
                'message' => 'Invalid email or password',
                '_serialize' => ['success', 'message']
            ]);
        }
    }
}
