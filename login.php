<?php
 // 共通関数を読み込み
require('function.php');

debug('===========================');
debug('ログイン');
debug('===========================');
debugLogStart();

// ログイン認証
require('auth.php');

// =================
// ログイン画面処理
// =================
// post送信されていた場合
if (!empty($_POST)) {
    debug('POST送信があります。');

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
            debug('バリデーションOKです。');

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

                debug('クエリ結果の中身：' . print_r($result, true));
                // パスワード称号
                if (!empty($result) && password_verify($pass, array_shift($result))) {
                    debug('パスワードがマッチしました。');

                    // ログイン情報の有効期限(デフォルトを1時間とする)
                    $sesLimit = 60 * 60;
                    // 最終ログイン日時を現在日時に
                    $_SESSION['login_limit'] = time();
                    // ログイン保持にチェックがある場合
                    if ($pass_save) {
                        debug('ログイン保持にチェック');
                        // ログイン有効期限を30日にしてセット
                        $_SESSION['login_limit'] = $sesLimit * 24 * 30;
                    } else {
                        debug('ログイン保持にチェックはありません。');
                        // 次回からログイン保持したいので、ログイン有効期限を1時間にセット
                        $_SESSION['login_limit'] = $sesLimit;
                    }
                    // ユーザーIDを格納
                    $_SESSION['user_id'] = $result['id'];

                    debug('セッション変数の中身：' . print_r($_SESSION, true));
                    debug('マイページへ遷移します');
                    header("Location:mypage.php");
                } else {
                    debug('パスワードがアンマッチです。');
                    $err_msg['common'] = MSG09;
                }
            } catch (Exception $e) {
                error_log('エラー発生：' . $e->getMessage());
                $err_msg['common'] = MSG07;
            }
        }
    }
}
debug('画面表示終了===================');
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
                    <input type="text" name="email" value="<?php if (!empty($_POST['email'])) echo $_POST['email']; ?>">
                    <div class="area-msg">
                        <?php
                        if (!empty($err_msg['email'])) echo $err_msg['email'];
                        ?>
                    </div>
                </label>
                <label class="<?php if (!empty($err_msg['pass'])) echo 'err'; ?>">
                    <p class="form-password">パスワード</p>
                    <input type="password" name="pass" value=""><br>
                    <div class="area-msg">
                        <?php
                        if (!empty($err_msg['pass'])) echo $err_msg['pass'];
                        ?>
                    </div>
                </label>
                <label>
                    <input type="checkbox" name="pass_save">
                    次回から自動的にログインする<br>
                </label>

                <div class="btn-contner">
                    <input type="submit" name="submit" class="btn btn-mid" value="ログイン"><br>
                </div>

                <a href="passRemindSend.php">パスワードをお忘れの方</a>
            </form>
        </div>

    </section>

    <!-- フッター -->
    <?php
    require('footer.php');
    ?> 