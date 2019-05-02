<?php
// 共通関数の読み込み
require('function.php');
// debug('=================');
// debug('マイページ');
// debug('=================');
// debugLogStart();

// ログイン認証
require('auth.php');

// =================
// 画面処理
// =================
// GETパラメータを取得
// カレントページ
$currentPageNum = (!empty($_GET['p'])) ? (int)$_GET['p'] : 1;
// DBからユーザー情報を取得
$dbFormData = getUser($_SESSION['user_id']);
// カテゴリーID
$c_id = (!empty($_GET['c_id'])) ? $_GET['c_id'] : '';
// ソート順
$sort = (!empty($_GET['sort'])) ? $_GET['sort'] : '';
// 表示件数
$listSpan = 20;
// 現在のレコード先頭を産出
$currentMinNum = (($currentPageNum - 1) * $listSpan);
$startDate = '';
$endDate = '';
if (!empty($_GET['search'])) {
    if (((int)$_GET['start-year'] === 0 || (int)$_GET['start-month'] === 0 || (int)$_GET['end-year'] === 0 || (int)$_GET['end-month'] === 0) && ((int)$_GET['start-year'] !== 0 || (int)$_GET['start-month'] !== 0 || (int)$_GET['end-year'] !== 0 || (int)$_GET['end-month'] !== 0)) {
        global $$err_msg;
        $c_id = '';
        $sort = '';
        $err_msg['search-day'] = MSG18;
        $err_msg['common'] = MSG19;
    } else {
        if ((int)$_GET['start-year'] !== 0 && (int)$_GET['start-month'] !== 0 && (int)$_GET['end-year'] !== 0 && (int)$_GET['end-month'] !== 0) {
            $start_month = '';
            $end_month = '';
            if ((int)$_GET['start-month'] > 0 && (int)$_GET['start-month'] < 10) {
                $start_month = '0' . $_GET['start-month'];
            }
            if ((int)$_GET['end-month'] > 0 && (int)$_GET['end-month'] < 10) {
                $end_month = '0' . $_GET['end-month'];
            }
            $startDate = $_GET['start-year'] . '-' . $start_month . '-01';
            $endDate = $_GET['end-year'] . '-' . $end_month . '-01';
        }
    }
}


$dbPerfData = getPerformanceAll($_SESSION['user_id'], $c_id, $sort, $startDate, $endDate, $currentMinNum);
$dbPerfData_Prof = getPerfAll($_SESSION['user_id']);
$dbCategoryData = getCategory($_SESSION['user_id']);

$sumHourTime = sumHourTime($dbPerfData_Prof);
$sumMinuteTime = sumMinuteTime($dbPerfData_Prof);

// プロフィール画像の有無判定
$userPic = '';
if (!empty($dbFormData['pic'])) {
    $userPic = $dbFormData['pic'];
} else {
    $userPic = 'img/sample-img.png';
}

$nowDate = strtotime(reset($dbPerfData_Prof['data'])['action_date']);


?>

<?php
$siteTitle = 'マイページ';
require('head.php');
?>

