<?php

//共通変数・関数ファイルを読み込む
require('function_kc.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「　口コミ投稿ページ01　');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

//ログイン認証
require('auth_kc.php');

//$dbFormData = getUser($_SESSION['user_id']);

//================================
// 画面処理
//================================
$reviewDate = '';
//================================
// 画面表示用データ
//================================

$reviewData = getReview_3();
debug('$reviewDataの中身：'.print_r($reviewData, true));

//post送信されていた場合
if(!empty($_POST)) {
    debug('POST送信があります。');
    debug('POSTの中身：'.print_r($_POST, true));

    //バリデーションチェック
    $comment = (isset($_POST['comment'])) ? $_POST['comment'] : '';

    //最大文字数チェック
    validMaxLen($comment, 'comment');
    //未入力チェック
    validNotEntered($comment, 'comment');

    if(empty($err_msg)) {
        debug('バリデーションOKです。');

        //例外処理
        try {
            //DBへ接続
            $dbh = dbConnect();
            //SQL文作成
            $sql = 'INSERT INTO reviews (page_id, review_date, review_user_id, review_comment, review_create_date) VALUES (:page_id, :r_date, :r_user_id, :r_comment, :date)';
            $data = array(':page_id' => 3, ':r_date' => date('Y-m-d H:i:s'), ':r_user_id' => $_SESSION['user_id'], ':r_comment' => $comment, ':date' => date('Y-m-d H:i:s'));
            //クエリ実行
            $stmt = queryPost($dbh, $sql, $data);

            //クエリ成功の場合
            if($stmt) {
                $_POST = array(); //postをクリア

                $_SESSION['msg_success'] = SUC03;

                debug('口コミ投稿ページへ遷移します。');
                header('Location:reviewpage03.php'); //自分自身に遷移する
                exit();

            }

        } catch(Exception $e) {
            error_log('エラー発生：'. $e->getMessage());
            $err_msg['common'] = MSG08;
        }
    }
}
debug('画面表示処理終了 <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<');
?>

<!--head-->
<?php
    $siteTitle = '生後3ヶ月〜6ヶ月';
    require('head_kc.php');
?>
<body>

    <!--ヘッダー-->
    <?php
        require('header_kc.php');
    ?>

    <!--メッセージ表示-->
    <p id='js-show-msg' style='display:none;' class='msg-slide'>
        <?php echo getSessionFlash('msg_success'); ?>
    </p>


    <!--メインコンテンツ-->
    <div id='contents' class='site-width'>

        <!--写真＆説明-->
        <div class='area-title'>
            <div class='area-img'>
                <img src="image/main-pic03.jpg" alt="">
            </div>
            <div class='area-text'>
                <h2>生後3ヶ月〜6ヶ月</h2>
                <p>
                    嬉しかったこと<br>
                    大変だったこと <br>
                    ちょっと役立つ豆知識などなど<br>
                    お気軽に投稿してください<i class="far fa-comment"></i>
                </p>
            </div>
        </div>


        <!--投稿エリア-->
        <div class='area-sub-title'>
            <p><i class="far fa-edit"></i>投稿する</p>
        </div>

        <div class='send-reviw'>
            <div class='error-message common-message'>
                <?php
                    if(!empty($err_msg['common'])) echo $err_msg['common'];
                ?>
            </div>

            <form action="" method='post' class='form review-form'>

                <textarea name="comment"  cols="30" rows="10" class='review-textarea'></textarea>
                <div class='error-message'>
                    <?php
                        if(!empty($err_msg['comment'])) echo $err_msg['comment'];
                    ?>
                </div>

                <div class='button-container'>
                    <input type="submit" value='投稿する'>
                </div>

            </form>
        </div>

        <!--投稿内容表示-->
        <div class='area-sub-title'>
            <p><i class="far fa-comments"></i>みんなの投稿</p>
        </div>
        <div class='area-board' id='js-scroll-bottom'>
            <?php 
                if(!empty($reviewData)) {
                    foreach($reviewData as $key => $val) {
            ?>
                        <div class='review-container'>
                            <div class='user-name'>
                                <p><?php echo sanitize($val['name']); ?></p>
                            </div>
                            <div class='avatar'>
                                <img src="<?php echo showImg(sanitize($val['pic'])); ?>" alt="" class='avatar'>
                            </div>
                            <div class='review-text'>
                                <span class='triangle'></span>
                                <p><?php echo sanitize($val['review_comment']); ?></p>
                            </div>
                            <div class='review-favo'>
                                <i class="far fa-heart"></i>
                            </div>
                            <div class='review-date'>
                                <?php echo sanitize($val['review_date']); ?>
                            </div>
                        </div>
            <?php 
                    }
                } else {
            ?>
                    <p style="text-align:center;line-height:20;">投稿はまだありません</p>
            <?php 
                }
            ?>
        </div>

        <script src='https://code.jquery.com/jquery-3.4.1.min.js'></script>

        <script>
            $(function(){
                //scrollHeightは要素のスクロールビューの高さを取得するもの
                $('#js-scroll-bottom').animate({scrollTop: $('#js-scroll-bottom')[0].scrollHeight}, 'fast');
            });
        </script>

    </div>

    <!--フッター-->
    <?php
        require('footer_kc.php');
    ?>