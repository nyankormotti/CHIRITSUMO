<?php
// 共通関数の読み込み
require('function.php');

// debug('=================');
// debug('カテゴリー編集ページ');
// debug('=================');
// debugLogStart();

// ログイン認証
require('auth.php');

// =================
// 画面処理
// =================
$categoryData = getCategory($_SESSION['user_id']);

// カテゴリー編集フラグ
$c_edit_flg = 0;

// セッションのユーザーIDを変数に代入
$user_id = $_SESSION['user_id'];

// 登録ボタンが押された場合
if (!empty($_POST['sub_reg'])) {

    // 変数にユーザー情報を代入
    $c_name = $_POST['c_name'];

    // 未入力チェック
    validInput($c_name,  'c_name');

    if (empty($err_msg['c_name'])) {

        // 最大文字数チェック
        validMaxLen($c_name, 'c_name', 13);

        if (empty($err_msg['c_name'])) {

            // 例外処理
            try {
                // DBへ接続
                $dbh = dbConnect();
                // SQL文作成
                $sql = 'INSERT INTO category (category_name,user_id,create_date) VALUES (:c_name, :u_id,:date)';
                $data = array(':c_name' => $c_name, ':u_id' => $user_id, ':date' => date('Y-m-d H:i:s'));

                // クエリ実行
                $stmt = queryPost($dbh, $sql, $data);

                // クエリ成功の場合
                if ($stmt) {
                    $c_edit_flg = 1;
                    $_SESSION['category_success'] = SUS04;
                    header("Location:categoryEdit.php");
                }
            } catch (Exception $e) {
                error_log('エラー発生：' . $e->getMessage());
                $err_msg['common'] = MSG07;
            }
        }
    }
}
// 削除ボタンが押された場合
elseif (!empty($_POST['sub_del'])) {

    // 変数にユーザー情報を代入
    $c_id = $_POST['c_id'];
    $performData = getPerfCate($c_id);

    if ((int)$c_id ===  0) {
        $err_msg['c_id'] = MSG16;
    }

    if (empty($err_msg['c_id']) && $performData) {
        $err_msg['c_id'] = MSG17;
    }

    if (empty($err_msg)) {

        // 例外処理
        try {
            // DB接続
            $dbh = dbConnect();
            // SQL文作成
            $sql = 'UPDATE category SET delete_flg = 1 WHERE id = :c_id';
            $data = array(':c_id' => $c_id);
            // クエリ実行
            $stmt = queryPost($dbh, $sql, $data);
            // クエリ成功の場合
            if ($stmt) {
                $c_edit_flg = 1;
                $_SESSION['category_success'] = SUS05;
                header("Location:categoryEdit.php");
            }
        } catch (Exception $e) {
            error_log('エラー発生：' . $e->getMessage());
            $err_msg['common'] = MSG07;
        }
    }
}

?>

<?php
$siteTitle = 'カテゴリ編集';
require('head.php');
?>

<body class="category-body">
    <!-- ヘッダー -->
    <?php
    require('header.php');
    ?>
    <p id="js-show-msg" style="display:none;" class="msg-slide">
        <?php if ($c_edit_flg !== 0) {
            $c_edit_flg = 0;
        } else {
            echo getSessionFlash('category_success');
        }
        ?>
    </p>

    <!-- メインコンテンツ -->
    <section class="main category-section">
        <h1 class="title">カテゴリー編集</h1>

        <div class="category-content">
            <!-- フォーム -->
            <section class="category-form">
                <!-- 登録フォーム -->
                <form method="post" class="category-regist category-form-part form">
                    <p class="c-title">登録フォーム</p>
                    <?php
                    if (!empty($err_msg['c_name'])) {
                        ?>
                        <div class="area-msg">
                            <?php
                            echo $err_msg['c_name'];
                            ?>
                        </div>
                    <?php

                } else {
                    ?>
                        <p class="c-regist-title-valid">
                            ※13字以内で入力ください
                        </p>
                    <?php

                }
                ?>

                    <input type="text" name="c_name" class="c_regist-input <?php if (!empty($err_msg['c_name'])) echo 'err'; ?>" value="<?php if (!empty($err_msg['c_name'])) echo $_POST['c_name']; ?>">
                    <input type="submit" name="sub_reg" value="登録" class="c_regist-submit">
                </form>

                <!-- 削除フォーム -->
                <form method="post" class="category-delete category-form-part">
                    <p class="c-title">削除フォーム</p>
                    <?php
                    if (!empty($err_msg['c_id'])) {
                        ?>
                        <div class="area-msg">
                            <?php
                            echo $err_msg['c_id'];
                            ?>
                        </div>
                    <?php

                } else {
                    ?>
                        <p class="c-delete-title-valid">
                            カテゴリーを選択してください。
                        </p>
                    <?php

                }
                ?>
                    <div class="selectbox">
                        <span class="icn_select"></span>
                        <select name="c_id" id="" class="<?php if (!empty($err_msg['c_id'])) echo 'err'; ?>">
                            <option value="0" <?php if (getFormdata('c_id', true) == 0) {
                                                    echo 'selected';
                                                } ?>>選択してください</option>
                            <?php
                            foreach ($categoryData as $key => $val) {
                                ?>
                                <option value="<?php echo $val['id'] ?>" <?php if (getFormData('c_id', true) == $val['id']) {
                                                                                echo 'selected';
                                                                            } ?>>
                                    <?php echo $val['category_name']; ?>
                                </option>
                            <?php
                        }
                        ?>
                        </select>
                    </div>
                    <input type="submit" name="sub_del" value="削除" class="c_regist-submit">
                </form>
            </section>

            <!-- 一覧 -->
            <section class="category-list">
                <div class="category-list-area">
                    <p class="c-title">カテゴリー　一覧</p>
                    <div class="category-list-area-form">
                        <?php
                        foreach ($categoryData as $key => $val) {
                            ?>
                            <p class="c_object"><?php echo $val['category_name']; ?></p>
                        <?php
                    }
                    ?>
                    </div>
                </div>

            </section>
        </div>
    </section>

    <div class="footer_dummy"></div>

    <!-- フッター -->
    <?php
    require('footer.php');
    ?>