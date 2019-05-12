<?php
// 共通関数を読み込み
require('function.php');

// debug('===========================');
// debug('ログイン');
// debug('===========================');
// debugLogStart();

// ログイン認証
require('auth.php');

// =================
// ログイン画面処理
// =================
// post送信されていた場合
if (!empty($_POST)) {

    // 変数にユーザー情報を代入
    $email = $_POST['email'];
    $pass = $_POST['pass'];
    $pass_save = (!empty($_POST['pass_save'])) ? true : false;

    // 未入力チェック
    validInput($email, 'email');
    validInput($pass, 'pass');

    if (empty($err_msg)) {
        // emailの形式チェック
        validEmail($email, 'email');
        // emailの最大文字数チェック
        validMaxLen($email, 'email');
        // passwordの半角英数字チェック
        validHalf($pass, 'pass');
        // passwordの最大文字数チェック
        validMaxLen($pass, 'pass');
        // passwordの最小文字数チェック
        validMinLen($pass, 'pass');

        if (empty($err_msg)) {

            // 例外処理
            try {
                // DBへの接続
                $dbh = dbConnect();
                // SQL文作成
                $sql = 'SELECT password, id FROM user WHERE email = :email AND delete_flg = 0';
                $data = array(
                    ':email' => $email
                );
                // クエリの実行
                $stmt = queryPost($dbh, $sql, $data);
                // クエリ結果の値の取得
                $result = $stmt->fetch(PDO::FETCH_ASSOC);

                // パスワード称号
                if (!empty($result) && password_verify($pass, array_shift($result))) {

                    // ログイン情報の有効期限(デフォルトを1時間とする)
                    $sesLimit = 60 * 60;
                    // 最終ログイン日時を現在日時に
                    $_SESSION['login_date'] = time();
                    // ログイン保持にチェックがある場合
                    if ($pass_save) {
                        // ログイン有効期限を30日にしてセット
                        $_SESSION['login_limit'] = $sesLimit * 24 * 30;
                    } else {
                        // 次回からログイン保持したいので、ログイン有効期限を1時間にセット
                        $_SESSION['login_limit'] = $sesLimit;
                    }
                    // ユーザーIDを格納
                    $_SESSION['user_id'] = $result['id'];

                    // マイページへ遷移する
                    header("Location:mypage.php");
                } else {
                    $err_msg['common'] = MSG09;
                }
            } catch (Exception $e) {
                error_log('エラー発生：' . $e->getMessage());
                $err_msg['common'] = MSG07;
            }
        }
    }
}
?>

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
            <form action="" method="post" class="form">
                <div class="area-msg">
                    <?php
                    if (!empty($err_msg['common'])) echo $err_msg['common'];
                    ?>
                </div>
                <label class="<?php if (!empty($err_msg['email'])) echo 'err'; ?>">
                    <p class="form-email">メールアドレス<span></span></p>
                    <input type="text" name="email" value="<?php if (!empty($_POST['email'])) echo $_POST['email']; ?>" style="margin-bottom:0;">
                    <div class=" area-msg">
                    <?php
                    if (!empty($err_msg['email'])) echo $err_msg['email'];
                    ?>
        </div>
        </label>
        <label class="<?php if (!empty($err_msg['pass'])) echo 'err'; ?>">
            <p class="form-password">パスワード</p>
            <input type="password" name="pass" value="" style="margin-bottom:0;">
            <div class="area-msg">
                <?php
                if (!empty($err_msg['pass'])) echo $err_msg['pass'];
                ?>
            </div>
        </label>
        <label class="pass_save">
            <input type="checkbox" name="pass_save">
            次回から自動的にログインする<br>
        </label>

        <div class="login-btn-contner">
            <input type="submit" name="submit" class="btn-mid login-btn" value="ログイン"><br>
        </div>

        <p class="login-pass">パスワードをお忘れの方は<a class="forget_pass" href="passRemindSend.php">コチラ</a></p>
        </form>
        </div>

    </section>

    <div class="footer_dummy"></div>

    <!-- フッター -->
    <?php
    require('footer.php');
    ?>