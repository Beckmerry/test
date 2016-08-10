
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <?php
        $this->assign('title', 'タイムライン');
     ?>
</head>
<body>
    <div class='clearfix'>
        <h2><?php echo (h($user['username'])); ?>は　人をフォローされています</h2>
        <?php echo $this->Session->Flash(); ?>
        <div class="recent">
            ユーザー名/名前　　　　　　　　　　　　フォローする？
        <ul>
            <?php foreach($follower as $follower): ?>
            <li>
                <?php echo $this->Html->link($follower['User']['username'], 'http://localhost:8888/twitter/users/tweets/'.$follower['User']['id'], array('class' => 'user')); ?>
                <br>
                <?php echo h($follower['User']['name']); ?>
                <?php
                    echo $this->Form->create('Follow');
                    echo $this->Form->hidden('user_id', array('value' => $user['id']));
                    echo $this->Form->hidden('follow_id', array('value' => $follower['User']['id']));
                    var_dump($follow_result);
                    var_dump($user['id']);
                    if($follower['User']['id'] == $user['id']) {
                        echo $this->Form->end();
                    }elseif($follow_result ==! 0){
                        echo $this->Form->submit('YES', array('type' => 'submit', 'class' => 'change'));
                        echo $this->Form->end();
                    }
                ?>
                <hr>
            </li>
        <?php endforeach; ?>
        </ul>
        </div>
        <div class='user_info'>
                <?php
                    echo $this->Html->image('user_icon.png', array('class' => 'user_icon'));
                    echo '&emsp;';
                    echo $this->Html->link($user['username'], 'http://localhost:8888/twitter/users/tweets/'.$user['id'], array('class' => 'user_label'));
                ?>
              <br>
                <div class='follow_info clearfix'>
                    <div class="info">1<br><?php echo $this->Html->link('フォローをしている', 'http://localhost:8888/twitter/users/follow/'.$user['id']); ?></div>
                    <div class="info">2<br><?php echo $this->Html->link('フォローをされている', 'http://localhost:8888/twitter/users/follower/'.$user['id']); ?></div>
                    <div class="info">3<br><?php echo $this->Html->link('投稿数', 'http://localhost:8888/twitter/users/tweets/'.$user['id']); ?></div>
                </div>
            </div>
    </div>
        
    <?php 
        echo $this->Paginator->prev('< 前へ', array(), null, array('class' => 'prev'));
        echo $this->Paginator->counter(array('format' => '全%count%件' ));
        echo $this->Paginator->counter(array('format' => '{:page}/{:pages}ページを表示'));
        echo $this->Paginator->next('次へ >', array(), null, array('class' => 'next'));
    ?>
    </body>
</html>