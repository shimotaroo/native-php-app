<?php

//共通変数・関数ファイルを読み込む
require('function_kc.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「　退会ページ　');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

//ログイン認証
require('auth_kc.php');

//========================
//画面処理
//========================
//post送信されていた場合
if(!empty($_POST)) {
    debug('POST送信があります。');

    //例外処理
    try {
        //DBへ接続
        $dbh = dbConnect();
        //SQL文作成
        $sql1 = 'UPDATE users SET delete_flg = 1 WHERE id = :user_id';
        $sql2 = 'UPDATE reviews SET review_delete_flg = 1 WHERE review_user_id = :user_id';
        $sql3 = 'UPDATE favorite SET delete_flg = 1 WHERE user_id = :user_id';

        //データ流し込み
        $data = array(':user_id' => $_SESSION['user_id']);

        //クエリ実行
        $stmt = queryPost($dbh, $sql1, $data);
        $stmt = queryPost($dbh, $sql2, $data);
        $stmt = queryPost($dbh, $sql3, $data);

        //クエリ実行成功の場合
        if($stmt) {

            //セッション削除
            session_destroy();
            debug('セッション変数の中身：'.print_r($_SESSION, true));
            debug('トップページに遷移します');
            header('Location:index.php');

        } else {
            debug('クエリが失敗しました。');
            $err_msg['common'] = MSG08;
        }

    } catch(Exception $e) {
        error_log('エラー発生：' .$e->getMessage());
        $err_msg['common'] = MSG08;
    }
}

debug('画面表示処理終了>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>');


?>

<!--head-->
<?php
    $siteTitle = '退会';
    require('head_kc.php');
?>

<body>
    <!--ヘッダー-->
    <?php
    require('header_kc.php');
    ?>

    <!--メインコンテンツ-->
    <div id=contents class='site-width'>

        <div class='form-container'>
            <h2><i class="fas fa-user-slash"></i>退会</h2>

            <div class='error-message common-message'>
                <?php
                    if(!empty($err_msg['common'])) echo $err_msg['common'];
                ?>
            </div>

            <div class='conf-message'>
                <p>本当に退会しますか？・・・</p>
            </div>
            
            <form action="" method='post' class='form'>

                <div class='button-withdraw'>
                    <input type="submit" value='退会する' name='submit'>
                    <input type="button" value='退会しない' onclick="location.href='mypage_kc.php'">
                </div>

            </form>

        </div>

    </div>

    <!--フッター-->
    <?php
        require('footer_kc.php');
    ?>

