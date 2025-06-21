<?php
namespace App\Controller\Api;

use App\Controller\AppController;

class UsersController extends AppController
{
    public function initialize(): void
    {
        parent::initialize();
        $this->loadComponent('RequestHandler');
        $this->RequestHandler->renderAs($this, 'json');
        $this->loadModel('Users');
    }

    public function add()
    {
        $this->request->allowMethod(['post']);
        $user = $this->Users->newEmptyEntity();
        $user = $this->Users->patchEntity($user, $this->request->getData());

        if ($this->Users->save($user)) {
            $this->set([
                'success' => true,
                'data' => $user,
                '_serialize' => ['success', 'data'],
            ]);
        } else {
            $this->set([
                'success' => false,
                'errors' => $user->getErrors(),
                '_serialize' => ['success', 'errors'],
            ]);
        }
    }
}
