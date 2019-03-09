<?php
$siteTitle = 'ログイン';
require('head.php');
?>

<body>

    <!-- ヘッダー -->
    <?php

    require('header.php');
    ?>

    <!-- メインコンテンツ -->
    <section class="main main-login">
        <h1 class="title">ログイン</h1>

        <div class="form">
            <form action="" mehod="post" class="form">
                <div class="area-msg"></div>
                <p class="form-email">メールアドレス<span></span></p>
                <input type="text" name="email" value="">
                <div class="area-msg"></div>
                <p class="form-password">パスワード</p>
                <input type="password" name="pass" value=""><br>

                <label>
                    <input type="checkbox" name="pass_save">
                    ログイン状態を記録する<br>
                </label>

                <div class="btn-contner">
                    <input type="submit" name="submit" class="btn btn-mid" value="ログイン"><br>
                </div>

                <a href="passwordReminder.php">パスワードをお忘れの方</a>
            </form>
        </div>

        <!--  -->
    </section>

    <!-- フッター -->
    <?php
    require('footer.php');
    ?> 