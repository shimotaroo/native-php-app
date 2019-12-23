<?php

//共通変数・関数ファイルを読み込む
require('function_kc.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「　ユーザー登録ページ　');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();


//POST送信された場合
if(!empty($_POST)) {

debug('POSTの中身:'.print_r($_POST, true));

    //変数にユーザー情報を格納
    $name = $_POST['name'];
    $email = $_POST['email'];
    $pass = $_POST['pass'];
    $pass_re = $_POST['pass_re'];

    //未入力チェック
    validNotEntered($name, 'name');
    validNotEntered($email, 'email');
    validNotEntered($pass, 'pass');
    validNotEntered($pass_re, 'pass_re');

    if(empty($err_msg)) {

        //ニックネームの最大文字数チェック
        validNameMaxLen($name, 'name');

        //emailの形式チェック
        validEmail($email, 'email');
        //emailの最大文字数チェック
        validMaxLen($email, 'email');
        //emailの重複チェック
        validEmailDup($email);

        //パスワードの半角英数字チェック
        validHarf($pass, 'pass');
        //パスワードの最大文字数チェック
        validMaxLen($pass, 'pass');
        //パスワードの最小文字数チェック
        validMinLen($pass, 'pass');

        //パスワード（再入力）の最大数チェック
        validMaxLen($pass_re, 'pass_re');
        //パスワード（再入力）の最小文字数チェック
        validMinLen($pass_re, 'pass_re');

        if(empty($err_msg)) {

            //パスワードがパスワード（再入力）と合っているかチェック
            validMatch($pass, $pass_re, 'pass_re');

            if(empty($err_msg)) {

                debug('POSTのバリデーションOKです。');

                //例外処理
                try {
                    //DBへ接続
                    $dbh = dbConnect();
                    //SQL文作成
                    $sql = 'INSERT INTO users (name, email, password, login_time) VALUES (:name, :email, :password, :login_time)';
                    $data = array(':name' => $name, ':email' => $email, ':password' => password_hash($pass, PASSWORD_DEFAULT),
                                ':login_time' => date('Y-m-d H:i:s'));

                    //クエリ実行
                    $stmt = queryPost($dbh, $sql, $data);

                    //クエリ成功の場合
                    if($stmt) {
                        //ログイン有効期限（デフォルトを1時間とする）
                        $sessionLimit = 60*60;
                        //最終ログイン日時を現在日時に
                        $_SESSION['login_date'] = time();
                        $_SESSION['login_limit'] = $sessionLimit;
                        //ユーザーIDを格納
                        $_SESSION['user_id'] = $dbh->lastInsertId();
                        $_SESSION['msg_success'] = SUC01;

                        debug('セッションの中身：'.print_r($_SESSION, true));
                        header('Location:mypage_kc.php'); //マイページへ

//                    } else {
//                        error_log('クエリに失敗しました。');
//                        $err_msg['common'] = MSG08;
                    }

                    
                } catch(Exception $e) {
                    error_log('エラー発生：' . $e -> getMessage());
                    $err_msg['common'] = MSG08;
                }

            }
        }
    }    
}
?>

<!--head-->
<?php
    $siteTitle = 'ユーザー登録';
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
            <h2><i class="fas fa-user-plus"></i>ユーザー登録</h2>
            <div class='error-message common-message'>
                    <?php
                        if(!empty($err_msg['common'])) echo $err_msg['common'];
                    ?>
            </div>
            <form action="" method='post' class='form'>

                <label class="<?php if(!empty($err_msg['name'])) echo 'err'; ?>">
                    ニックネーム<span class='form-rule'>※10文字以内</span>
                    <input type="text" name='name' value="<?php if(!empty($_POST['name'])) echo $_POST['name']; ?>">
                </label>
                <div class='error-message'>
                    <?php
                        if(!empty($err_msg['name'])) echo $err_msg['name'];
                    ?>
                </div>

                <label class="<?php if(!empty($err_msg['email'])) echo 'err'; ?>">
                    メールアドレス
                    <input type="text" name='email' value="<?php if(!empty($_POST['email'])) echo $_POST['email']; ?>">
                </label>
                <div class='error-message'>
                    <?php
                        if(!empty($err_msg['email'])) echo $err_msg['email'];
                    ?>
                </div>


                <label class="<?php if(!empty($err_msg['pass'])) echo 'err'; ?>">
                    パスワード<span class='form-rule'>※英数字8文字以上</span>
                    <input type="password" name='pass'>
                </label>
                <div class='error-message'>
                    <?php
                        if(!empty($err_msg['pass'])) echo $err_msg['pass'];
                    ?>
                </div>

                <label class="<?php if(!empty($err_msg['pass_re'])) echo 'err'; ?>">
                    パスワード（再入力）
                    <input type="password" name='pass_re'>
                </label>
                <div class='error-message'>
                    <?php
                        if(!empty($err_msg['pass_re'])) echo $err_msg['pass_re'];
                    ?>
                </div>

                <div class='button-container'>
                    <input type="submit" value='登録する'>
                </div>
            </form>

        </div>

    </div>

    <!--フッター-->
    <?php
        require('footer_kc.php');
    ?>
