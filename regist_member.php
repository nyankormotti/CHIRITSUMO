<?php
// 共通関数の呼び出し
require('function.php');

// debug('===========================');
// debug('会員登録');
// debug('===========================');
// debugLogStart();

// post送信がある場合
if (!empty($_POST)) {
    // ユーザー情報を変数に格納
    $username = $_POST['name'];
    $email = $_POST['email'];
    $pass = $_POST['pass'];
    $pass_re = $_POST['pass_re'];

    // 未入力チェック
    validInput($username, 'name');
    validInput($email, 'email');
    validInput($pass, 'pass');
    validInput($pass_re, 'pass_re');

    if (empty($err_msg)) {
        // バリデーションチェック

        // 名前のバリデーション
        // 名前の最大文字数チェック
        validMaxLen($username, 'name', 10);
        // Emailのバリデーション
        // Emailの形式チェック
        validEmail($email, 'email');
        // Emailの最大文字数チェック
        validMaxLen($email, 'email');
        // Emailの重複チェック
        validEmailDup($email);

        // passwordのバリデーション
        // passwordとpassword(再入力)の同一チェック
        validMatch($pass, $pass_re, 'pass_re');

        if (empty($err_msg)) {
            // passwordのバリデーション
            // paswordの半角英数字チェック
            validHalf($pass, 'pass');
            // passwordの最小文字数チェック
            validMaxLen($pass, 'pass');
            // passwordの最大文字数チェック
            validMinLen($pass, 'pass');
            if (empty($err_msg)) {
                // 例外処理
                try {
                    // DBへ接続
                    $dbh = dbConnect();
                    // SQL文発行
                    $sql = 'INSERT INTO user (username,email,password,login_time,create_date) VALUES (:username,:email,:pass,:login_time,:create_date)';
                    $data = array(
                        ':username' => $username,
                        ':email' => $email,
                        ':pass' => password_hash($pass, PASSWORD_DEFAULT),
                        ':login_time' => date('Y-m-d H:i:s'),
                        ':create_date' => date('Y-m-d H:i:s')
                    );
                    // クエリ実行
                    $stmt = queryPost($dbh, $sql, $data);

                    // クエリ成功の場合
                    if ($stmt) {
                        // ログイン有効期限(デフォルトを1時間とする)
                        $sesLimit = 60 * 60;
                        // 最終ログイン日時を現在日時に
                        $_SESSION['login_date'] = time();
                        $_SESSION['login_limit'] = $sesLimit;
                        // ユーザーIDを格納
                        // 直近にINSERTされたIDをセッションに格納
                        $_SESSION['user_id'] = $dbh->lastInsertId();

                        // マイページへ繊維
                        header("Location:mypage.php"); 
                    }
                } catch (Exceotion  $e) {
                    error_log('エラー 発 生：' . $e->getMessage());
                    $err_msg['common'] = MSG07;
                }
            }
        }
    }
}
?>

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
                <div class="area-msg">
                    <?php
                    if (!empty($err_msg['common'])) {
                        echo $err_msg['common'];
                    }
                    ?>
                </div>
                <label class="<?php if ((!empty($err_msg['name']))) echo 'err'; ?>">
                    <p class="form-name">お名前<span>&nbsp;&nbsp;&nbsp;&nbsp;※10文字以内にてご入力ください</span></p>
                    <input type="text" name="name" value="<?php if (!empty($_POST['name'])) echo $_POST['name']; ?>" style="margin-bottom:0;">
                    <div class="area-msg">
                        <?php
                        if (!empty($err_msg['name'])) {
                            echo $err_msg['name'];
                        }
                        ?>
                    </div>
                </label>

                <label class="<?php if (!empty($err_msg['email'])) echo 'err'; ?>">
                    <p class="form-email">メールアドレス<span></span></p>
                    <input type="text" name="email" value="<?php if (!empty($_POST['email'])) echo $_POST['email']; ?>" style="margin-bottom:0;">
                    <div class="area-msg">
                        <?php
                        if (!empty($err_msg['email'])) {
                            echo $err_msg['email'];
                        }
                        ?>
                    </div>
                </label>

                <label class="<?php if (!empty($err_msg['pass'])) echo 'err'; ?>">
                    <p class="form-password">パスワード<span>&nbsp;&nbsp;&nbsp;&nbsp;※半角英数字6字以上にてご入力ください</span></p>
                    <input type="password" name="pass" style="margin-bottom:0;">
                    <div class="area-msg">
                        <?php
                        if (!empty($err_msg['pass'])) {
                            echo $err_msg['pass'];
                        }
                        ?>
                    </div>
                </label>

                <label class="<?php if (!empty($err_msg['pass_re'])) echo 'err'; ?>">
                    <p class="form-password">パスワード(再入力)<span></span></p>
                    <input type="password" name="pass_re" style="margin-bottom:0;">
                    <div class="area-msg">
                        <?php
                        if (!empty($err_msg['pass_re'])) {
                            echo $err_msg['pass_re'];
                        }
                        ?>
                    </div>
                </label>


                <div class="btn-contner reg-men-btn">
                    <input type="submit" name="submit" class="btn btn-mid" value="登録"><br>
                </div>
            </form>
        </div>
    </section>

    <div class="footer_dummy"></div>

    <!-- フッター  -->
    <?php
    require('footer.php');
    ?>