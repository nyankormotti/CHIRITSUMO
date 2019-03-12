<header>
    <h1 id="header-title">
        <?php
        if (empty($_SESSION['user_id'])) {
            ?>
        <a href="index.php">継続支援アプリ CHIRITSUMO!!</a>
        <?php
    } else {
        ?>
        <a href="mypage.php">継続支援アプリ CHIRITSUMO!!</a>
        <?php
    }
        ?>

    </h1>
    <div id="top-nav">
        <ul>
            <?php
            if (empty($_SESSION['user_id'])) {
                ?>
            <li><a href="login.php">ログイン</a></li>
            <li><a href="regist_member.php">会員登録</a></li>

            <?php

        } else {
            ?>
            <li><a href="mypage.php">マイページ</a></li>
            <li><a href="logout.php">ログアウト</a></li>

            <?php

        }
        ?>
            <li><a href="contact.php">お問い合わせ</a></li>
        </ul>
    </div>
</header>
<div id="header-dummy"></div> 