<?php

namespace App\Controller\Api;

use App\Controller\AppController;
use Cake\Http\Exception\UnauthorizedException;
use Cake\Log\Log;
use Cake\Filesystem\File;
use Cake\Utility\Text;

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
    
        $user = $this->Authentication->getIdentity();
        if (!$user) {
            throw new UnauthorizedException('ログインしてください');
        }
    
        $data = $this->request->getData();
        $data['user_id'] = $user->get('sub');
    
        $post = $this->Posts->newEntity($data);
    
        // 画像ファイルがあれば保存
        if (!empty($data['media']) && $data['media']->getError() === UPLOAD_ERR_OK) {
            $file = $data['media'];
            $filename = uniqid() . '_' . $file->getClientFilename();
            $file->moveTo(WWW_ROOT . 'uploads' . DS . $filename);
            $post->media_path = '/uploads/' . $filename;
        }
    
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

