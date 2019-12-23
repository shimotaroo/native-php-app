<?php

//共通変数・関数ファイルを読み込む
require('function_kc.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「　マイページ　');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

//================================
// 画面処理
//================================
//ログイン認証
require('auth_kc.php');

// 画面表示用データ取得
//================================
//ユーザー情報取得
$dbFormData = getUser($_SESSION['user_id']);
debug('$ログイン中のユーザー情報：'.print_r($dbFormData, true));

$u_id = $_SESSION['user_id'];
//自分の投稿を取得
$myReviewData = getMyReviews($u_id);
//自分がお気に入りした投稿を取得
$myFavoriteData = getMyFavorites($u_id);

//DBからきちんとデータが取れているかのチェックは行わず、取れなければ何も表示しないことにする

debug('取得した自分の投稿データ：'.print_r($myReviewData, true));
debug('取得したお気に入りデータ：'.print_r($myFavoriteData, true));

debug('画面表示処理終了 <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<');

?>

<!--head-->
<?php
    $siteTitle = 'マイページ';
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

        <div class='mypage-container'>

            <!--ニックネーム-->
            <div class='mypage-name'>
                <p>
                    <span><?php echo $dbFormData['name']; ?></span>さん
                </p>
            </div>

            <!--プロフィール画像-->
            <div class='mypage-img'>
                <img src="<?php echo showImg($dbFormData['pic']); ?>"  alt="">
            </div>

            <div class='mypage-overflow'>
                <div class='mypage-edit'>
                    <p>
                        <a href='profEdit_kc.php' class='js-link'><i class="fas fa-user-edit"></i>プロフィール編集</a>
                    </p>
                </div>
            </div>
        

            <div class='mypage-info'>
                <?php 
                    if(!empty($dbFormData['prof'])) {
                ?>
                    <p>
                        <?php echo $dbFormData['prof']; ?>
                    </p>
                <?php
                    }else{
                ?>
                    <p>
                        まだ自己紹介が設定されていません
                    </p>
                <?php
                    }
                ?>

            </div>

            <!--タブメニュー-->
            <div class='mypage-tab-container'>

                <ul class='mypage-tab'>
                    <li class='active'><i class="far fa-comment-dots"></i>自分の投稿</li>
                    <li><i class="fas fa-heart"></i>お気に入り</li>
                </ul>

                <div class='mypage-area'>
                    <section class='show' id='js-scroll-bottom'>
                    <?php
                        if(!empty($myReviewData)){
                            foreach($myReviewData as $key => $val) {
                    ?>
                                <div class='myreview-container'>

                                    <div class='user-name'>
                                        <p><?php echo sanitize($val['name']); ?></p>
                                    </div>
                                    <div class='avatar'>
                                        <img src="<?php echo showImg(sanitize($val['pic'])); ?>" alt="" class='avatar'>
                                    </div>
                                    <div class='myreview-text'>
                                        <span class='triangle'></span>
                                        <p><?php echo sanitize($val['review_comment']); ?></p>
                                    </div>
                                    <div class='myreview-date'>
                                        <?php echo sanitize($val['review_date']); ?>
                                    </div>

                                </div>

                        <?php
                            }
                            }else {
                        ?>
                                <p style="text-align:center;line-height:20;">まだ投稿してません</p>
                        <?php
                            }
                        ?>
                    </section>
                    <section id='js-scroll-bottom'>
                        <?php
                            if(!empty($myFavoriteData)){
                                foreach($myFavoriteData as $key => $val) {
                        ?>
                                    <div class='favorite-container'>

                                        <div class='user-name'>
                                            <p><?php echo sanitize($val['name']); ?></p>
                                        </div>
                                        <div class='avatar'>
                                            <img src="<?php echo showImg(sanitize($val['pic'])); ?>" alt="" class='avatar'>
                                        </div>
                                        <div class='favorite-text'>
                                            <span class='triangle'></span>
                                            <p><?php echo sanitize($val['review_comment']); ?></p>
                                        </div>
                                        <div class='favorite-date'>
                                            <?php echo sanitize($val['review_date']); ?>
                                        </div>

                                    </div>

                        <?php
                            }
                            }else {
                        ?>
                                <p style="text-align:center;line-height:20;">お気に入りした投稿はありません</p>
                        <?php
                            }
                        ?>
                    </section>
                </div>

            </div>

            <div class='mypage-overflow'>
                <div class='mypage-withdraw'>
                    <p>
                        <a href='withdrawal_kc.php' class='js-link'><i class="fas fa-user-slash"></i>退会</a>
                    </p>
                </div>
            </div>

        </div>

    </div>

    <script src='https://code.jquery.com/jquery-3.4.1.min.js'></script>

    <script>

        $(function(){

            //「プロフィール編集」の文字色切り替え
            $('.mypage-edit').mouseover(function(){
                $(this).find('.js-link').css("color", "#444");
            });
            $('.mypage-edit').mouseleave(function(){
                $(this).find('.js-link').css("color", "#fff");
            });

            //「退会」の文字色切り替え
            $('.mypage-withdraw').mouseover(function(){
                $(this).find('.js-link').css("color", "#444");
            });
            $('.mypage-withdraw').mouseleave(function(){
                $(this).find('.js-link').css("color", "#fff");
            });

            //タブメニューの切替
            $('.mypage-tab li').on('click', function(){

                //クリックされたタブの順番を変数に格納
                var index = $(this).index();
                console.log(index);

                //クリック済みタブのデザインを設定したcssのクラス（active)を一旦削除
                $('.mypage-tab li').removeClass('active');

                //クリックされたタブにクリック済みデザインを適用する
                $(this).addClass('active');

                //コンテンツを一旦非表示にし、クリックされた順番のコンテンツのみを表示
                $('.mypage-area section').removeClass('show').eq(index).addClass('show');
            });

            
            $(function(){
                //scrollHeightは要素のスクロールビューの高さを取得するもの
                $('#js-scroll-bottom').animate({scrollTop: $('#js-scroll-bottom')[0].scrollHeight}, 'fast');
            });


        });


</script>


    <!--フッター-->
    <?php
        require('footer_kc.php');
    ?>
    