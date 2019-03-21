<?php
 // 共通関数の読み込み
require('function.php');

debug('=====================');
debug('実績記載ページ');
debug('=====================');
debugLogStart();

// ログイン認証
require('auth.php');

// ===================
// 画面処理
// ===================
// GETデータを格納
$p_id = (!empty($_GET['p_id'])) ? $_GET['p_id'] : '';
// DBから実績データを格納
$dbFormData = (!empty($p_id)) ? getPerformance($_SESSION['user_id'], $p_id) : '';
$dbCategoryData = getCategory($_SESSION['user_id']);
// $dbTotalTime = getTotalTime($_SESSION['user_id']);
// 新規登録画面か編集画面かの判別用フラグ
$edit_flg = (empty($dbFormData)) ? false : true;
// 実績記載が初回であるかを判別するフラグ
// $firstReg_flg = (!$dbTotalTime) ? true : false;
debug('実績ID：' . $p_id);
debug('フォーム用データ：' . print_r($dbFormData, true));
if ($firstReg_flg) {
    debug('初回判定');
} else {
    debug('初回判定ではない');
}

// パラメータ改ざんチェック
// =============================
// GETパラメータはあるが、改ざんされている場合に、正しい実績データが取れないため、マイページへ遷移させる。
if (!empty($p_id) && empty($dbFormData)) {
    debug('GETパラメータの実績IDが違います。マイページへ遷移します。');
    header("Location:mypage.php");
}

// POST送信処理開始
if (!empty($_POST)) {
    debug('POST送信があります。');
    debug('POST情報：' . print_r($_POST, true));
    debug('FILE情報：' . print_r($_FILES, true));

    // 変数にユーザー情報を代入
    $a_title = $_POST['a_title'];
    $a_date = str_replace('/', '-', $_POST['a_date']);
    $a_hour = $_POST['a_hour'];
    if ((int)$a_hour >= 0 && (int)$a_hour < 10) {
        $a_hour = '0' . $a_hour;
    }
    $a_minute = $_POST['a_minute'];
    if ((int)$a_minute >= 0 && (int)$a_minute < 10) {
        $a_minute = '0' . $a_minute;
    }
    $a_time = $a_hour . ':' . $a_minute;

    debug('日時：' . $a_date);
    debug('時間：' . $a_time);
    $c_id = $_POST['c_id'];
    $a_comment = $_POST['comment'];
    // 画像をアップロードし、パスを格納
    $pic1 = (!empty($_FILES['pic1']['name'])) ? uploadImg($_FILES['pic1'], 'pic1') : '';
    $pic1 = (empty($pic1) && !empty($dbFormData['pic1'])) ? $dbFormData['pic1'] : $pic1;
    $pic2 = (!empty($_FILES['pic2']['name'])) ? uploadImg($_FILES['pic2'], 'pic2') : '';
    $pic2 = (empty($pic2) && !empty($dbFormData['pic2'])) ? $dbFormData['pic2'] : $pic2;
    $pic3 = (!empty($_FILES['pic3']['name'])) ? uploadImg($_FILES['pic3'], 'pic3') : '';
    $pic3 = (empty($pic3) && !empty($dbFormData['pic3'])) ? $dbFormData['pic3'] : $pic3;


    // 未入力チェック
    validInput($a_title, 'a_title');
    validInput($a_date, 'a_date');
    validInput($a_time, 'a_time');
    validInput($c_id, 'c_id');
    // 更新の場合はDBの情報と入力情報が異なる場合にバリデーションを行う
    if (empty($dbFormData)) {
        // タイトルの最大文字数チェック
        validMaxLen($a_title, 'a_title', 35);
        // 年月日の形式チェック
        validDate($a_date, 'a_date');
        // カテゴリー未選択チェック
        if ((int)$c_id ===  0) {
            debug('カテゴリー未選択');
            global $err_msg;
            $err_msg['c_id'] = MSG16;
        }
        // コメント最大文字数チェック
        validMaxLen($a_comment, 'comment', 200);
    } else { }


    if (empty($err_msg)) {
        debug('バリデーションOKです。');

        // 例外処理
        try {
            // DBへ接続
            $dbh = dbConnect();
            // SQL文作成
            if ($edit_flg) { } else {
                debug('新規登録です。');
                $sql1 = 'INSERT into performance (title,action_date,action_time,category_id,comment,pic1,pic2,pic3,user_id,create_date) VALUES (:title,:action_date,:action_time,:category_id,:comment,:pic1,:pic2,:pic3,:u_id,:create_date)';
                $data1 = array(':title' => $a_title, ':action_date' => $a_date, ':action_time' => $a_time, ':category_id' => $c_id, ':comment' => $a_comment, ':pic1' => $pic1, ':pic2' => $pic2, ':pic3' => $pic3, ':u_id' => $_SESSION['user_id'], ':create_date' => date('Y-m-d H:i:s'));
            }
            debug('SQL:' . $sql1);
            debug('流し込みデータ：' . print_r($data1, true));
            // // 実績初回記載時
            // if ($firstReg_flg) {
            //     debug('累積時間テーブルに登録します。');
            //     $sql2 = 'INSERT into total_active_time (user_id,total_hour,total_minute,first_active_day,create_date) VALUES (:u_id,:total_hour,total_minute,:first_active_day,:create_date)';
            //     $data2 = array(':u_id' => $_SESSION['user_id'], ':total_hour' => $a_time, ':first_active_day' => $a_date, ':create_date' => date('Y-m-d H:i:s'));
            //     // ２度目以降の実績記載時
            // } else {
            //     debug('累積時間テーブルを更新します。');
            //     $sql2 = '';
            //     $data = '';
            //     $calc_time = getSumTime($dbTotalTime['total_time'], $a_time);
            //     if (strtotime($dbTotalTime['first_active_day']) > strtotime($a_date)) {
            //         $sql2 = 'UPDATE total_active_time SET total_hour = :total_hour,total_minute = :total_minute, first_active_day = :first_active_day WHERE user_id = :u_id';
            //         $data2 = array(':total_time' => $calc_time, ':first_active_day' => $a_date, ':u_id' => $_SESSION['user_id']);
            //     } else {
            //         $sql2 = 'UPDATE total_active_time SET total_time = :total_time WHERE user_id = :u_id';
            //         $data2 = array(':total_time' => $calc_time, ':u_id' => $_SESSION['user_id']);
            //     }
            // }
            // debug('SQL:' . $sql2);
            // debug('流し込みデータ：' . print_r($data2, true));
            // クエリ実行
            $stmt1 = queryPost($dbh, $sql1, $data1);
            // $stmt2 = queryPost($dbh, $sql2, $data2);

            // クエリ成功の場合
            if ($stmt1) {
                $_SESSION['msg_success'] = SUS06;
                debug('マイページへ遷移します');
                header("Location:mypage.php");
            }
        } catch (Exception $e) {
            error_log('エラー発生：' . $e->getMessage());
            $err_msg['common'] = MSG07;
        }
    }
}

