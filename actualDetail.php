<?php
 // 共通関数
require('function.php');

// debug('====================');
// debug('実績詳細ページ');
// debug('====================');
// debugLogStart();
// ========================
// 画面処理
// =========================
// 実績IDのGETパラメータ取得
$p_id = (!empty($_GET['p_id'])) ? $_GET['p_id'] : '';
// DBから実績データを取得
$dbFormData = getPerformance($_SESSION['user_id'], $p_id);
// debug('実績データ：' . print_r($dbFormData, true));

// パラメータに不正な値が入っているかチェック
if (empty($dbFormData)) {
    error_log('エラー発生：指定ページに不正な値が入りました');
    header("Location:mypage.php");
}
$dbCategoryData = getCategoryName($dbFormData['category_id']);
// debug('カテゴリーデータ：' . print_r($dbCategoryData, true));

if (!empty($_GET['d_id'])) {
    // debug('削除');

    // 例外処理
    try {
        // DB接続
        $dbh = dbConnect();
        // SQL文作成
        $sql = 'UPDATE performance SET delete_flg = 1 WHERE id = :p_id AND user_id = :u_id';
        $data = array(':p_id' => $p_id, ':u_id' => $_SESSION['user_id']);
        // クエリ実行
        $stmt = queryPost($dbh, $sql, $data);

        // クエリ成功の場合
        if ($stmt) {
            $_SESSION['msg_success'] =SUS08;
            // debug('マイページに遷移します');
            header("Location:mypage.php");
        }
     }catch(Exception $e){
        error_log ('エラー発生：'.$e->getMessage());
        $err_msg['common'] = MSG07;
    }
}

?>

<?php
$siteTitle = '実績詳細';
require('head.php');
?>

<body>
    <!-- ヘッダー -->
    <?php
    require('header.php');
    ?>

    <!-- メインコンテンツ -->
    <section class="main">
        <h1 class="title">実績詳細</h1>
        <div class="detail-area-top">
            <div class="detail-area">
                <h2><?php echo $dbFormData['title']; ?></h2>
                <div class="detail-table">
                    <table>
                        <tbody>
                            <tr>
                                <th>実施日</th>
                                <td><?php echo mb_substr($dbFormData['action_date'], 0, 4) . " 年 " . mb_substr($dbFormData['action_date'], 5, 2) . " 月 " . mb_substr($dbFormData['action_date'], 8, 2) . " 日 "; ?></td>
                            </tr>
                            <tr>
                                <th>実績時間</th>
                                <td>
                                    <?php echo mb_substr($dbFormData['action_time'], 0, 2) . " 時間 " . mb_substr($dbFormData['action_time'], 3, 2) . " 分 " ?>
                                </td>
                            </tr>
                            <tr>
                                <th>カテゴリー</th>
                                <td><?php echo $dbCategoryData['category_name']; ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="detail-comment">
                    <p><?php echo $dbFormData['comment']; ?></p>

                </div>
            </div>

            <div class="detail-img">
                <div class="img-container">
                    <img src="<?php if (!empty($dbFormData['pic1'])) {
                                    echo $dbFormData['pic1'];
                                } else {
                                    echo "img/sample-img.png";
                                } ?>" alt="" class="prev-img" style="">
                </div>
                <div class="img-container">
                    <img src="<?php if (!empty($dbFormData['pic2'])) {
                                    echo $dbFormData['pic2'];
                                } else {
                                    echo "img/sample-img.png";
                                } ?>" alt="" class="prev-img" style="">
                </div>
                <div class="img-container">
                    <img src="<?php if (!empty($dbFormData['pic3'])) {
                                    echo $dbFormData['pic3'];
                                } else {
                                    echo "img/sample-img.png";
                                } ?>" alt="" class="prev-img" style="">
                </div>
            </div>

            <div class="detail-submit">

                <!-- <form action="" method="post" class="detail-submit-delete">
                    <input type="submit" name="delete" value="削除">
                </form> -->

                <div class="detail-submit-delete">
                    <a href=<?php echo "actualDetail.php?p_id=" . $dbFormData['id']."&d_id=1"; ?>>削除</a>
                </div>

                <div class="detail-submit-edit">
                    <a href=<?php echo "actualEdit.php?p_id=" . $dbFormData['id']; ?>>編集</a>
                </div>
            </div>
        </div>

    </section>

    <div class="footer_dummy"></div>


    <?php
    require('footer.php')
    ?>