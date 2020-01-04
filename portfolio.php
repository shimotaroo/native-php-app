<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="style.css">
    <link href="https://fonts.googleapis.com/css?family=Lato&display=swap" rel="stylesheet">
    <link href="https://use.fontawesome.com/releases/v5.6.1/css/all.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Montserrat&display=swap" rel="stylesheet">

    <title>下田祐太朗のポートフォリオ</title>
</head>

<body>
    <header class="header">
        <h1 class="header_title">Yutaro Shimoda's Portfolio</h1>
        <nav class='header_nav'>
            <ul>
                <li>
                    <a href=".introdcution">Introduction</a>
                </li>
                <li>
                    <a href=".skills">Skills</a>
                </li>
                <li>
                    <a href=".appealpoint">Appeal Point</a>
                </li>
            </ul>
        </nav>
    </header>

    <section class="main-visual">
        <div class="main-visual_img">
            <img src="image/portfolio01.jpg" alt="メインビジュアルの写真">
        </div>
        <div class="main-visual_greeting">
            <div class="greeting-container">
                <p class="greeting-1">現在転職活動中です！</p>
                <p class="greeting-2">
                    このポートフォリオサイトをご覧いただき<br>
                    少しでも私のことを知っていただけたら幸いです<i class="far fa-laugh-beam"></i>
                </p>
            </div>
        </div>

    </section>

    <main>
        <section class="main introdcution">
            <h2><i class="far fa-user"></i>Introduction</h2>
            <p>自己紹介</p>
            <div class="main-container introduction">
                <div class="introduction-box-1">
                    <img src="image/portfolio02.jpg" alt="">
                    <P class="twitter"><a href="https://twitter.com/shimotaroo"><i class="fab fa-twitter"></i>Twitter</a></P>
                    <P class="github"><a href="https://github.com/shimotaroo"><i class="fab fa-github"></i>GitHub</a></P>
                    <P class="qiita"><a href="https://qiita.com/shimotaroo"><i class="fas fa-search-plus"></i>Qiita</a></P>
                </div>
                <div class="introduction-box-2">
                    <ul>
                        <i class="fas fa-check"></i>Personal
                        <li>下田 祐太朗（しもだゆうたろう）</li>
                        <li>1992年3月生まれ／27歳</li>
                        <li>福岡県北九州市出身</li>
                    </ul>
                    <ul>
                        <i class="fas fa-check"></i>Career
                        <li>2014年3月：九州工業大学 工学部 機械知能工学科卒業</li>
                        <li>2016年3月：九州工業大学大学院 機械工学専攻 卒業</li>
                        <li>2016年4月：大手重工メーカ 就職（東証一部）</li>
                    </ul>
                    <ul>
                        <i class="fas fa-check"></i>Hobby
                        <li>筋トレ</li>
                        <li>バスケ</li>
                        <li>妻と娘を喜ばせること</li>
                    </ul>

                </div>

            </div>
        </section>

        <section class="main skills">
            <h2><i class="fas fa-code"></i>Skills</h2>
            <P>使用可能言語・スキル</P>
            <section class="skills-container">
                <div class="skills-box">HTML</div>
                <div class="skills-box">CSS</div>
                <div class="skills-box">JavaScript</div>
                <div class="skills-box">PHP</div>
                <div class="skills-box">MySQL</div>
                <div class="skills-box">GitHub</div>
            </section>
        </section>

        <section class="main appealpoint">
            <h2><i class="far fa-hand-point-right"></i>Appeal Point</h2>
            <p>アピールポイント</p>
            <section class="appeal-container">
                <div class="appeal-box">
                    <p class="appeal-topic">コミュニケーション能力</p>
                    <p>マネジメント業務の経験があり<br>
                        社内外との密なコミュニケーションを<br>
                        取ることができます
                    </p>
                </div>
                <div class="appeal-box">
                    <p class="appeal-topic">好奇心旺盛</p>
                    <p>新たなことに果敢に<br>
                        チャレンジするのが好きです<br>
                        これからも人生でもどんどん<br>
                        チャレンジしていきます
                    </p>
                </div>
                <div class="appeal-box">
                    <p class="appeal-topic">継続力</p>
                    <p>スキルアップするために<br>
                        ストイックに努力を継続する<br>
                        力があります
                    </p>
                </div>
        </section>
    </main>

    <foooter>
        <div class="footer">
            <p>Copyright Yutaro Shimoda 2019 All Right Reserved.</p>
            <a href="index.php">KoSoDATEに戻る</a>
        </div>
    </foooter>

    <p class="page-top">
        <i class="fas fa-arrow-up"></i>
    </p>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script>
        $(function(){
            $('a[href^="."]').on("click", function(){
                var speed = 400;
                var href = $(this).attr("href");
                var target = $(href == "." || href == "" ? 'html' : href);
                var position = target.offset().top-100; 
                $('body,html').animate({scrollTop:position}, speed, 'swing');
                return false;
            });

            var pagetop = $('.page-top');
            $(window).scroll(function(){
                if($(this).scrollTop() > 300) {
                    pagetop.fadeIn();
                } else {
                    pagetop.fadeOut();
                }
            });
            pagetop.on("click", function(){
                $('body, html').animate({ scrollTop: 0 }, 500);
                return false;

            });
        });
    </script>


</body>
</html>