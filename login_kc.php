<?php

//共通変数・関数ファイルを読み込む
require('function_kc.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「　ログインページ　');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

//ログイン認証
require('auth_kc.php');

//========================
//ログイン画面処理
//========================
//post送信されていた場合
if(!empty($_POST)) {
    debug('POST送信があります。');

    //変数にユーザー情報を代入
    $email = $_POST['email'];
    $pass = $_POST['pass'];
    $pass_save = (!empty($_POST['pass_save'])) ? true : false; //ショートハンド（略記法）

    //未入力チェック
    validNotEntered($email, 'email');
    validNotEntered($pass, 'pass');

    //emialの形式チェック
    validEmail($email, 'email');
    //emailの最大文字数チェック
    validMaxLen($email, 'email');

    //パスワードの半角英数字チェック
    validHarf($pass, 'pass');
    //パスワードの最大文字数チェック
    validMaxLen($pass, 'pass');
    //パスワードの最小文字数チェック
    validMinLen($pass, 'pass');

    if(empty($err_msg)) {
        debug('バリデーションOKです。');

        //例外処理
        try {
            //DB接続
            $dbh = dbConnect();
            //SQL文作成
            $sql = 'SELECT password,id FROM users WHERE email = :email AND delete_flg = 0';
            $data = array(':email' => $email);
            //クエリ実行
            $stmt = queryPost($dbh, $sql, $data);
            //クエリ結果の値を取得
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            debug('クエリ結果の中身：'.print_r($result, true));

            //パスワード照合
            if(!empty($result) && password_verify($pass, array_shift($result))) {
                debug('パスワードがマッチしました。');

                //ログイン有効期限（デフォルトを1時間とする）
                $sessionLimit = 60*60;
                //最終ログイン日時を現在日時に更新する
                $_SESSION['login_date'] = time();

                //ログイン保持にチェックがある場合
                if(!empty($pass_save)) {
                    debug('ログイン保持にチェックがあります。');
                    //ログイン有効期限を30日にしてセット
                    $_SESSION['login_limit'] = $sessionLimit*24*30;

                }else {
                    debug('ログイン保持にチェックがありません。');
                    //ログイン保持しないので、ログイン有効期限を1時間後にセット
                    $_SESSION['login_limit'] = $sessionLimit;
                }

                //ユーザーIDをセッションに格納
                $_SESSION['user_id'] = $result['id'];

                debug('セッション変数の中身：'.print_r($_SESSION, true));  
                debug('マイページへ遷移します。');

                $_SESSION['msg_success'] = SUC02;

                header('Location:mypage_kc.php');

            }else {
                debug('パスワードがマッチしませんでした。');
                $err_msg['common'] = MSG10;
            }

        } catch(Exception $e) {
            error_log('エラー発生：' .$e -> getMessage());
            $err_msg['common'] = MSG08;
        }
    }
}

debug('画面表示処理終了>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>');

?>


<!--head-->
<?php
    $siteTitle = 'ログイン';
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
            <h2><i class="fas fa-sign-in-alt"></i>ログイン</h2>
            <div class='error-message common-message'>
                    <?php
                        if(!empty($err_msg['common'])) echo $err_msg['common'];
                    ?>
            </div>
            <form action="" method='post' class='form'>
                <label class="<?php if(!empty($err_msg['email'])) echo 'err'; ?>">
                    メールアドレス
                    <input type="text" name='email'>
                </label>
                <div class='error-message'>
                    <?php
                        if(!empty($err_msg['email'])) echo $err_msg['email'];
                    ?>
                </div>

                <label class="<?php if(!empty($err_msg['pass'])) echo 'err'; ?>">
                    パスワード
                    <input type="password" name='pass'>
                </label>
                <div class='error-message'>
                    <?php
                        if(!empty($err_msg['pass'])) echo $err_msg['pass'];
                    ?>
                </div>

                <label>
                    <input type="checkbox" name='pass_save'>次回ログインを省略する
                </label>

                <div class='button-container'>
                    <input type="submit" value='ログインする'>
                </div>

            </form>

        </div>

    </div>

    <!--フッター-->
    <?php
        require('footer_kc.php');
    ?>
