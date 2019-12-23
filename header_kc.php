<header>
        <div class='site-width'>
            <h1><a href="index.php"><i class="fas fa-baby-carriage"></i>K<span>o</span>S<span>o</span>DATE</a></h1>
            <nav id='top-nav'>
                <ul>
                    <?php
                        if(empty($_SESSION['user_id'])) {
                    ?>
                            <li><a href='signup_kc.php'><i class="fas fa-user-plus"></i>ユーザー登録</a></li>
                            <li><a href='login_kc.php'><i class="fas fa-sign-in-alt"></i>ログイン</a></li>
                    <?php 
                        }else{
                    ?>
                            <li class='mypage'><a href='mypage_kc.php'><i class="fas fa-user"></i>マイページ</a></li>
                            <li><a href='logout_kc.php'><i class="fas fa-sign-out-alt"></i>ログアウト</a></li>
                    <?php
                        }
                    ?>
                </ul>
            </nav>
        </div>
    </header>