?>


<?php
$siteTitle = '実績記載';
require('head.php');
?>

<body>
    <!-- ヘッダー -->
    <?php
    require('header.php');
    ?>

    <!-- メインコンテンツ -->
    <section class="main">
        <h1 class="title">実績記載</h1>
        <!-- main -->
        <form action="" method="post" class="form form-actualedit" enctype="multipart/form-data">
            <div class="area-msg">
                <?php
                if (!empty($err_msg['common'])) echo $err_msg['common'];
                ?>
            </div>
            <label class="<?php if (!empty($err_msg['a_title'])) echo 'err'; ?>">
                タイトル<span class="label-require">必須</span>
                <input type="text" name="a_title" value="<?php if (!empty($err_msg)) echo $_POST['a_title']; ?>">
            </label>
            <div class="area-msg">
                <?php
                if (!empty($err_msg['a_title'])) echo $err_msg['a_title'];
                ?>
            </div>

            <label class="<?php if (!empty($err_msg['a_date'])) echo 'err'; ?>">
                実施日<span class="label-require">必須</span>
                <input type="text" name="a_date" value="<?php if (!empty($err_msg)) echo $_POST['a_date']; ?>" class="calender">
            </label>
            <div class="area-msg">
                <?php
                if (!empty($err_msg['a_date'])) echo $err_msg['a_date'];
                ?>
            </div>

            <label class="a_time-form <?php if (!empty($err_msg['a_time'])) echo 'err'; ?>">
                実施時間<span class="label-require">必須</span>
                <!-- <input type="text" name="a_time" value="" class="a_time-input"> -->
                <div class="selectbox hour">
                    <span class="icn_select"></span>
                    <select name="a_hour" id="">
                        <?php
                        for ($i = 0; $i < 25; $i++) {
                            ?>
                        <option value="<?php echo $i; ?>" <?php if (!empty($_POST['a_hour']) && $_POST['a_hour'] == $i) {
                                                                echo 'selected';
                                                            } ?>><?php echo $i; ?></option>
                        <?php

                    }
                    ?>
                    </select>
                </div>
                <p class="a_time-parts">時間</p>
                <div class="selectbox minute">
                    <span class="icn_select"></span>
                    <select name="a_minute" id="">
                        <?php
                        for ($i = 0; $i < 60; $i++) {
                            ?>
                        <option value="<?php echo $i; ?>" <?php if (!empty($_POST['a_minute']) && $_POST['a_minute'] == $i) {
                                                                echo 'selected';
                                                            } ?>><?php echo $i; ?></option>
                        <?php

                    }
                    ?>
                    </select>
                </div>
                <p class="a_time-minute-parts">分</p>

            </label>
            <div class="area-msg">

            </div>

            <label class="a_category-form <?php if (!empty($err_msg['a_category'])) echo 'err'; ?>">
                カテゴリー<span class="label-require">必須</span>
                <div class="selectbox">
                    <span class="icn_select"></span>

                    <select name="c_id" id="">
                        <option value="0" <?php if (getFormdata('c_id', true) == 0) {
                                                echo 'selected';
                                            } ?>>選択してください</option>
                        <?php
                        foreach ($dbCategoryData as $key => $val) {
                            ?>
                        <option value="<?php echo $val['id'] ?>" <?php if (!empty($_POST['c_id']) && $_POST['c_id'] == $val['id']) {
                                                                        echo 'selected';
                                                                    } ?>>
                            <?php echo $val['category_name']; ?>
                        </option>
                        <?php 
                    }
                    ?>

                    </select>
                </div>
            </label>
            <div class="area-msg">
                <?php
                if (!empty($err_msg['c_id'])) echo $err_msg['c_id'];
                ?>
            </div>

            <label class="form-group">
                <p class="comment">内容
                    <span>&nbsp;&nbsp;&nbsp;&nbsp;<span class="help-block">※200文字以内にてご入力ください</span></span>
                </p>
                <textarea class="" name="comment" id="count-contact-text" cols="63" rows="8" value="<?php if (!empty($_POST['comment'])) echo $_POST['comment']; ?>"></textarea>
                <div class="comment-su"><span class="comment-count">0</span><span>/ 200</span></div>

            </label>

            <div style="overflow:hidden;">
                <div class="imgDrop-container">
                    画像１
                    <label class="area-drop">
                        <input type="hidden" name="MAX_FILE_SIZE" value="3145728">
                        <input type="file" name="pic1" class="input-file" style="min-height:140px">
                        <img src="<?php echo getFormData('pic1'); ?>" alt="" class="prev-img" style="">
                        <span class="drug-drop">ドラッグ&ドロップ</span>
                    </label>
                    <div class="area-msg">

                    </div>
                </div>
                <div class="imgDrop-container">
                    画像２
                    <label class="area-drop">
                        <input type="hidden" name="MAX_FILE_SIZE" value="3145728">
                        <input type="file" name="pic2" class="input-file" style="min-height:140px">
                        <img src="<?php echo getFormData('pic2'); ?>" alt="" class="prev-img" style="">
                        <span class="drug-drop">ドラッグ&ドロップ</span>
                    </label>
                    <div class="area-msg">

                    </div>
                </div>
                <div class="imgDrop-container">
                    画像３
                    <label class="area-drop">
                        <input type="hidden" name="MAX_FILE_SIZE" value="3145728">
                        <input type="file" name="pic3" class="input-file" style="min-height:140px">
                        <img src="<?php echo getFormData('pic3'); ?>" alt="" class="prev-img" style="">
                        <span class="drug-drop">ドラッグ&ドロップ</span>
                    </label>
                    <div class="area-msg">

                    </div>
                </div>
            </div>

            <div class="btn-contner">
                <input type="submit" name="submit" class="btn btn-mid" value="実績記載">
            </div>
        </form>
    </section>



    <!-- フッター -->
    <?php
    require('footer.php');
    ?> 