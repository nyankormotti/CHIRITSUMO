<?php
$siteTitle = 'お問い合わせ';
require('head.php');
?>

<body>

    <!--ヘッダー -->
    <?php
    require('header.php');
    ?>

    <!-- メインコンテンツ -->
    <section class="main">
        <h1 class="title">お問い合わせ</h1>

        <div class="form">
            <form action="" method="post" class="form contact-form1">
                <div class="contact-form2">
                    <div class="area-msg"></div>

                    <label class="">
                        <p class="form-name">お名前<span>&nbsp;&nbsp;&nbsp;&nbsp;※10文字以内にてご入力ください</span></p>
                        <input type="text" name="name" class="contact-name">
                        <!-- <p class="name-su"><span class="name-count">0</span><span>/ 10</span></p> -->
                        <div class="area-msg"></div>
                    </label>

                    <label class="">
                        <p class="form-email">メールアドレス<span></span></p>
                        <input type="text" name="email">
                        <div class="area-msg"></div>
                    </label>

                    <label class="form-group">
                        <p class="comment">内容<span class="help-block">&nbsp;&nbsp;&nbsp;&nbsp;※200文字以内にてご入力ください</span></p>
                        <textarea name="comment" id="" cols="63" rows="8"></textarea>
                        <div class="comment-su"><span class="comment-count">0</span><span>/ 200</span></div>
                        <!-- <div class="area-msg"></div> -->
                    </label>


                    <div class="btn-contner">
                        <input type="submit" name="submit" class="btn btn-mid" value="送信">
                    </div>
                </div>

            </form>
        </div>
    </section>


    <!-- フッター -->
    <?php
    require('footer.php');
    ?> 