<?php
// 共通関数の読み込み
require('function.php');

// debug('=================');
// debug('パスワード変更ページ');
// debug('=================');
// debugLogStart();

// ログイン認証
require('auth.php');

// ==================
// 画面処理
// ==================

// DBからユーザーデータを取得
$userData = getUser($_SESSION['user_id']);

// post送信されていた場合
if (!empty($_POST)) {

    // 変数にユーザー情報を代入
    $pass_old = $_POST['old_pass'];
    $pass_new = $_POST['new_pass'];
    $pass_new_re = $_POST['new_pass_re'];

    // 未入力チェック
    validInput($pass_old, 'old_pass');
    validInput($pass_new, 'new_pass');
    validInput($pass_new_re, 'new_pass_re');

    if (empty($err_msg)) {

        // 古いパスワードチェック
        validPass($pass_old, 'old_pass');
        // 新しいパスワ ードチェック
        validPass($pass_new, 'new_pass');
        // 古いパスワードとDBのパスワードを称号
        if (!password_verify($pass_old, $userData['password'])) {
            $err_msg['old_pass'] = MSG12;
        }
        // 新しいパスワードと古いパスワードが同じか チェック
        if ($pass_old === $pass_new) {
            $err_msg['new_pass'] = MSG13;
        }
        //  パスワードとパスワード再入力が合って いるかチェック
        validMatch($pass_new, $pass_new_re, 'new_pass_re');

        if (empty($err_msg)) {

            // 例外処理
            try {
                // DBへの接続
                $dbh = dbConnect();
                // SQL文の作成
                $sql = 'UPDATE user SET password = :pass WHERE id = :u_id';
                $data = array(':pass' => password_hash($pass_new, PASSWORD_DEFAULT), ':u_id' => $_SESSION['user_id']);
                // クエリ実行
                $stmt = queryPost($dbh, $sql, $data);
                // クエリの実行が成功した場合
                if ($stmt) {
                    $_SESSION['msg_success'] = SUS01;

                    // メールを送信
                    $username = $userData['username'];
                    $from = 'info@chiritsumo.nyankormotti.com';
                    $to = $userData['email'];
                    $subject = '【パスワード変更通知】 | CHIRITSUMO';

                    $comment = <<<EOT
{$username} さん
パスワードが変更されました。

///////////////////////////////////////////////
chiritumoカスタマーセンター
URL  http://chiritsumo.nyankormotti.com
E-mail info@chiritsumo.nyankormotti.com
///////////////////////////////////////////////
EOT;
                    sendMail($from, $to, $subject, $comment);

                    header("Location:mypage.php");
                }
            } catch (Exception $e) {
                error_log('エラー発生:' . $e->getMessage());
                $err_msg['common'] = MSG07;
            }
        }
    }
}

?>

<?php
$siteTitle = 'パスワード変更';
require('head.php');
?>

<body>
    <!-- ヘッダー -->
    <?php
    require('header.php');
    ?>

    <!-- メインコンテンツ -->
    <section class="main">
        <h1 class="title">パスワード変更</h1>

        <div class="form">
            <form action="" method="post" class="form">
                <div class="area-msg">
                    <?php
                    if (!empty($err_msg['common'])) echo $err_msg['common'];
                    ?>
                </div>
                <label class="<?php if (!empty($err_msg['old_pass'])) echo 'err'; ?>">
                    <p class="form-password">現在のパスワード</p>
                    <input type="password" name="old_pass" value="<?php echo getFormData('old_pass') ?>">
                    <div class="area-msg">
                        <?php
                        if (!empty($err_msg['old_pass'])) {
                            echo $err_msg['old_pass'];
                        }
                        ?>
                    </div>
                </label>

                <label class="<?php if (!empty($err_msg['new_pass'])) echo 'err'; ?>">
                    <p class="form-password">新しいパスワード<span class="pass-valid">&nbsp;&nbsp;&nbsp;&nbsp;※半角英数字6字以上にてご入力ください</span></p>
                    <input type="password" name="new_pass" value="<?php echo getFormData('new_pass') ?>">
                    <div class="area-msg">
                        <?php
                        if (!empty($err_msg['new_pass'])) {
                            echo $err_msg['new_pass'];
                        }
                        ?>
                    </div>
                </label>

                <label class="<?php if (!empty($err_msg['new_pass_re'])) echo 'err'; ?>">
                    <p class="form-password">新しいパスワード（再入力）</p>
                    <input type="password" name="new_pass_re" value="<?php echo getFormData('new_pass_re') ?>">
                    <div class="area-msg">
                        <?php
                        if (!empty($err_msg['new_pass_re'])) {
                            echo $err_msg['new_pass_re'];
                        }
                        ?>
                    </div>
                </label>

                <div class="btn-contner">
                    <input type="submit" name="submit" class="btn btn-mid" value="変更"> <br>
                </div>
            </form>
        </div>
    </section>



    <!-- フッター -->
    <?php
    require('footer.php');
    ?>