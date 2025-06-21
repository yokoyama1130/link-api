<?php

namespace App\Controller\Api;

use App\Controller\AppController;
use Cake\Http\Exception\UnauthorizedException;
use Cake\Log\Log;

class PostsController extends AppController
{
    public function initialize(): void
    {
        parent::initialize();
        $this->loadComponent('RequestHandler');
        $this->loadComponent('Authentication.Authentication');
        $this->loadModel('Posts');
    }

    public function index()
    {
        $posts = $this->Posts->find()->all();
        $this->set([
            'success' => true,
            'data' => $posts,
            '_serialize' => ['success', 'data']
        ]);
    }

    public function add()
    {
        $this->request->allowMethod(['post']);
    
        // ログインユーザーの確認
        $user = $this->Authentication->getIdentity();
        if (!$user) {
            throw new \Cake\Http\Exception\UnauthorizedException('ログインしてください');
        }
    
        \Cake\Log\Log::debug('ログインユーザー: ' . print_r($user, true));
    
        $data = $this->request->getData();
        $data['user_id'] = $user->get('sub');
    
        $post = $this->Posts->newEntity($data);
    
        if ($this->Posts->save($post)) {
            $this->set([
                'success' => true,
                'post' => $post,
                '_serialize' => ['success', 'post']
            ]);
        } else {
            $this->set([
                'success' => false,
                'errors' => $post->getErrors(),
                '_serialize' => ['success', 'errors']
            ]);
        }
    }
    
}

