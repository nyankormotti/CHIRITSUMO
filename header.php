<header>
    <div class="header_div">
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
            <ul class="menu-ul">
                <?php
                if (empty($_SESSION['user_id'])) {
                    ?>
                    <li><a href="login.php">ログイン</a></li>
                    <li><a href="regist_member.php">会員登録</a></li>

                <?php

            } else {
                ?>

                    <li><a href="logout.php">ログアウト</a></li>
                    <li><a href="mypage.php">マイページ</a></li>
                    <li class="menu__single">
                        <a href="#" class="init-bottom">メニュー</a>
                        <ul class="menu__second-level">
                            <li><a href="actualEdit.php">実績を記載</a></li>
                            <li><a href="categoryEdit.php">カテゴリー編集</a></li>
                            <li><a href="profEdit.php">プロフィール編集</a></li>
                            <li><a href="chnangePassword.php">パスワード変更</a></li>
                            <li><a href="withDraw.php">退会</a></li>
                        </ul>
                    </li>

                <?php

            }
            ?>
                <li><a href="contact.php">お問い合わせ</a></li>
            </ul>
        </div>
    </div>
</header>
<div id="header-dummy"></div>