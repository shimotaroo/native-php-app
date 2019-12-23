<?php

error_reporting(E_ALL); //E_STRICT以外のエラーを報告する
ini_set('display_errors', 'On'); //画面にエラーを表示させるか


//================================
// ログ
//================================
//ログを取るか
ini_set('log_errors', 'On');
//ログの出力ファイルを指示
ini_set('error_log', 'php_kc.log');

//================================
//デバッグ
//================================
//デバッグフラグ
$debug_flg = true;
//デバッグログ関数
function debug($str) {
    global $debug_flg;
    if(!empty($debug_flg)) {
        error_log('デバッグ：' .$str);
    }
}

//================================
//セッション準備・セッション有効期限を延ばす
//================================
//セッションファイルの置き場を変更する（/var/tmp以下に置くと30日は削除されない）
session_save_path("/var/tmp");
//ガーベージコレクションが削除するセッションの有効期限を設定（30日以上経っているものに対してだけ100分の1の確率で削除）
ini_set('session.gc_maxlifetime', 60*60*24*30);
//ブラウザを閉じても削除されないようにクッキー自体の有効期限を延ばす
ini_set('session.cookie_lifetime', 60*60*24*30);
//セッションを使う
session_start();
//現在のセッションIDを新しく生成したものと置き換える（なりすましのセキュリティ対策）
session_regenerate_id();

//================================
//画面表示処理開始ログ吐き出し関数
//================================
function debugLogStart() {
    debug('>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>画面表示処理開始');
    debug('セッションID：' .session_id());
    debug('セッション変数の中身：' .print_r($_SESSION, true));
    debug('現在日時タイムスタンプ：' .time());
    if(!empty($_SESSION['login_date']) && !empty($_SESSION['login_limit'])) {
        debug('ログイン期限日時タイムスタンプ：' .($_SESSION['login_date'] + $_SESSION['login_limit']));
    }
}

//定数
//================================
//エラーメッセージを定数に設定

define('MSG01', '入力必須です');
define('MSG02', '10文字以内で入力してください');
define('MSG03', 'E-mailの形式で入力してください');
define('MSG04', 'パスワード（再入力）が合っていません');
define('MSG05', '半角英数字のみご利用いただけます');
define('MSG06', '8文字以上で入力してください');
define('MSG07', '256文字以内で入力してください');
define('MSG08', 'エラーが発生しました。しばらく経ってからやり直してください。');
define('MSG09', 'そのEmailは既に登録されています');
define('MSG10', 'メールアドレスまたはパスワードが違います');
define('SUC01', 'ユーザー登録しました');
define('SUC02', 'ログインしました');
define('SUC03', '投稿しました');
define('SUC04', 'プロフィールを編集しました');


//=================================
//エラーメッセージ格納用の配列
//=================================

$err_msg = array();

//=================================
//バリデーション関数
//=================================

//バリデーション関数（未入力チェック）
function validNotEntered($str, $key) {
    if(empty($str)) {
        global $err_msg;
        $err_msg[$key] = MSG01;
    }
}

//バリデーション関数（ニックネーム最大文字数チェック）
function validNameMaxLen($str, $key) {
    if(mb_strlen($str) > 11) {
        global $err_msg;
        $err_msg[$key] = MSG02;
    }
}

//バリデーション関数（E-mail形式チェック）
function validEmail($str, $key) {
    if(!preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/", $str)) {
        global $err_msg;
        $err_msg[$key] = MSG03;
    }
}

//バリデーション関数（重複チェック）
function validEmailDup($email) {
    global $err_msg;

    //例外処理
    try {
        //DBへ接続
        $dbh = dbConnect();
        //SQl文作成
        $sql = 'SELECT count(*) FROM users WHERE email = :email AND delete_flg = 0';
        $data = array(':email' => $email);
        //クエリ実行
        $stmt = queryPost($dbh, $sql, $data);
        //クエリ結果の値を取得
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        //array_shift関数は配列の先頭を取り出す関数です。クエリ結果は配列形式で入っているので、array_shiftで1つ目だけ取り出して判定します
        if(!empty(array_shift($result))) {
            $err_msg['email'] = MSG09;
        }
    } catch (Exception $e) {
        error_log('エラー発生：' . $e->getMessage());
        $err_msg['common'] = MSG08;
    }
}

