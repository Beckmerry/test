<?php 
APP::uses('AppController', 'Controller');
class UsersController extends AppController {
    public $uses = array('users');
    public $helpers = array('Html', 'Form', 'Flash');
    public $components = array('Session', 'Auth' =>array(
        'allowedActions' => array('index', 'add', 'login')));

    public function aftersave($created, $options = array()) {
        parent::aftersave($created, $options);

        App::uses('CakeSession', 'Model/Datasource');
        CakeSession::write('Auth', $this->findById(AuthComponent::user('id')));

        return true;
    }

    public $paginate = array(
        'Post' => array(
            'limit' => 10,
            'order' => array('Post.id' => 'desc')),

        'User' => array(
            'limit' => 10,
            'order' => array('User.id' => 'desc'))
        );

    public function index() {
        $this->loadModel('User');
        $this->layout = 'my_layout';
        if($this->request->is('post')) {
            $this->User->set($this->request->data);
            if($this->User->validates()) {
                $this->User->create();
                if($this->User->save($this->request->data)) {
                    $this->redirect(array('action' => 'add'));
                }else{
                    $this->Session->setFlash("エラー：登録失敗");
                }
            }else{
                $this->render('index');
            }
        }
    }

    public function add() {
        $this->loadModel('User');
        $this->layout = 'my_layout';
        $box = $this->User->find('first', array("fields" => "MAX(User.id) as max_id"));
        $id = $box[0]['max_id'];
        $user = $this->User->findById($id);
        $this->set('user', $user);

    }

    public function login() {
        $this->loadModel('User');
        $this->layout = 'my_layout';
        if($this->request->is('post')) {
            $this->User->set($this->request->data);
            $this->User->validate = $this->User->validate2;
            if($this->User->validates()) {
                if($this->Auth->login()) {
                    $user_data = $this->Auth->user();
                    $this->redirect('http://localhost:8888/twitter/users/tweet');
                }else{
                    return $this->Session->setFlash('ユーザー名、パスワードの組み合わせが違うようです。', 'default', array(), 'auth');
                }
            }
        }
    }

    public function tweet() {
        $this->loadModel('User');
        $this->loadModel('Post');
        $this->layout = 'my_layout2';
        $box = $this->Post->find('first', array("fields" => "MAX(Post.id) as max_id"));
        $id = $box[0]['max_id'];
        $twe = $this->Post->findById($id);
        $this->set('twe', $twe);
        $this->set('user', $this->Auth->user());
        $user_data = $this->Auth->user();
        $this->set('tweets', $this->paginate('Post'));
        $this->set('tweet', $this->Post->read());
        if($this->request->is('post')) {
            $this->Post->set($this->request->data);
            if($this->Post->validates()) {
                $this->Post->create();
                if($this->Post->save($this->request->data)) {
                    $this->redirect('http://localhost:8888/twitter/users/tweet');
                }else{
                    $this->Session->setFlash("エラー：登録失敗");
                }
            }
        }
    }

    public function tweets($id = null) {
        $this->loadModel('User');
        $this->loadModel('Post');
        $this->layout = 'my_layout2';
        $box = $this->Post->find('first', array("fields" => "MAX(Post.id) as max_id"));
        $id = $box[0]['max_id'];
        $twe = $this->Post->findById($id);
        $this->set('twe', $twe);
        $this->set('user', $this->Auth->user());
        $user_data = $this->Auth->user();
        $this->set('tweets', $this->paginate('Post'));
        $this->set('tweet', $this->Post->read());
        if($this->request->is('post')) {
            $this->Post->set($this->request->data);
            if($this->Post->validates()) {
                $this->Post->create();
                if($this->Post->save($this->request->data)) {
                    $this->redirect('http://localhost:8888/twitter/users/tweet');
                }else{
                    $this->Session->setFlash("エラー：登録失敗");
                }
            }
        }

    }

    public function follow() {
        $this->loadModel('User');
        $this->layout = 'my_layout2';
        $this->set('user', $this->Auth->user());
        $user_data = $this->Auth->user();
        $this->set('follows', $this->paginate('User'));
    }

    public function follower($id = null) {
        $this->loadModel('Follow');
        $this->loadModel('User');

        // ログインユーザー情報の取得
        $user = $this->Auth->user();
        $this->set('user', $user);

        // 保存処理は先に書いておいた方が良いです。何よりも先に条件分岐させたいので。
        if($this->request->is('post')) {
        // ここで、送信されてきたデータに何が入っているか確認します。

            $this->Follow->set($this->request->data);
            $this->Follow->create();
            if($this->Follow->save($this->request->data)) {
                $this->Session->setFlash("成功です");
                $this->redirect('http://localhost:8888/twitter/users/follower/'.$user['id']);
            }else{
                $this->Session->setFlash("エラー：失敗");
            }
        }
        
        // 全ユーザー情報の取得
        $this->set('follows', $this->paginate('User'));
        $follower = $this->User->find('all');
        $this->set('follower', $follower);

        $follow_result = $this->Follow->find('count',
            array('conditions' => array('Follow.user_id' => $user['id'], 'Follow.follow_id' => $follower['User']['id'])));
        $this->set('follow_result', $follow_result);
        var_dump($follow_result);


        // ビューの指定は最後に記入しましょう。
        $this->layout = 'my_layout2';
    }
    // public function follower($id = null) {
    //     $this->loadModel('Follow');
    //     $this->loadModel('User');
    //     $this->layout = 'my_layout2';
    //     $user = $this->Auth->user();
    //     $this->set('user', $user);
    //     $user_data = $this->Auth->user();
    //     $this->set('follows', $this->paginate('User'));
    //     $this->set('follower', $this->User->find('all'));
    //         if($this->request->is('post')) {
    //             $this->Follow->set($this->request->data);
    //             $this->Follow->create();
    //             if($this->Follow->save($this->request->data)) {
    //                 $this->Session->setFlash("成功です");
    //                 $this->redirect('http://localhost:8888/twitter/users/follower/'.$user['id']);
    //             }else{
    //                 $this->Session->setFlash("エラー：失敗");
    //             }
    //         }
    //     }

    public function search() {
        $this->loadModel('User');
        $this->loadModel('Post');
        $this->layout = 'my_layout2';
        //リクエストがPOSTで送られたデータが空白で無ければ
        if($this->request->is('post')){
            //Formの値を取得
            $data = $this->request->data['User']['username'];
            //POSTされたデータを曖昧検索
            $datas = $this->User->find('all',array(
               'conditions' => array('username like'=>'%'.$data.'%'))
            );
            $this->set('datas',$datas);
        }else{
             //POST以外の場合、一覧表示
             $datas = $this->User->find('all');
             $this->set('datas',$datas);
        }
        
        // $box = $this->Post->find('first', array("fields" => "MAX(Post.id) as max_id"));
        // $id = $box[0]['max_id'];
        // $twe = $this->Post->findById($id);
        // $this->set('twe', $twe);
    }

    public function delete($id) {
    $this->loadModel('Post');
    $this->layout = 'my_layout2';
    $this->Post->id=$id;
    if($this->Post->delete()){
        $this->redirect('http://localhost:8888/twitter/users/tweet/');
    } else {
        $this->Sessin->setFlash('削除失敗');
    }
}

    public function logout() {
        $this->Auth->logout();
        return $this->redirect(array('action' => 'login'));
    }
}
 ?>