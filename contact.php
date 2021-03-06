<?php

// 共通関数
require('function.php');

// debug('==============');
// debug('お問い合わせページ');
// debug('==============');
// debugLogStart();

// セッションIDがある場合、ログイン認証を実施
if (!empty($_SESSION['user_id'])) {
    require('auth.php');
} else {
    session_unset();
}

// セッションIDの有無で処理を分ける
if (empty($_SESSION['user_id'])) {

    // post送信されていた場合
    if (!empty($_POST)) {

        // 変数にPOST情報を代入
        $email = $_POST['email'];
        $user_comment = $_POST['comment'];

        // 未入力チェック
        validInput($email, 'email');
        validInput($user_comment, 'comment');

        if (empty($err_msg)) {

            // email形式チェック
            validEmail($email, 'email');
            // 最大文字数チェック
            validMaxLen($email, 'email');
            validMaxLen($user_comment, 'comment', 200);


            if (empty($err_msg)) {

                // 例外処理
                try {

                    // メール送信
                    $from = $email;
                    $to = 'info@chiritsumo.nyankormotti.com';
                    $subject = '【お問い合わせ】| 未ログインユーザーより';

                    $comment = <<<EOT
{$user_comment}
EOT;
                    sendMail($from, $to, $subject, $comment);

                    $_SESSION['msg_success'] = SUS03;

                    header("Location:index.php");
                } catch (Exception $e) {
                    error_log('エラー発生：' . $e->getMessage());
                    $err_mg['common'] = MSG07;
                }
            }
        }
    }
} else {
    // post送信されていた場合
    if (!empty($_POST)) {

        // post情報を変数に格納
        $user_id = $_SESSION['user_id'];
        $user_comment = $_POST['comment'];

        // 未入力チェック
        validInput($user_comment, 'comment');

        if (empty($err_msg)) {

            // 最大文字数チェック
            validMaxLen($user_comment, 'comment', 200);

            if (empty($err_msg)) {

                // 例外処理
                try {
                    // DB接続
                    $dbh = dbConnect();
                    // SQL文作成
                    $sql = 'SELECT email FROM user WHERE id = :id AND delete_flg = 0';
                    $data = array(':id' => $user_id);
                    // クエリ実行
                    $stmt = queryPost($dbh, $sql, $data);

                    // クエリ実行結果の値を取得
                    $result = $stmt->fetch(PDO::FETCH_ASSOC);

                    // EmailがDBに登録されていない場合
                    if ($stmt && $stmt->rowCount() > 0) {
                        // メール送信
                        $from = $result['email'];
                        $to = 'info@chiritsumo.nyankormotti.com';
                        $subject = '【お問い合わせ】| ログインユーザーより';
                        $comment = <<<EOT
{$user_comment}
EOT;
                        sendMail($from, $to, $subject, $comment);

                        $_SESSION['msg_success'] = SUS03;

                        header("Location:mypage.php");
                    } else {
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
?>

<?php
$siteTitle = 'お問い合わせ';
require('head.php');
?>

<body>

    <!--ヘッダー -->
    <?php
    require('header.php');
    ?>



    <!-- メインコンテンツ -->
    <section class="main">
        <h1 class="title">お問い合わせ</h1>

        <div class="form">
            <form action="" method="post" class="form contact-form1">

                <div class="contact-form2">
                    <div class="area-msg"></div>

                    <?php
                    if (empty($_SESSION['user_id'])) {
                        ?>
                        <label class="<?php if (!empty($err_msg['email'])) echo 'err'; ?>">
                            <p class="form-email">メールアドレス<span class="area-msg">&nbsp;&nbsp;&nbsp;&nbsp;<?php if (!empty($err_msg['email'])) echo $err_msg['email']; ?></span></p>
                            <input type="text" name="email" value="<?php if (!empty($_POST['email'])) echo $_POST['email']; ?>">
                        </label>
                    <?php

                }
                ?>

                    <label class="form-group">
                        <p class="comment">内容<?php if (empty($err_msg['comment'])) { ?>
                                <span>&nbsp;&nbsp;&nbsp;&nbsp;<span class="help-block">※200文字以内にてご入力ください</span></span>
                            <?php

                        } else {
                            ?>
                                <span>
                                    &nbsp;&nbsp;&nbsp;&nbsp;
                                    <span class="area-msg">
                                        <?php
                                        echo  $err_msg['comment'];
                                        ?>
                                    </span>
                                </span>
                            <?php

                        }
                        ?>
                        </p>
                        <textarea class=" <?php if (!empty($err_msg['comment'])) echo 'err'; ?>" name="comment" id="count-contact-text" cols="63" rows="8" value="<?php if (!empty($_POST['comment'])) echo $_POST['comment']; ?>"></textarea>
                        <div class="comment-su"><span class="comment-count">0</span><span>/ 200</span></div>

                    </label>


                    <div class="btn-contner contact-btn">
                        <input type="submit" name="submit" class="btn btn-mid" value="送信">
                    </div>
                </div>

            </form>
        </div>
    </section>

    <div class="footer_dummy"></div>


    <!-- フッター -->
    <?php
    require('footer.php');
    ?>