<body id="mypage">
    <!-- ヘッダー -->
    <?php
    require('header.php');
    ?>

    <p id="js-show-msg" style="display:none;" class="msg-slide">
        <?php echo getSessionFlash('msg_success'); ?>
    </p>

    <!-- メインコンテンツ -->
    <section class="main mypage">
        <div class="area-msg">
            <?php
            if (!empty($err_msg['common'])) {
                echo $err_msg['common'];
            }
            ?>
        </div>

        <div id="mycontent">
            <!-- サイドバー -->
            <div id="side-bar">
                <div class="profile-form">
                    <div class="profile-img-form">
                        <img class="profile-img" src="<?php echo $userPic; ?>" alt="">
                    </div>
                    <div class="profile-text">
                        <p><?php echo $dbFormData['username']; ?>さん</p>
                        <?php
                        if ($dbPerfData['data']) {
                            ?>

                            <p><span><?php echo mb_substr(reset($dbPerfData_Prof['data'])['action_date'], 0, 4) . ' 年 ' . mb_substr(reset($dbPerfData_Prof['data'])['action_date'], 5, 2) . ' 月 ' . mb_substr(reset($dbPerfData_Prof['data'])['action_date'], 8, 2) . ' 日 '; ?></span></p>
                            <p>より継続開始！！</p>

                            <p><?php echo '継続開始より ' . floor((strtotime(date('Y-m-d')) - $nowDate) / (60 * 60 * 24) + 1) . ' 日目 '; ?></p>
                            <p>累計時間：<span><?php echo $sumHourTime . ' 時間 ' . $sumMinuteTime . ' 分 '; ?></span></p>
                        <?php

                    } else {
                        ?>
                            <p>実績を記録しましょう！！</p>
                        <?php

                    }
                    ?>
                    </div>
                    <div class="actualEdit_move_form">
                        <a href="actualEdit.php" class="actualEdit_move">実績を記載する</a>
                    </div>


                </div>


            </div>

            <!-- 実績結果 -->
            <section id="active-result">
                <div class="active-field">
                    <div class="title-bar">
                        <div class="title-bar2">
                            <h2 class="mytitle">実績一覧</h2>
                            <!-- 検索チェックボックス -->
                            <div class="search-form-button">
                                <div class="serch-form-button2 ol-searchform">
                                    <input id="ol-search" type="checkbox">
                                    <label for="ol-search" class="search-icon"></label>
                                    <div class="ol-search-wrap">
                                        <label for="ol-search" class="overlay"></label>
                                        <!--検索フォーム-->
                                        <form role="search" method="get" class="searchform" action="#">
                                            <h1 class="search-title">検索フォーム</h1>
                                            <p class="search-category search-input">カテゴリー</p>
                                            <div class="selectbox">
                                                <span class="icn_select"></span>
                                                <select name="c_id" id="">
                                                    <option value="0" <?php if (!empty($_GET['c_id']) && $_GET['c_id'] == 0) {
                                                                            echo 'selected';
                                                                        } ?>>選択してください</option>
                                                    <?php
                                                    foreach ($dbCategoryData as $key => $val) {
                                                        ?>
                                                        <option value="<?php echo $val['id'] ?>" <?php if (!empty($_GET['c_id']) && $_GET['c_id'] == $val['id']) {
                                                                                                        echo 'selected';
                                                                                                    } ?>>
                                                            <?php echo $val['category_name']; ?>
                                                        </option>
                                                    <?php
                                                }
                                                ?>
                                                </select>
                                            </div>
                                            <p class="search-sort search-input">表示順</p>
                                            <div class="selectbox">
                                                <span class="icn_select"></span>
                                                <select name="sort" id="">
                                                    <!-- <option value="0">選択してください</option> -->
                                                    <option value="1">新しい順</option>
                                                    <option value="2">古い順</option>
                                                </select>
                                            </div>
                                            <p class="search-day search-input">期間&nbsp;&nbsp;<span class=<?php if (!empty($err_msg['search-day'])) {
                                                                                                                echo 'area-msg';
                                                                                                            } ?>><?php if (!empty($err_msg['search-day'])) {
                                                                                                                                                                            echo $err_msg['search-day'];
                                                                                                                                                                        } else {
                                                                                                                                                                            echo "※開始年月と終了年月をご指定ください。";
                                                                                                                                                                        } ?></span></p>

                                            <div class="input-day">

                                                <select name="start-year" id="" class="search-field">
                                                    <option value="0" <?php if (empty($_GET['start_year']) || (int)$_GET['start-year'] === 0) echo 'selected'; ?>></option>
                                                    <?php
                                                    for ($i = 2018; $i < 2031; $i++) {
                                                        ?>
                                                        <option value="<?php echo $i; ?>" <?php if (!empty($_GET['start-year']) && (int)$_GET['start-year'] === $i) echo 'selected'; ?>>
                                                            <?php
                                                            echo $i;
                                                            ?>
                                                        </option>
                                                    <?php

                                                }
                                                ?>
                                                </select>
                                                <p class="start-year-parts">年</p>
                                                <select name="start-month" id="" class="search-field start-month">
                                                    <option value="0" <?php if (empty($_GET['start-month']) || (int)$_GET['start-month'] === 0) echo 'selected'; ?>></option>
                                                    <?php
                                                    for ($i = 1; $i < 13; $i++) {
                                                        ?>
                                                        <option value="<?php echo $i; ?>" <?php if (!empty($_GET['start-month']) && (int)$_GET['start-month'] === $i) echo 'selected'; ?>>
                                                            <?php
                                                            echo $i;
                                                            ?>
                                                        </option>
                                                    <?php

                                                }
                                                ?>
                                                </select>
                                                <p class="start-month-parts">月&nbsp;&nbsp;〜</p>
                                                <select name="end-year" id="" class="search-field end-year">
                                                    <option value="0" <?php if (empty($_GET['end-year']) || (int)$_GET['end-year'] === 0) echo 'selected'; ?>></option>
                                                    <?php
                                                    for ($i = 2018; $i < 2031; $i++) {
                                                        ?>
                                                        <option value="<?php echo $i; ?>" <?php if (!empty($_GET['end-year']) && (int)$_GET['end-year'] === $i) echo 'selected'; ?>>
                                                            <?php
                                                            echo $i;
                                                            ?>
                                                        </option>
                                                    <?php

                                                }
                                                ?>
                                                </select>
                                                <p class="end-year-parts">年</p>
                                                <select name="end-month" id="" class="search-field end-month">
                                                    <option value="0" <?php if (empty($_GET['end-month']) || (int)$_GET['end-month'] === 0) echo 'selected'; ?>></option>
                                                    <?php
                                                    for ($i = 1; $i < 13; $i++) {
                                                        ?>
                                                        <option value="<?php echo $i; ?>" <?php if (!empty($_GET['end-month']) && (int)$_GET['end-month'] === $i) echo 'selected'; ?>>
                                                            <?php
                                                            echo $i;
                                                            ?>
                                                        </option>
                                                    <?php

                                                }
                                                ?>
                                                </select>
                                                <p class="end-month-parts">月</p>
                                            </div>

                                            <input class="search-button" type="submit" name="search" value="検索">
                                        </form>
                                        <!--検索フォーム-->
                                    </div>
                                </div>
                            </div>


                            <div class="kensu">
                                <span class="total-num"><?php if (!empty($dbPerfData['total'])) {
                                                            echo sanitize($dbPerfData['total']) . '件のうち ';
                                                        } else {
                                                            echo '0 件のうち';
                                                        }  ?></span>
                                <span class="num"><?php echo (!empty($dbPerfData['data'])) ? $currentMinNum + 1 : 0; ?></span><span> - </span>
                                <span><?php echo $currentMinNum + count($dbPerfData['data']); ?></span><span>件表示</span>
                            </div>
                        </div>
                    </div>

                    <!-- 実積記録一覧 -->
                    <div class="active-list-aria">

                        <?php
                        foreach ($dbPerfData['data'] as $key => $val) {
                            ?>
                            <div class="active-parts">
                                <!-- 画像 -->
                                <div class="active-img-form">
                                    <img class="active-img" src="<?php if (!empty($val['pic1'])) {
                                                                        echo $val['pic1'];
                                                                    } elseif (!empty($val['pic2'])) {
                                                                        echo $val['pic2'];
                                                                    } elseif (!empty($val['pic3'])) {
                                                                        echo $val['pic3'];
                                                                    } else {
                                                                        echo 'img/sample-img.png';
                                                                    } ?>" alt="">
                                </div>
                                <!-- 詳細 -->
                                <div class="active-detail">

                                    <!-- タイトル -->
                                    <h2 class="active-title"><?php echo $val['title']; ?></h2>
                                    <!-- パラメータ -->
                                    <div class="active-para">
                                        <div class="active-day">
                                            <p>実施日</p>
                                            <p><?php echo mb_substr($val['action_date'], 0, 4) . ' 年 ' . mb_substr($val['action_date'], 5, 2) . ' 月 ' . mb_substr($val['action_date'], 8, 2) . ' 日 '; ?></p>
                                        </div>
                                        <div class="active-time">
                                            <p>実施時間</p>
                                            <p><?php echo mb_substr($val['action_time'], 0, 2) . ' 時間 ' . mb_substr($val['action_time'], 3, 2) . ' 分 '; ?></p>
                                        </div>
                                        <div class="active-category">
                                            <p>カテゴリー</p>
                                            <p><?php echo $val['category_name']; ?></p>
                                        </div>
                                    </div>

                                    <!-- リンク -->
                                    <div class="active-link">
                                        <a href="<?php echo "actualDetail.php?p_id=" . $val['p_id']; ?>" class="active-link-left">続きを読む</a>
                                        <a href=<?php echo "actualEdit.php?p_id=" . $val['p_id']; ?> class="active-link-right">編集</a>
                                    </div>
                                </div>
                            </div>
                        <?php

                    }
                    ?>
                    </div>

                    <!-- ページネイション -->
                    <?php if (!empty($_GET['search'])) {
                        pagination($currentPageNum, $dbPerfData['total_page'], $_GET['c_id'], $_GET['sort'], $_GET['start-year'], $_GET['start-month'], $_GET['end-year'], $_GET['end-month'], $_GET['search']);
                    } else {
                        pagination($currentPageNum, $dbPerfData['total_page']);
                    } ?>

                </div>
            </section>
        </div>

        <div class="footer_dummy"></div>

    </section>

    <!-- フッター -->
    <?php
    require('footer.php');
    ?>