<?php

//プロフィール編集機能の処理フロー
//1.DBからユーザ情報を取得
//2.POSTされているかチェック
//  （フォームにはDBのデータを表示しているんもで、DBのデータのまま変更していなくてもPOSTされる）
//3.DBの情報とPOSTされた情報を比べて違いがあれば、バリデーションチェック
//4.DB接続
//5.レコード更新
//6.マイページへ遷移

//共通変数・関数ファイルを読み込む
require('function_kc.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「　プロフィール編集ページ　');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

//ログイン認証
require('auth_kc.php');

//========================
//ログイン画面処理
//========================
//DBからユーザー情報を取得

$dbFormData = getUser($_SESSION['user_id']);

debug('取得したユーザー情報：'.print_r($dbFormData, true));

//post送信されていた場合
if(!empty($_POST)) {

    debug('post送信があります。');
    debug('POST情報：'.print_r($_POST, true));
    debug('FILE情報：'.print_r($_FILES, true));  //画像アップロード機能を使うときに用いる

    //変数にユーザー情報を代入
    $name = $_POST['name'];
    $prof = $_POST['prof'];
    $email = $_POST['email'];
    //画像をアップロードし、パスを格納
    $pic = (!empty($_FILES['pic']['name'])) ? uploadImg($_FILES['pic'], 'pic') : '';
    //画像をPOSTしていない（登録していない）が既にDBに登録されている場合、DBのパスを入れる（POSTには反映されないので）
    $pic = (empty($pic) && !empty($dbFormData['pic'])) ? $dbFormData['pic'] : $pic;

    //DB情報と入力情報が異なる場合にバリデーションを行う
    if($dbFormData['name'] !== $name) {
        //名前の最大文字数をチェック
        validNameMaxLen($name, 'name');
        //未入力チェック
        validNotEntered($name, 'name');
    }

    if($dbFormData['prof'] !== $prof) {
        //自己紹介の最大文字数チェック
        validMaxLen($prof, 'prof');
    }

    if($dbFormData['email'] !== $email) {
        //最大文字数チェック
        validMaxLen($email, 'email');
        if(empty($err_msg['email'])) {
            //emailの重複チェック
            validEmailDup($email);
        }
        //emailの形式チェック
        validEmail($email, 'email');
        //未入力チェック
        validNotEntered($name, 'name');
    }

    if(empty($err_msg)) {
        debug('バリデーションOKです。');

        try {
            //DBへ接続
            $dbh = dbConnect();
            //SQL文作成
            $sql1 = 'UPDATE users SET name = :name, email = :email, prof = :prof, pic = :pic WHERE id = :user_id';
            //データ流し込み
            $data = array(':name' => $name, ':email' => $email, ':prof' => $prof, ':pic' => $pic, ':user_id' => $dbFormData['id']);

            //クエリ実行
            $stmt = queryPost($dbh, $sql1, $data);

            //クエリ成功の場合
            if($stmt) {
                $_SESSION['msg_success'] = SUC04;
                debug('マイページに遷移します。');
                header('Location:mypage_kc.php');
            }
                
        } catch (Exception $e) {
            error_log('エラー発生：' .$e->getMessage());
            $err_msg['common'] = MSG08;
        }
    }
}
debug('画面表示処理終了 <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<');

?>

<!--head-->
<?php
    $siteTitle = 'プロフィール編集';
    require('head_kc.php');
?>

<body>

    <!--ヘッダー-->
    <?php
        require('header_kc.php');
    ?>

    <!--メインコンテンツ-->
    <div id='contents' class='site-width'>

        <div class='form-container'>
        <h2><i class="fas fa-user-edit"></i>プロフィール編集</h2>
            <div class='error-message common-message'>
                    <?php
                        if(!empty($err_msg['common'])) echo $err_msg['common'];
                    ?>
            </div>
            <form action="" method='post' class='form' enctype="multipart/form-data">
                <div class='imgDrop-container'>
                    プロフィール画像
                    <label class='area-drop'>
                        <input type="hidden" name='MAX_FILE_SIZE' value='3145728'> <!--3M-->
                        <input type="file" name='pic' class='input-file'>
                        <img src="<?php echo showImg($dbFormData['pic']); ?>" alt="" class='prev-img' style="<?php if(empty($dbFormData['pic'])) echo 'display: none;' ?>">
                    </label>
                </div>

                <label >
                    ニックネーム
                    <input type="text" name='name' value="<?php echo $dbFormData['name']; ?>">
                </label>
                <div class='error-message'>
                    <?php
                        if(!empty($err_msg['name'])) echo $err_msg['name'];
                    ?>
                </div>

                <label >
                    自己紹介
                    <textarea name="prof" id="count" cols="30" rows="10"><?php echo $dbFormData['prof']; ?></textarea>
                </label>
                <p class='counter-text'><span id='js-counter-view'>0</span>／255文字</p>
                <div class='error-message'>
                    <?php
                        if(!empty($err_msg['prof'])) echo $err_msg['prof'];
                    ?>
                </div>

                <label >
                    メールアドレス
                    <input type="text" name='email' value="<?php echo $dbFormData['email']; ?>">
                </label>
                <div class='error-message'>
                    <?php
                        if(!empty($err_msg['email'])) echo $err_msg['email'];
                    ?>
                </div>

                <div class='button-container'>
                    <input type="submit" value='変更する'>
                </div>

            </form>
        </div>
    </div>

    <!--フッター-->
    <?php
        require('footer_kc.php');
    ?>
    