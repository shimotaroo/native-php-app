<?php

//共通変数・関数ファイルを読み込む
require('function_kc.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「　マイページ　');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

?>


<!--head-->
<?php
    $siteTitle = 'HOME';
    require('head_kc.php');
?>

<body>

    <!--ヘッダー-->
    <?php
        require('header_kc.php');
    ?>

    <div id='main' class='site-width'>

        <?php 
            if(empty($_SESSION['user_id'])) {
        ?>
        <div class='main-explain-container'>
            <h2>
                <span>KoSoDATE</span>（コソダテ）は<br>
                育児に励むママ・パパのための情報共有サービスです。<br>
            </h2>
            <p class='main-explain'>
                苦労したこと、豆知識など、育児の中で感じたことを投稿してください。<br>
                自分にとっては当たり前の情報が誰かの役に立つ貴重な情報かもしれません。<br>
            </p>
            <p class='main-explain-2'>
                まずはユーザー登録して、投稿してみよう
            </p>
            <p class='main-signup'>
                <a href='signup_kc.php' class='js-link'><i class="fas fa-user-plus"></i>ユーザー登録する</a>
            </p>
            <p class='main-explain-2'>
                ユーザー登録済みの方はログイン
            </p>
            <p class='main-login'>
                <a href='login_kc.php' class='js-link'><i class="fas fa-sign-in-alt"></i>ログインする</a>
            </p>

        </div>
        <?php 
            }
        ?>

        <div class='choice-text'>
            <p>
                お子様の年齢を選んでください
            </p>
        </div>

        <div class='main-pic-container'>

            <panel class='main-panel'>
                <div class='main-pics'>
                    <a href='reviewpage01.php'><img src="image/main-pic01.jpg" alt="" class='image'></a>
                    <div class='js-hover-show'><a href='reviewpage01.php'>投稿する</a></div>
                </div>
                <p class='age'>
                    生後0ヶ月〜1ヶ月
                </p>
            </panel>
            
            <panel class='main-panel'>
                <div class='main-pics'>
                    <a href='reviewpage02.php'><img src="image/main-pic02.jpg" alt=""class='image'></a>
                    <div class='js-hover-show'><a href='reviewpage02.php'>投稿する</a></div>
                </div>
                <p class='age'>
                    生後1ヶ月〜3ヶ月
                </p>
            </panel>

            <panel class='main-panel'>
                <div class='main-pics'>
                    <a href='reviewpage03.php'><img src="image/main-pic03.jpg" alt=""class='image'></a>
                    <div class='js-hover-show'><a href='reviewpage03.php'>投稿する</a></div>
                </div>
                <p class='age'>
                    生後3ヶ月〜6ヶ月
                </p>
            </panel>
        </div>  

        <div class='main-pic-container'>
            <panel class='main-panel'>
                <div class='main-pics'>
                    <a href='reviewpage04.php'><img src="image/main-pic04.jpg" alt="" class='image'></a>
                    <div class='js-hover-show'><a href='reviewpage04.php'>投稿する</a></div>
                </div>
                <p class='age'>
                    生後6ヶ月〜９ヶ月
                </p>
            </panel>
            
            <panel class='main-panel'>
                <div class='main-pics'>
                    <a href='reviewpage05.php'><img src="image/main-pic05.jpg" alt="" class='image'></a>
                    <div class='js-hover-show'><a href='reviewpage05.php'>投稿する</a></div>
                </div>
                <p  class='age'>
                    生後9ヶ月〜12ヶ月(1歳)
                </p>
            </panel>

            <panel class='main-panel'>
                <div class='main-pics'>
                    <a href='reviewpage06.php'><img src="image/main-pic06.jpg" alt="" class='image'></a>
                    <div class='js-hover-show'><a href='reviewpage06.php'>投稿する</a></div>
                </div>
                <p class='age'>
                    1歳〜2歳
                </p>
            </panel>
        </div>  

        <div class='main-pic-container'>
            <panel class='main-panel'>
            <div class='main-pics'>
                    <a href='reviewpage07.php'><img src="image/main-pic07.jpg" alt="" class='image'></a>
                    <div class='js-hover-show'><a href='reviewpage07.php'>投稿する</a></div>
                </div>
                <p class='age'>
                    2歳〜3歳
                </p>
            </panel>
            
            <panel class='main-panel'>
                <div class='main-pics'>
                    <a href='reviewpage08.php'><img src="image/main-pic08.jpg" alt="" class='image'></a>
                    <div class='js-hover-show'><a href='reviewpage08.php'>投稿する</a></div>
                </div>
                <p class='age'>
                    3歳〜4歳    
                </p>
            </panel>

            <panel class='main-panel'>
                <div class='main-pics'>
                    <a href='reviewpage09.php'><img src="image/main-pic09.jpg" alt="" class='image'></a>
                    <div class='js-hover-show'><a href='reviewpage09.php'>投稿する</a></div>
                </div>
                <p class='age'>
                    4歳〜6歳
                </p>
            </panel>
        </div>  

    </div>

    <script src='https://code.jquery.com/jquery-3.4.1.min.js'></script>

    <script>

        $(function(){

            $('.js-hover-show').hide();

            $('.main-pics').mouseover(function(){
                $(this).find('.image').css({
                    "transform": "scale(1.1)",
                    "transition-duration": "0.3s",
                    "opacity": "0.6"
                });
            });
            
            $('.main-pics').mouseleave(function(){
                $(this).find('.image').css({
                    "transform": "scale(1)",
                    "transition-duration": "0.3s",
                    "opacity": "1"
                });
            });

            $('.main-pics').mouseover(function(){
                $(this).children('.js-hover-show').fadeIn();
            });
            $('.main-pics').mouseleave(function(){
                $(this).children('.js-hover-show').fadeOut();
            });

            //「ユーザー登録する」の文字色切り替え
            $('.main-signup').mouseover(function(){
                $(this).children('.js-link').css("color", "#00a1e9");
            });
            $('.main-signup').mouseleave(function(){
                $(this).children('.js-link').css("color", "#fff");
            });

            //「ログインする」の文字色切り替え
            $('.main-login').mouseover(function(){
                $(this).children('.js-link').css("color", "#f03");
            });
            $('.main-login').mouseleave(function(){
                $(this).children('.js-link').css("color", "#fff");
            });

        });

    </script>


    <!--フッター-->
    <?php
        require('footer_kc.php');
    ?>