//バリデーション関数（同値チェック）
function validMatch($str1, $str2, $key) {
    if($str1 !== $str2){
        global $err_msg;
        $err_msg[$key] = MSG04;
    }
}

//バリデーション関数（半角チェック）
function validHarf($str, $key) {
    if(!preg_match("/^[a-zA-Z0-9]+$/", $str)){
        global $err_msg;
        $err_msg[$key] = MSG05;
    }
}

//バリデーション関数（最小文字数チェック）
function validMinLen($str, $key) {
    if(mb_strlen($str) < 8){
        global $err_msg;
        $err_msg[$key] = MSG06;
    }
}

//バリデーション関数（最大文字数チェック）
function validMaxLen($str, $key) {
    if(mb_strlen($str) > 256){
        global $err_msg;
        $err_msg[$key] = MSG07;
    }
}

function dbConnect() {
    //DBへの接続準備
    $dsn = 'mysql:dbname=shimotaroo_kosodate;host=ホスト名;charset=utf8';
    $user = 'ユーザー名';
    $password = 'パスワード';
    $options = array(
        //SQL実行失敗時にはエラーコードのみ設定
        PDO::ATTR_ERRMODE => PDO::ERRMODE_SILENT,
        //デフォルトフェッチモードを連想配列形式に設定
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        //バッファードクエリを使う（一度結果をセットを全て取得し、サーバー負荷を軽減）
        //SELECTで得た結果に対してもrowCountメソッドを使えるようにする
        PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,
    );
    

    //PDOオブジェクトを生成（DBへ接続）
    $dbh = new PDO($dsn, $user, $password, $options);
    return $dbh;
}

function queryPost($dbh, $sql, $data) {
    //クエリー作成
    $stmt = $dbh->prepare($sql);
    //プレースホルダーに値をセットし、SQL文を実行
    if(!$stmt->execute($data)){
        debug('クエリに失敗しました。');
        debug('失敗したSQL：'.print_r($stmt, true));
        $err_msg['common'] = MSG08;
        return 0;
    }
    debug('クエリに成功しました。');
    return $stmt;

}

