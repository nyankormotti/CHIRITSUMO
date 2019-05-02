<?php
// 共通関数の読み込み
require('function.php');

// debug('====================');
// debug('パスワード再発行認証キー入力ページ');
// debug('====================');
// debugLogStart();

// SESSIONに認証キーがあるか確認、無ければリダイレクト
if (empty($_SESSION['auth_key'])) {
    // debug('認証キーがありません。');
    header("Location:passRemindSend.php"); //認証キー送信ページ
}

// post送信があるか
if (!empty($_POST)) {

    // 変数に認証キーを代入
    $auth_key = $_POST['token'];

    // 未入力チェック
    validInput($auth_key, 'token');

    if (empty($err_msg)) {
        // debug('未処理分OK。');

        // 固定長チェック
        validLength($auth_key, 'token');
        // 半角チェック
        validHalf($auth_key, 'token');

        if (empty($err_msg)) {
            // debug('バリデーションOK');

            // postとsessionの認証キーの同値チェック
            if ($auth_key !== $_SESSION['auth_key']) {
                $err_msg['common'] = MSG15;
            }

            // 認証キーの有効期限判定
            if (time() > $_SESSION['auth_key_limit']) {
                $err_msg['common'] = MSG16;
            }

            if (empty($err_msg)) {
                // debug('認証OK。');
                // パスワード生成
                $pass = makeRandKey();

                // debug('パスワード：' . $pass);

                // 例外処理
                try {
                    // DBへ接続
                    $dbh = dbConnect();
                    // SQL文発行
                    $sql = 'UPDATE user SET password = :pass WHERE email = :email AND delete_flg = 0';
                    $data = array(':email' => $_SESSION['auth_email'], ':pass' => password_hash($pass, PASSWORD_DEFAULT));
                    // クエリ実行
                    $stmt = queryPost($dbh, $sql, $data);
                    // クエリ成功
                    if ($stmt) {
                        // debug('クエリ成功');

                        // メール送信
                        $from = 'info@chiritsumo.nyankormotti.com';
                        $to = $_SESSION['auth_email'];
                        $subject = '【パスワード再発行完了】 | CHIRITSUMO';
                        $comment = <<<EOT
本メールアドレス宛にパスワードの再発行をいたしました。
下記のURLにて再発行パスワードをご入力頂き、ログインください。

ログインページ：http://chiritsumo.nyankormotti.com/login.php
再発行パスワード：{$pass}
*ログイン後、パスワードのご変更をお願いいたします。

///////////////////////////////////////////////
chiritumoカスタマーセンター
URL  http://chiritsumo.nyankormotti.com
E-mail info@chiritsumo.nyankormotti.com
///////////////////////////////////////////////

EOT;
                        sendMail($from, $to, $subject, $comment);
                        // セッション削除
                        session_unset();
                        $_SESSION['msg_success'] = SUS03;
                        // debug('セッションの中身：' . print_r($_SESSION, true));

                        header("Location:login.php");
                    } else {
                        // debug('クエリに失敗しました。');
                        $err_msg['common'] = MSG07;
                    }
                } catch (Exception $e) {
                    error_log('エラー発生：' . $e->getMessage());
                    $err_msg['common'] = MSG07;
                }
            }
        }
    }
}


// ========================
// 画像処理
// ========================

?>

<?php
$siteTitle = 'パスワード再発行受信ページ';
require('head.php');
?>

<body>

    <!-- ヘッダー -->
    <?php
    require('header.php');
    ?>
    <p id="js-show-msg" style="display:none;" class="msg-slide">
        <?php echo getSessionFlash('msg_success'); ?>
    </p>

    <!-- メインコンテンツ -->
    <section class="main">
        <h1 class="title">パスワード再発行</h1>

        <div class="form">
            <form action="" method="post" class="form contact-form">

                <div class="area-msg">
                    <?php
                    if (!empty($err_msg['common'])) {
                        echo $err_msg['common'];
                    }
                    ?>

                </div>
                <p class="pass-remind">受信されたメールに記載のある<br>
                    認証キーをご記載ください。<br>
                    メールにて再発行したパスワードをお伝えします。</p>


                <label class="<?php if (!empty($err_msg['token'])) echo 'err'; ?>">
                    <p class="form-auth_key">認証キー</p>
                    <input type="text" name="token" value="<?php echo getFormData('token'); ?>" style="margin-bottom:0;">
                    <div class="area-msg">
                        <?php
                        if (!empty($err_msg['token'])) echo $err_msg['token'];
                        ?>
                    </div>
                </label>

                <div class="btn-contner">
                    <input type="submit" name="submit" class="btn btn-mid" value="送信"><br>
                </div>
                <a href="passRemindSend.php">&gt;&gt;パスワード再発行メールを再度送付する</a>
            </form>
        </div>

    </section>

    <!-- フッター -->
    <?php
    require('footer.php');
    ?>