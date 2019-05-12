<?php
 // 共通関数の読み込み
require('function.php');

// debug('==================');
// debug('プロフィール編集');
// debug('==================');
// debugLogStart();

// ログイン認証
require('auth.php');

// =================
// 画面処理
// =================
// DBからユーザー情報を取得
$dbFormData = getUser($_SESSION['user_id']);

// post送信された場合
if (!empty($_POST)) {

    // 変数にユーザー情報を代入
    $username = $_POST['username'];
    $email = $_POST['email'];
    // 画像をアップロードし、パスを格納
    $pic = (!empty($_FILES['pic']['name'])) ? uploadImg($_FILES['pic'], 'pic') : '';
    $pic = (empty($pic) && !empty($dbFormData['pic'])) ? $dbFormData['pic'] : $pic;

    // 未入力チェック
    validInput($username, 'username');
    validInput($email, 'email');

    if (empty($err_msg)) {
        // debug('未入力チェックOK');

        if ($dbFormData['username'] !== $username) {
            // 名前の最大文字数チェック
            validMaxLen($username, 'username', 10);
        }

        if ($dbFormData['email']) {
            // Emailの最大文字数チェック
            validMaxLen($email, 'email');
            // Emailの形式チェック
            validEmail($email, 'email');
        }


        if (empty($err_msg)) {

            // 例外処理
            try {
                // DBへの接続
                $dbh = dbConnect();
                // SQL文作成
                $sql = 'UPDATE user SET username = :u_name, email = :email, pic = :pic WHERE id = :u_id';
                $data = array(':u_name' => $username, ':email' => $email, ':pic' => $pic, ':u_id' => $_SESSION['user_id']);
                // クエリ実行
                $stmt = queryPost($dbh, $sql, $data);

                // クエリ成功
                if ($stmt) {
                    $_SESSION['msg_success'] = SUS02;
                    header("Location:mypage.php");
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
$siteTitle = 'プロフィール編集';
require('head.php');
?>

<body class="prof-edit">
    <!-- ヘッダー -->
    <?php
    require('header.php');
    ?>

    <!-- メインコンテンツ -->
    <section id="contents" class="site-width">
        <h1 class="title">プロフィール編集</h1>
        <!-- main -->
        <section class="main">
            <div class="form-container">
                <form action="" method="post" class="form form-profedit" enctype="multipart/form-data">
                    <div class="aria-msg">
                        <?php
                        if (!empty($err_msg['common'])) echo $err_msg['common'];
                        ?>

                    </div>
                    <label class="<?php if (!empty($err_msg['username'])) echo 'err'; ?>">
                        お名前<span class="label-require">必須</span>&nbsp;&nbsp;<span>※10字以内で入力ください。</span>
                        <input type="text" name="username" class="prof-input" value="<?php echo getFormData('username'); ?>">
                        <div class="area-msg">
                            <?php
                            if (!empty($err_msg['username'])) echo $err_msg['username'];
                            ?>
                        </div>
                    </label>
                    <label class="<?php if (!empty($err_msg['email'])) echo 'err'; ?>">
                        メールアドレス<span class="label-require">必須</span>
                        <input type="text" name="email" value="<?php echo getFormData('email'); ?>">
                    </label>
                    <div class="area-msg">
                        <?php
                        if (!empty($err_msg['email'])) {
                            echo $err_msg['email'];
                        }
                        ?>
                    </div>
                    プロフィール画像
                    <label class="area-drop <?php if (!empty($err_msg['pic'])) echo 'err'; ?>" style="min-height:370px; border:none;">
                        <input type="hidden" name="MAX_FILE_SIZE" value="3145728">
                        <input type="file" name="pic" class="input-file" style="min-height:370px;">
                        <span class="drug-drop">ドラッグ＆ドロップ</span>
                        <img src="<?php echo getFormData('pic'); ?>" alt="" class="prev-img" style="<?php if (empty(getFormData('pic'))) echo 'display:none;' ?>">

                    </label>
                    <div class="area-msg">
                        <?php
                        if (!empty($err_msg['pic'])) echo $err_msg['pic'];
                        ?>
                    </div>
                    <div class="btn-container">
                        <input type="submit" name="submit" class="btn btn-mid" value="変更する">
                    </div>
                    </label>
                </form>
            </div>
        </section>
    </section>

    <div class="footer_dummy"></div>


    <!-- フッター -->
    <?php
    require('footer.php');
    ?> 