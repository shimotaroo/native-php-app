
    <footer id='footer'>
        Copyright <a href="index.php">KoSoDATE</a> .All Rights Reserved.<br>
        <a href="portfolio.php">下田祐太朗のポートフォリオ</a>
    </footer>

    <script src='https://code.jquery.com/jquery-3.4.1.min.js'></script>

    <script>

        $(function(){

            //フッターを最下部に固定
            var $footer = $('#footer');
            if(window.innerHeight > $footer.offset().top + $footer.outerHeight() ) {
                $footer.attr({'style': 'position:fixed; top:' + (window.innerHeight - $footer.outerHeight()) + 'px;' });
            }

            //メッセージを表示
            var $jsShowMsg = $('#js-show-msg');
            var msg = $jsShowMsg.text();
            if(msg.replace(/^[\s ]+[\s ]+$/g, "").length) {
                $jsShowMsg.slideToggle('slow');
                setTimeout(function(){ $jsShowMsg.slideToggle('slow'); }, 3000);
            }

            //画像ライブプレビュー
            var $dropArea = $('.area-drop');
            var $fileInput = $('.input-file');
            $dropArea.on('dragover', function(e){
                e.stopPropagation();
                e.preventDefault();
                $(this).css('border', '3px #ccc dashed');
            });

            $dropArea.on('dragleave', function(e){
                e.stopPropagation();
                e.preventDefault();
                $(this).css('border', 'none');
            });

            $fileInput.on('change', function(e){
                $dropArea.css('border', 'none');
                var file = this.files[0];  //file配列にファイルが入っています
                var $img = $(this).siblings('.prev-img'); //jQueryのsiblingsメソッドで兄弟のimgを取得
                var fileReader = new FileReader();  //ファイルを読み込むFileReaderオブジェクト
                
                //読み込みが完了した際のイベントハンドラー。imgのsrcにデータをセット
                fileReader.onload =function(e){
                    $img.attr('src', e.target.result).show();
                };

                //画像の読み込み
                fileReader.readAsDataURL(file);
            });

            //テキストエリアカウント
            var $countUp = $('#count');
            var $countView = $('#js-counter-view');

            $countUp.onload = function(e){
                $countView.html($(this).val().length);
            };

            $countUp.on('keyup', function(e){
                $countView.html($(this).val().length);
            });
            

            //お気に入り登録・削除
            var $favorite;
            var favoriteReviewId;

            $favorite = $('.js-click-like') || null; //nullというのはnull値で、「変数の中身は空ですよ」と明示するために使う値
            favoriteReviewId = $favorite.data('reviewid') || null;

            if(favoriteReviewId !== undefined && favoriteReviewId !== null) {

                $favorite.on('click', function(){
                    var $this = $(this);
                    selectReviewId = $(this).data('reviewid');
                    $.ajax({
                        type: "POST",
                        url: "ajaxFavorite_kc.php",
                        data: { reviewId : selectReviewId }
                    }).done(function( data ){
                        console.log('Ajax Success');
                        //クラス属性をtoggleで付け外しする
                        $this.toggleClass('active');
                    }).fail(function( msg ) {
                        console.log('Ajax Error');
                    });
                });
            }

        });

    </script>

</body>
</html>
