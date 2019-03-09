<?php
$siteTitle = '会員登録';
require('head.php');
?>

<body>

    <!-- ヘッダー -->
    <?php
    require('header.php');
    ?>

    <!-- メインコンテンツ -->
    <section class="main">
        <h1 class="title">会員登録</h1>

        <div class="form">
            <form action="" method="post" class="form">
                <div class="area-msg"></div>
                <p class="form-name">お名前<span>&nbsp;&nbsp;&nbsp;&nbsp;※10文字以内にてご入力ください</span></p>
                <input type="text" name="name">
                <div class="area-msg"></div>
                <p class="form-email">メールアドレス<span></span></p>
                <input type="text" name="email">
                <div class="area-msg"></div>
                <p class="form-password">パスワード<span>&nbsp;&nbsp;&nbsp;&nbsp;※半角英数字にてご入力ください</span></p>
                <input type="password" name="pass">
                <div class="area-msg"></div>
                <p class="form-password">パスワード(再入力)<span></span></p>
                <input type="password" name="pass_re">

                <div class="btn-contner">
                    <input type="submit" name="submit" class="btn btn-mid" value="登録"><br>
                </div>
            </form>
        </div>
    </section>

    <!-- フッター -->
    <?php
    require('footer.php');
    ?> 