//================================
// ユーザー情報取得
//================================
function getUser($u_id) {
    debug('ユーザー情報を取得します。');

    //例外処理
    try {
        //DBへ接続
        $dbh = dbConnect();
        //SQL文作成
        $sql = 'SELECT * FROM users WHERE id = :u_id AND delete_flg = 0';
        $data = array(':u_id' => $u_id);
        //クエリ実行
        $stmt = queryPost($dbh, $sql, $data);

        //クエリ成功の場合
//        if($stmt) {
//            debug('クエリ成功。');
//        } else {
//            debug('クエリ失敗。');
//        }
        //クエリ結果のデータを１レコード返却
        if($stmt) {
            return $stmt -> fetch(PDO::FETCH_ASSOC);
        } else {
            return false;
        }

    } catch(Exception $e) {
        error_log('エラー発生：' .$e->getMessage());
    }

    //クエリ結果のデータを返却
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// フォーム入力保持
//function getFormData($str){
    //global $dbFormData;
    // ユーザーデータがある場合
    //if(!empty($dbFormData)){
        //フォームのエラーがある場合
        //if(!empty($err_msg[$str])){
            //POSTにデータがある場合
            //if(isset($_POST[$str])){//金額や郵便番号などのフォームで数字や数値の0が入っている場合もあるので、issetを使うこと
                //return $_POST[$str];
            //}else{
                //ない場合（フォームにエラーがある＝POSTされてるハズなので、まずありえないが）はDBの情報を表示
                //return $dbFormData[$str];
            //}
        //}else{
            //POSTにデータがあり、DBの情報と違う場合（このフォームも変更していてエラーはないが、他のフォームでひっかかっている状態）
            //if(isset($_POST[$str]) && $_POST[$str] !== $dbFormData[$str]){
                //return $_POST[$str];
            //}else{//そもそも変更していない
                //return $dbFormData[$str];
            //}
        //}
    //}else{
      //if(isset($_POST[$str])){
        //return $_POST[$str];
      //}
    //}
//}  

//画像処理
function uploadImg($file, $key) {
    debug('画像アップロード処理開始');
    debug('FILE情報：'.print_r($file, true));

    //issetは入っていたらtrue、is_intは数字だったらtrue
    if(isset($file['error']) && is_int($file['error'])) {
        try {
            //バリデーション
            //$file['error']の値を確認。配列内には「UPLOAD_ERR_OK」などの定数が入っている。
            //「UPLOAD_ERR_OK」などの定数はphpでファイルアップロード時に自動的に定義される。定数には値として0や1などの数値が入っている。
            switch($file['error']) {
                case UPLOAD_ERR_OK: //OK
                    break;
                case UPLOAD_ERR_NO_FILE:    //ファイル未選択の場合
                    throw new RuntimeException('ファイルが選択されていません');
                case UPLOAD_ERR_INI_SIZE:  //php.ini定義の最大サイズを超過した場合
                case UPLOAD_ERR_FORM_SIZE: //フォーム定義の最大サイズを超過した場合
                    throw new RuntimeException('ファイルサイズが大きすぎます');
                default:                   //その他の場合
                    throw new RuntimeException('その他のエラーが発生しました');
            }

            //$file['mime']の値はブラウザ側で偽装可能なので、MIMEタイプを自前でチェックする
            //exif_imagetype関数は「IMAGETYPE_GIF」、「IMAGETYPE_JPEG」などの定数を返す
            $type = @exif_imagetype($file['tmp_name']);
            if(!in_array($type, [IMAGETYPE_GIF, IMAGETYPE_JPEG, IMAGETYPE_PNG], true)) { // 第三引数にはtrueを設定すると厳密にチェックしてくれるので必ずつける
                throw new RuntimeException('画像形式が未対応です');
            }

            //ファイルデータからSHA-1ハッシュを取ってファイル名を決定し、ファイルを保持する
            //ハッシュ化しておかないとアップロードされたファイル名をそのままで保存してしまうと同じファイル名がアップロードされる可能性があり、
            //DBにパスを保存した場合、どっちの画像パスなのか判断つかなくなってしまう
            //image_type_to_extension関数はファイルの拡張子を取得するもの
            $path = 'uploads/'.sha1_file($file['tmp_name']).image_type_to_extension($type);

            if(!move_uploaded_file($file['tmp_name'], $path)) { //ファイルを移動する
                throw new RuntimeException('ファイル保存時にエラーが発生しました');
            }

            //保存したファイルパスのパーミッション（権限）を変更する
            chmod($path, 0644);

            debug('ファイルは正常にアップロードされました');
            debug('ファイルパス：'.$path);
            return $path;

        } catch (RuntimeException $e) {
            debug($e->getMessage());
            global $err_msg;
            $err_msg[$key] = $e->getMessage();
        }
    }
}

function getReview_1() {
    debug('レビュー情報を取得します。');

    //例外処理
    try {
        $dbh = dbConnect();
        //SQL文作成
        $sql = 'SELECT * FROM reviews LEFT JOIN users ON reviews.review_user_id = users.id  WHERE users.delete_flg = 0 AND reviews.page_id = 1  ORDER BY review_date ASC ';
        // クエリ実行
        $stmt = $dbh -> query($sql);

        if($stmt) {
            //クエリ結果の全データを返却
            debug('クエリ成功');
            return $stmt->fetchAll();
        } else {
            debug('クエリ失敗');
            return false;
        }
    
    } catch(Exception $e) {
        error_log('エラー発生：'.$e->getMessage());
    }
}

function getReview_2() {
    debug('レビュー情報を取得します。');

    //例外処理
    try {
        $dbh = dbConnect();
        //SQL文作成
        $sql = 'SELECT * FROM reviews LEFT JOIN users ON reviews.review_user_id = users.id  WHERE users.delete_flg = 0 AND reviews.page_id = 2  ORDER BY review_date ASC ';
        // クエリ実行
        $stmt = $dbh -> query($sql);

        if($stmt) {
            //クエリ結果の全データを返却
            debug('クエリ成功');
            return $stmt->fetchAll();
        } else {
            debug('クエリ失敗');
            return false;
        }
    
    } catch(Exception $e) {
        error_log('エラー発生：'.$e->getMessage());
    }
}

function getReview_3() {
    debug('レビュー情報を取得します。');

    //例外処理
    try {
        $dbh = dbConnect();
        //SQL文作成
        $sql = 'SELECT * FROM reviews LEFT JOIN users ON reviews.review_user_id = users.id  WHERE users.delete_flg = 0 AND reviews.page_id = 3  ORDER BY review_date ASC ';
        // クエリ実行
        $stmt = $dbh -> query($sql);

        if($stmt) {
            //クエリ結果の全データを返却
            debug('クエリ成功');
            return $stmt->fetchAll();
        } else {
            debug('クエリ失敗');
            return false;
        }
    
    } catch(Exception $e) {
        error_log('エラー発生：'.$e->getMessage());
    }
}

function getReview_4() {
    debug('レビュー情報を取得します。');

    //例外処理
    try {
        $dbh = dbConnect();
        //SQL文作成
        $sql = 'SELECT * FROM reviews LEFT JOIN users ON reviews.review_user_id = users.id  WHERE users.delete_flg = 0 AND reviews.page_id = 4 ORDER BY review_date ASC ';
        // クエリ実行
        $stmt = $dbh -> query($sql);

        if($stmt) {
            //クエリ結果の全データを返却
            debug('クエリ成功');
            return $stmt->fetchAll();
        } else {
            debug('クエリ失敗');
            return false;
        }
    
    } catch(Exception $e) {
        error_log('エラー発生：'.$e->getMessage());
    }
}

function getReview_5() {
    debug('レビュー情報を取得します。');

    //例外処理
    try {
        $dbh = dbConnect();
        //SQL文作成
        $sql = 'SELECT * FROM reviews LEFT JOIN users ON reviews.review_user_id = users.id  WHERE users.delete_flg = 0 AND reviews.page_id = 5 ORDER BY review_date ASC ';
        // クエリ実行
        $stmt = $dbh -> query($sql);

        if($stmt) {
            //クエリ結果の全データを返却
            debug('クエリ成功');
            return $stmt->fetchAll();
        } else {
            debug('クエリ失敗');
            return false;
        }
    
    } catch(Exception $e) {
        error_log('エラー発生：'.$e->getMessage());
    }
}

function getReview_6() {
    debug('レビュー情報を取得します。');

    //例外処理
    try {
        $dbh = dbConnect();
        //SQL文作成
        $sql = 'SELECT * FROM reviews LEFT JOIN users ON reviews.review_user_id = users.id  WHERE users.delete_flg = 0 AND reviews.page_id = 6 ORDER BY review_date ASC ';
        // クエリ実行
        $stmt = $dbh -> query($sql);

        if($stmt) {
            //クエリ結果の全データを返却
            debug('クエリ成功');
            return $stmt->fetchAll();
        } else {
            debug('クエリ失敗');
            return false;
        }
    
    } catch(Exception $e) {
        error_log('エラー発生：'.$e->getMessage());
    }
}

function getReview_7() {
    debug('レビュー情報を取得します。');

    //例外処理
    try {
        $dbh = dbConnect();
        //SQL文作成
        $sql = 'SELECT * FROM reviews LEFT JOIN users ON reviews.review_user_id = users.id  WHERE users.delete_flg = 0 AND reviews.page_id = 7 ORDER BY review_date ASC ';
        // クエリ実行
        $stmt = $dbh -> query($sql);

        if($stmt) {
            //クエリ結果の全データを返却
            debug('クエリ成功');
            return $stmt->fetchAll();
        } else {
            debug('クエリ失敗');
            return false;
        }
    
    } catch(Exception $e) {
        error_log('エラー発生：'.$e->getMessage());
    }
}

function getReview_8() {
    debug('レビュー情報を取得します。');

    //例外処理
    try {
        $dbh = dbConnect();
        //SQL文作成
        $sql = 'SELECT * FROM reviews LEFT JOIN users ON reviews.review_user_id = users.id  WHERE users.delete_flg = 0 AND reviews.page_id = 8 ORDER BY review_date ASC ';
        // クエリ実行
        $stmt = $dbh -> query($sql);

        if($stmt) {
            //クエリ結果の全データを返却
            debug('クエリ成功');
            return $stmt->fetchAll();
        } else {
            debug('クエリ失敗');
            return false;
        }
    
    } catch(Exception $e) {
        error_log('エラー発生：'.$e->getMessage());
    }
}

function getReview_9() {
    debug('レビュー情報を取得します。');

    //例外処理
    try {
        $dbh = dbConnect();
        //SQL文作成
        $sql = 'SELECT * FROM reviews LEFT JOIN users ON reviews.review_user_id = users.id  WHERE users.delete_flg = 0 AND reviews.page_id = 9 ORDER BY review_date ASC ';
        // クエリ実行
        $stmt = $dbh -> query($sql);

        if($stmt) {
            //クエリ結果の全データを返却
            debug('クエリ成功');
            return $stmt->fetchAll();
        } else {
            debug('クエリ失敗');
            return false;
        }
    
    } catch(Exception $e) {
        error_log('エラー発生：'.$e->getMessage());
    }
}



//================================
// お気に入り登録機能
//================================

function isLike($u_id, $r_id) {
    debug('お気に入り情報があるか確認します。');
    debug('ユーザーID：'.$u_id);
    debug('レビューID：'.$r_id);

    //例外処理
    try {
        //DBへ接続
        $dbh = dbConnect();
        //SQL分作成
        $sql = 'SELECT * FROM favorite WHERE review_id = :r_id AND user_id = :u_id';
        $data = array(':r_id' => $r_id, ':u_id' => $u_id );
        //クエリ実行
        $stmt = queryPost($dbh, $sql, $data);

        if($stmt->rowCount()) {
            debug('お気に入りです。');
            return true;
        } else {
            debug('お気に入りではありません。');
            return false;
        }

    } catch (Exception $e) {
        error_log('エラー発生：' .$e->getMessage());
    }
}

//================================
// マイページ表示用関数
//================================
//自分の投稿取得
function getMyReviews($u_id) {
    debug('自分の投稿情報を取得します。');
    debug('ユーザーID：'.$u_id);
    //例外処理
    try {
        //DBへ接続
        $dbh = dbConnect();
        //SQL文作成
        $sql = 'SELECT * FROM reviews LEFT JOIN users ON reviews.review_user_id = users.id WHERE review_user_id = :u_id AND review_delete_flg = 0';
        $data = array(':u_id' => $u_id);
        //クエリ実行
        $stmt = queryPost($dbh, $sql, $data);

        if($stmt) {
            //クエリ結果のデータを全レコード取得
            return $stmt->fetchAll();
        } else {
            return false;
        }

    }catch (Exception $e) {
        error_log('エラー発生：'.$e-getMessage());
    }
}

//自分がお気に入りした投稿を取得
function getMyFavorites($u_id) {
    debug('自分のお気に入り情報を取得します。');
    debug('ユーザーID：'.$u_id);
    //例外処理
    try {
        //DBへ接続
        $dbh = dbConnect();
        //SQL文作成
        $sql = 'SELECT * FROM favorite LEFT JOIN reviews ON favorite.review_id = reviews.review_id LEFT JOIN users ON reviews.review_user_id = users.id WHERE favorite.user_id = :u_id AND favorite.delete_flg = 0';
        $data = array(':u_id' => $u_id);
        //クエリ実行
        $stmt = queryPost($dbh, $sql, $data);

        if($stmt) {
            //クエリ結果のデータを全レコード取得
            return $stmt->fetchAll();
        } else {
            return false;
        }

    }catch (Exception $e) {
        error_log('エラー発生：'.$e-getMessage());
    }
}

//================================
// その他
//================================
//サニタイズ
function sanitize($str) {
    return htmlspecialchars($str, ENT_QUOTES);
}

//画像表示用関数
function showImg($path) {
    if(empty($path)) {
        return 'image/sample-imaeg.png';
    } else {
        return $path;
    }
}

//sessionを１回だけ取得できる（ページ上部メッセージ表示用）
function getSessionFlash($key) {
    debug('メッセージ表示処理します');
    debug('$_SESSIONの中身：'.print_r($_SESSION, true));
    if(!empty($_SESSION[$key])) {
        $data = $_SESSION[$key];
        $_SESSION[$key] = '';
        debug('$dataの中身：'.print_r($data, true));
        return $data;
    }
}


?>