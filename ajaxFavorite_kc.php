<?php

//共通変数・関数ファイルを読み込む
require('function_kc.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「　Ajax　');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

//========================
//Ajax処理
//========================


//postがあり、ユーザーIDがあり、ログインしている場合
if(isset($_POST['reviewId']) && isset($_SESSION['user_id'])) {
    debug('POST送信があります。');
    $r_id = $_POST['reviewId'];
    debug('レビューID：'.$r_id);
    debug('セッションID：'.$_SESSION['user_id']);

    //例外処理
    try {
        //DBへ接続
        $dbh = dbConnect();
        //SQL分作成
        //レビューがあるか検索
        $sql = 'SELECT * FROM favorite WHERE review_id = :r_id AND user_id = :u_id';
        $data = array(':r_id' => $r_id, ':u_id' => $_SESSION['user_id'] );
        //クエリ実行
        $stmt = queryPost($dbh, $sql, $data);
        $resultCount = $stmt->rowCount();
        debug($resultCount);
        //レコードが１件でもある場合
        if(!empty($resultCount)) {
            //レコードを削除する
            $sql = 'DELETE FROM favorite WHERE review_id = :r_id AND user_id = :u_id';
            $data = array(':r_id' => $r_id, ':u_id' => $_SESSION['user_id'] );
            //クエリ実行
            $stmt = queryPost($dbh, $sql, $data);
        } else {
            //レコードを挿入する
            $sql = 'INSERT INTO favorite (review_id, user_id, create_date) VALUE (:r_id, :u_id, :date)';
            $data = array(':r_id' => $r_id, ':u_id' => $_SESSION['user_id'], ':date' => date('Y-m-d H:i:s'));
            //クエリ実行
            $stmt = queryPost($dbh, $sql, $data);
        }
        

    } catch (Exception $e) {
        error_log('エラー発生：'.$e-getMessage());
    }
}

debug('Ajax処理終了>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>');

?>