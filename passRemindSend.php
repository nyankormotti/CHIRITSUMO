<?php

// 共通関数の読み込み
require('function.php');

// debug('=================');
// debug('パスワード再発行メール送信ページ');
// debug('=================');
// debugLogStart();

// ログイン認証なし

// post送信されていること
if (!empty($_POST)) {

    // 変数にPOST情報を代入
    $email = $_POST['email'];

    // 未入力チェック
    validInput($email, 'email');

    if (empty($err_msg)) {

        // emailの形式チェック
        validEmail($email, 'email');
        // emailの最大文字数チェック
        validMaxLen($email, 'email');

        if (empty($err_msg)) {

            // 例外処理
            try {
                // DB接続
                $dbh = dbConnect();
                // SQL文作成
                $sql = 'SELECT COUNT(*) FROM user WHERE email = :email AND delete_flg = 0';
                $data = array(':email' => $email);
                // クエリ実行
                $stmt = queryPost($dbh, $sql, $data);
                // クエリ実行結果の値を取得
                $result = $stmt->fetch(PDO::FETCH_ASSOC);

                // EmailがDBに登録されている場合
                if ($stmt && array_shift($result)) {
                    $_SESSION['msg_success'] = SUS03;

                    // 認証キー
                    $auth_key = makeRandkey();

                    // メール送信
                    $from = 'info@chiritsumo.nyankormotti.com';
                    $to = $email;
                    $subject = '【パスワード再発行認証】| CHIRITSUMO';
                    $comment = <<<EOT
本メールアドレス宛にパスワード再発行のご依頼がありました。
下記URLにて認証キーをご入力いただくとパスワードが再発行されます。

パスワード再発行認証キー入力ページ：http://chiritsumo.nyankormotti.com/passRemindRecieve.php
認証キー：{$auth_key}
*認証キーの有効期限は30分となります

認証キーを再発行されたい場合は下記ページより再度再発行をお願いします。
http://chiritsumo.nyankormotti.com/passRemindSend.php

///////////////////////////////////////////////
chiritumoカスタマーセンター
URL  http://chiritsumo.nyankormotti.com
E-mail info@chiritsumo.nyankormotti.com
///////////////////////////////////////////////

EOT;
                    sendMail($from, $to, $subject, $comment);

                    // 認証に必要な情報をセッションへ保存
                    $_SESSION['auth_key'] = $auth_key;
                    $_SESSION['auth_email'] = $email;
                    $_SESSION['auth_key_limit'] = time() + (60 * 30);

                    header("Location:passRemindRecieve.php");
                } else {
                    $err_msg['common'] = MSG07;
                }
            } catch (Exception $e) {
                error_log('エラー発生' . $e->getMessage());
                $err_msg['common'] = MSG07;
            }
        }
    }
}
?>

<?php
$siteTitle = 'パスワード再発行送信ページ';
require('head.php');
?>

<body>

    <!-- ヘッダー -->
    <?php
    require('header.php');
    ?>

    <!-- メインコンテンツ -->
    <section class="main">
        <h1 class="title">パスワード再発行</h1>

        <div class="form">
            <form action="" method="post" class="form contact-form">

                <div class="area-msg">
                    <?php
                    if (!empty($err_msg['common'])) echo $err_msg['common'];
                    ?>
                </div>
                <p class="pass-remind">ご指定いただいたメールアドレス宛に、<br>
                    パスワード再発行用の認証キーを送付します。</p>


                <label class="<?php if (!empty($err_msg['email'])) echo 'err'; ?>">
                    <p class="form-email">メールアドレス</p>
                    <input type="text" name="email" value="<?php echo getFormData('email'); ?>" style="margin-bottom:0;">
                    <div class="area-msg">
                        <?php
                        if (!empty($err_msg['email'])) echo $err_msg['email'];
                        ?>
                    </div>
                </label>

                <div class="btn-contner">
                    <input type="submit" name="submit" class="btn btn-mid" value="送信"><br>
                </div>
                <a href="index.php">&gt;&gt;トップページへ戻る</a>
            </form>
        </div>


    </section>

    <!-- フッター -->
    <?php
    require('footer.php');
    ?>