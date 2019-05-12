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

// パラメータに不正な値が入っているかチェック
if (empty($dbFormData)) {
    error_log('エラー発生：指定ページに不正な値が入りました');
    header("Location:mypage.php");
}
$dbCategoryData = getCategoryName($dbFormData['category_id']);

if (!empty($_GET['d_id'])) {

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
            $_SESSION['msg_success'] = SUS08;
            // マイページへ繊維
            header("Location:mypage.php");
        }
    } catch (Exception $e) {
        error_log('エラー発生：' . $e->getMessage());
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
                    <?php if (!empty($dbFormData['pic1'])) { ?>
                        <a href="<?php echo $dbFormData['pic1']; ?>" class="light-img" data-lightbox="lightbox" data-title="実績画像1">
                            <img src="<?php echo $dbFormData['pic1']; ?>" alt="" class="prev-img" style="">
                            <div class="imgCover imgCover--pic"><i class="fa fa-search" aria-hidden="true"></i></div>
                        </a>
                    <?php } else { ?>
                        <img src="<?php echo "img/sample-img.png"; ?>" alt="" class="prev-img" style="">
                    <?php } ?>
                </div>
                <div class="img-container">
                    <?php if (!empty($dbFormData['pic2'])) { ?>
                        <a href="<?php echo $dbFormData['pic2']; ?>" data-lightbox="lightbox" data-title="実績画像2">
                            <img src="<?php echo $dbFormData['pic2']; ?>" alt="" class="prev-img" style="">
                            <div class="imgCover imgCover--pic"><i class="fa fa-search" aria-hidden="true"></i></div>
                        </a>
                    <?php } else { ?>
                        <img src="<?php echo "img/sample-img.png"; ?>" alt="" class="prev-img" style="">
                    <?php } ?>
                </div>
                <div class="img-container">
                    <?php if (!empty($dbFormData['pic3'])) { ?>
                        <a href="<?php echo $dbFormData['pic3']; ?>" data-lightbox="lightbox" data-title="実績画像3">
                            <img src="<?php echo $dbFormData['pic3']; ?>" alt="" class="prev-img" style="">
                            <div class="imgCover imgCover--pic"><i class="fa fa-search" aria-hidden="true"></i></div>
                        </a>
                    <?php } else { ?>
                        <img src="<?php echo "img/sample-img.png"; ?>" alt="" class="prev-img" style="">
                    <?php } ?>
                </div>
            </div>


            <div class="detail-submit">

                <a href="mypage.php<?php echo appendGetParam(array('p_id')); ?>" class="myapage_return">&lt;&lt; マイページに戻る</a>

                <div class="detail-submit-delete">
                    <a href=<?php echo "actualDetail.php?p_id=" . $dbFormData['id'] . "&d_id=1"; ?>>削除</a>
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