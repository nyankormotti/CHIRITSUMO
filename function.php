<?php
// ==========================
// ログ
// ==========================
// エラーログを記録する
ini_set('log_errors','on');
// エラーログのファイル名を指定する
ini_set('error_log','chiritsumo_php.log');

// ==========================
// デバッグ
// ==========================
// デバッグフラグ
$debug_flg = true;
// デバッグログ関数
function debug($str) {
    global $debug_flg;
    if(!empty($debug_flg)){
        error_log('デバッグ'.$str);
    }
}
// ==========================
// セッション準備・セッション有効期限を伸ばす
// ==========================
// セッションファイルの置き場を変更する
session_save_path("/var/tmp/");
// ガベージコレクションが削除するセッションの有効期限を設定
ini_set('session.gc_maxlifetime',60 * 60 * 24 * 30);
// ブラウザを閉じても削除されないようにクッキー自体の有効期限を伸ばす
ini_set('session.cookie_lifetime',60 * 60 * 24 * 30);
// セッションを使う
session_start();
// 現在のセッションIDを新しく生成したものと置き換える
session_regenerate_id();

// ==========================
// 画面表示処理開始ログ吐き出し関数
// ==========================
function debugLogStart(){
    // debug('============画面表示処理開始');
    // debug('セッションID：'.session_id());

}

// ==========================
// 定数
// ==========================
// エラーメッセージを定数に格納
define('MSG01','入力必須です。');
define('MSG02','文字以内で入力してください。');
define('MSG03','Emailの形式が合っていません。');
define('MSG04','6文字以上で入力してください');
define('MSG05','パスワード(再入力)が合っていません');
define('MSG06','半角英数字にて入力してください');
define('MSG07','エラーが発生しました。しばらく待ってからやり直してください。');
define('MSG08','そのEmailは既に登録されています。');
define('MSG09','メールアドレスまたはパスワードが違います');
define('MSG10','日付が正しくありません。');
define('MSG11','存在しない日付です。');
define('MSG12', '現在のパスワードが違います');
define('MSG13', '古いパスワードと同じです');
define('MSG14','文字で入力してください。');
define('MSG15','正しくありません。');
define('MSG16',' カテゴリーを選択してください。');
define('MSG17','そのカテゴリーは使用されています。');
define('MSG18','※開始年月と終了年月をご指定ください。');
define('MSG19','検索条件が不正です。');
define('SUS01', 'パスワードを変更しました。');
define('SUS02',' プロフィールを変更しました');
define('SUS03','メールを送信しました。');
define('SUS04','カテゴリーを登録しました。');
define('SUS05','カテゴリーを削除しました。');
define('SUS06','実績を記載しました。');
define('SUS07',' 実績を編集しました。');
define('SUS08','実績を削除しました。');


// ==========================
// グローバル変数
// ==========================
// エラーメッセージ格納用の配列
$err_msg = array();

// ==========================
// バリデーションチェック
// ==========================
// 未入力チェック
function validInput($str, $key) {
    if($str === '') {
        global $err_msg;
        $err_msg[$key] = MSG01;
    }
}

// 最大文字数チェック
function validMaxLen($str,$key,$max = 255) {
    if(mb_strlen($str) > $max){
        debug('最大文字数:'. mb_strlen($str));
        debug('判定：'.$max);
        global $err_msg;
        $err_msg[$key] = $max.MSG02;
    }
}

// 最小文字数チェック
function validMinLen($str,$key){
    if(mb_strlen($str) < 6){
        global $err_msg;
        $err_msg[$key] = MSG04;
    }

}
// 半角英数字チェック
function validHalf($str,$key){
    if(!preg_match( "/^[a-zA-Z0-9]+$/",$str)){
        global $err_msg;
        $err_msg[$key] = MSG06;
    }
}

// Email形式チェック
function validEmail($str,$key) {
    if(!preg_match( "/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/",$str)){
        global $err_msg;
        $err_msg[$key] = MSG03;
    }
}

// Email重複チェック
function validEmailDup($email){
    global $err_msg;
    // 例外処理
    try{
        // DBへ接続
        $dbh = dbConnect();
        // SQL文作成
        $sql = 'SELECT count(*) FROM user WHERE email = :email AND delete_flg = 0';
        $data = array(':email' => $email);
        // クエリ実行
        $stmt = queryPost( $dbh, $sql, $data);
        // クエリ結果を取得
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if(!empty(array_shift($result))){
            $err_msg['email'] = MSG08;
        }
    }catch(Exception $e) {
        error_log('エラー発生：'.$e->getMessage());
        $err_msg['common'] = MSG07;
    }
}

// パスワードチェック
function validPass($str,$key){
    // 半角英数字チェック
    validHalf($str, $key);
    // 最大文字数チェック
    validMaxLen($str, $key);
    // 最小文字数チェック
    validMinLen($str, $key);
}

// パスワード同一チェック
function validMatch($str1,$str2,$key){
    if($str1 !== $str2) {
        global $err_msg;
        $err_msg[$key] = MSG05;
    }
}

// 日付チェック
function validDate($str, $key){
    list($Y, $m, $d) = explode('-', $str);

    if(!is_numeric($Y) || !is_numeric($m) || !is_numeric($d)){
        global $err_msg;
        $err_msg[$key] = MSG10;
    }
    if (empty($err_msg['$key']) && checkdate($m, $d, $Y) !== true) {
        global $err_msg;
        $err_msg[$key] = MSG11;
    } 
    
}

// 固定長チェック
function validLength($str, $key, $len = 8) {
    if(mb_strlen($str) !== $len){
        global $err_msg;
        $err_msg[$key] = $len.MSG14;
    }
}

// ==========================
// バリデーションチェック完了
// ==========================

// ==========================
// データベース
// ==========================
// DB接続関数
function dbConnect() {
    // DBへの接続準備
    $dsn= 'mysql:dbname=chiritsumo;host=localhost;charset=utf8';
    $user= 'root';
    $password= 'root';
    $options = array(
        // SQL実行失敗時にはエラーコードのみ設定
        PDO::ATTR_ERRMODE => PDO::ERRMODE_SILENT,
        // デフォルトフェッチモードを連想配列方式に設定
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        // バッファモードクエリを使う(一度に結果セットを全て取得し、サーバー負荷を軽減)
        // SELECTで得た結果に対してもrowCountメソッドを使えるようにする
        PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,
    );
     // PDOオブジェクト生成(DB接続)
    $dbh = new PDO($dsn, $user, $password, $options);
    return $dbh;
}

// SQL実行関数
function queryPost($dbh, $sql, $data) {

    // クエリー作成
    $stmt = $dbh->prepare($sql);
    // プレースホルダーに値をセットし、SQL文を実行
    if(!$stmt->execute($data)){
        global $err_msg;
        $err_msg['common'] = MSG07;
        return 0;
    }
    return $stmt;
}

// ユーザー情報を取得
function getUser($u_id){
    // 例外処理
    try{
        // DB接続
        $dbh = dbConnect();
        // SQL文作成
        $sql = 'SELECT * FROM user WHERE id = :u_id AND delete_flg = 0';
        $data = array(':u_id' => $u_id);

        // クエリ実行
        $stmt = queryPost($dbh, $sql, $data);

        if($stmt){
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }else{
            return false;
        }

    }catch(Exception $e){
        error_log('エラー発生：'.$e->getMessage());
        degug('SQLエラーが発生しました。');

    }
}

// GETで取得した実績IDに紐づく実績情報を取得
function getPerformance($u_id,$p_id){
    // 例外処理
    try{
        // DB接続
        $dbh = dbConnect();
        // SQL文作成
        $sql = 'SELECT * FROM performance WHERE user_id = :u_id AND id = :p_id AND delete_flg = 0';
        $data = array(':u_id' => $u_id, ':p_id' => $p_id);
        // クエリ実行
        $stmt = queryPost($dbh,$sql,$data);

        if($stmt){
            // クエリ結果のデータを１レコード返却
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }else{
            return false;
        }
    }catch(Exception $e){
        error_log('エラー発生：'.$e->getMessage());
    }
}

// ユーザーIDに紐づく全ての実績情報を取得
function getPerfAll(){

    // 例外処理
    try{
        // DB接続
        $dbh = dbConnect();
        // SQL文作成
        $sql = 'SELECT * FROM performance WHERE delete_flg = 0 AND user_id = :u_id ORDER BY action_date ASC';
        $data = array(':u_id' => $_SESSION['user_id']);
        
        // クエリ実行
        $stmt = queryPost($dbh, $sql, $data);
        if ($stmt && $stmt->rowCount() > 0) {
            //クエリ結果のデータを全レコードを格納
            $rst['data'] = $stmt->fetchAll();
            return $rst;
        } else {
            return false;
        }

    }catch(Exception $e){
        error_log('エラー発生：' . $e->getMessage());
    }
}

// ユーザーIDに紐づくの実績情報を取得(一覧表自分のみ(20件))
function getPerformanceAll($u_id,$category,$sort,$startDate,$endDate, $currentMinNum = 1 ,$span = 20)
{
    // 例外処理
    try {
        // DB接続
        $dbh = dbConnect();
        // SQL文作成
        $sql = 'SELECT p.id AS p_id,title,action_date,action_time,pic1,pic2,pic3,category_name,c.id AS c_id
        FROM performance AS p INNER JOIN category AS c ON p.category_id = c.id
        WHERE p.user_id = :u_id AND p.delete_flg = 0';
        if(!empty($category)) $sql.=" AND category_id = ". $category;
        if(!empty($startDate) && !empty($endDate)) $sql.=" AND DATE_FORMAT(action_date,'%Y%m') >= DATE_FORMAT('". $startDate."','%Y%m') AND DATE_FORMAT(action_date,'%Y%m') <= DATE_FORMAT('".$endDate."','%Y%m')";
        if(!empty($sort)) {
            switch($sort){
                  case 1:
                    $sql .= ' ORDER BY action_date DESC';
                    break;
                case 2:
                    $sql .= ' ORDER BY action_date ASC';
                    break;
            }
        } else{
            $sql .=' ORDER BY action_date DESC';
        }
        $data = array(':u_id' => $u_id);
        // クエリ実行
        $stmt = queryPost($dbh, $sql, $data);
        // トータルページ数を所得
        $rst['total'] = $stmt->rowCount();
        $rst['total_page'] = ceil( $rst['total']/$span);

        // ページング用
        $sql = 'SELECT p.id AS p_id, title,action_date,action_time,pic1,pic2,pic3,category_name,c.id AS c_id
        FROM performance AS p INNER JOIN category AS c ON p.category_id = c.id
        WHERE p.user_id = :u_id AND p.delete_flg = 0';
        if(!empty($category)) $sql.=' AND category_id = '.$category;
        if(!empty($startDate) && !empty($endDate)) $sql.=" AND DATE_FORMAT(action_date,'%Y%m') >= DATE_FORMAT('". $startDate."','%Y%m') AND DATE_FORMAT(action_date,'%Y%m') <= DATE_FORMAT('".$endDate."','%Y%m')";
        if(!empty($sort)) {
             switch($sort){
                 case 1:
                    $sql .= ' ORDER BY action_date DESC';
                    break;
                case 2:
                    $sql .= ' ORDER BY action_date ASC';
                    break;
            }
        } else{
            $sql .=' ORDER BY action_date DESC';
        }
        
        $sql .= ' LIMIT '.$span.' OFFSET '.$currentMinNum;
        
        
        $data = array(':u_id' => $u_id);
        // クエリ実行
        $stmt = queryPost($dbh, $sql, $data);

        if ($stmt && $stmt->rowCount() > 0) {
            //クエリ結果のデータを全レコードを格納
            $rst['data'] = $stmt->fetchAll();
            return $rst;
        } else {
            return false;
        }
    } catch (Exception $e) {
        error_log('エラー発生：' . $e->getMessage());
    }
}

// ユーザーIDにひもづくカテゴリー情報を取得
function getCategory($u_id){
    // 例外処理
    try{
        // DB接続
        $dbh = dbConnect();
        // SQL文作成
        $sql = 'SELECT * FROM category WHERE user_id = :u_id AND delete_flg = 0';
        $data = array(':u_id' => $u_id);

        // クエリ実行
        $stmt = queryPost($dbh, $sql,$data);

        if($stmt){
            return $stmt->fetchAll();
        }else{
            return false;
        }

    }catch(Exception $e){
        error_log('エラー発生：'.$e->getMessage());
    }
}

// カテゴリーIDに紐づく実績情報の件数を取得
function getPerfCate($c_id){
    // 例外処理
    try{
         // DB接続
        $dbh = dbConnect();
        // SQL文作成
        $sql = 'SELECT * FROM performance WHERE category_id = :c_id AND delete_flg = 0';
        $data = array(':c_id' => $c_id);

        // クエリ実行
        $stmt = queryPost($dbh, $sql,$data );

        if($stmt && $stmt->rowCount() > 0){
             return true;
        }else{
             return false;
        }
    }catch (Exception $e){
         error_log('エラー発生：'.$e-> getMessage());
    }
}

// カテゴリーIDに紐づくカテゴリー情報のカテゴリー名を取得
function getCategoryName($c_id)
{
    // 例外処理
    try {
        // DB接続
        $dbh = dbConnect();
        // SQL文作成
        $sql = 'SELECT category_name FROM category WHERE id = :c_id AND delete_flg = 0';
        $data = array(':c_id' => $c_id);

        // クエリ実行
        $stmt = queryPost($dbh, $sql, $data);

        if ($stmt && $stmt->rowCount() > 0) {
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } else {
            return false;
        }
    } catch (Exception $e) {
        error_log('エラー発生：' . $e->getMessage());
    }
}


// ========================
// メール送信
// ========================
function sendMail($from, $to, $subject, $comment){
    if(!empty($to) && !empty($subject) && !empty($comment)){
        // 文字化けしないように設定
        mb_language("Japanese");
        mb_internal_encoding("UTF-8");

        // メール送信
        $result = mb_send_mail($to, $subject, $comment, "From:".$from);
        // 送信結果を判定
        if($result){
            // debug('メールを送信しました。');
        }else{
            // debug('[エラー発生] メールの送信に失敗しました。');
        }
    }
}

// =========================
// その他
// =========================
// サニタイズ
function sanitize($str) {
    return htmlspecialchars($str,ENT_QUOTES);
}

// フォーム入力保持
function getFormData($str, $flg = false){
    global $err_msg;
    if($flg){
        $method = $_GET;
    }else{
        $method = $_POST;
    }
    global $dbFormData;
    // ユーザーデータがある場合
    if(!empty($dbFormData)){
        // フォームのエラーがある場合
        if(!empty($err_msg[$str])){
            // POSTにデータがある場合
            if(isset($method[$str])){
                // debug('postに情報あり');
                return sanitize($method[$str]);
            } else{
                // debug('DBに情報あり');
                return sanitize($dbFormData[$str]);
            }
        }else{
            // POSTにデータがあり、DBの情報と違う場合
            if(isset($method[$str]) && $method[$str] !== $dbFormData[$str]){
                return sanitize($method[$str]);
            }else{
                return sanitize($dbFormData[$str]);
            }
        }
        
    } else{
        if(isset($method[$str])){
            return sanitize($method[$str]);
        }
    }
}

// sessionを１回だけ取得でして中身を空にする
function getSessionFlash($key){
    if(!empty($_SESSION[$key])){
        $data = $_SESSION[$key];
        $_SESSION[$key] = '';
        return $data;
    }
}

// 認証キー
function makeRandkey($length = 8) {
    static $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJLKMNOPQRSTUVWXYZ0123456789';
    $str = '';
    for($i = 0; $i < $length; $i++){
        $str .= $chars[mt_rand(0,61)];
    }
    return $str;
}

// 画像処理
function uploadImg($file,$key){

    if(isset($file['error']) && is_int($file['error'])) {

        // 例外処理
        try{
            // バリデーション
            switch($file['error']) {
                case UPLOAD_ERR_OK:
                    break;
                case UPLOAD_ERR_NO_FILE:
                    throw new RuntimeException('ファイルが選択されていません。');
                case UPLOAD_ERR_INI_SIZE:
                case UPLOAD_ERR_FORM_SIZE:
                    throw new RuntimeException('ファイルサイズが大きすぎます。');
                default:
                    throw new RuntimeException('その他のエラーが派生しました。');
            }

            // MINEタイプを取得
            $type = @exif_imagetype($file['tmp_name']);
            if(!in_array($type,[IMAGETYPE_GIF,IMAGETYPE_JPEG,IMAGETYPE_PNG], true)){
                throw new RuntimeException('画像形式が未対応です。');
            }

            // sha1_file:ハッシュ化する関数
            $path = 'uploads/'.sha1_file($file['tmp_name']).image_type_to_extension($type);
            if(!move_uploaded_file($file['tmp_name'],$path)){
                throw new RuntimeException('ファイル保存時にエラーが発生しました');
            }

            // 保存したファイルパスの権限を保存
            chmod($path,0644);

            return $path;

        } catch(Exception $e){

            error_log('エラー発生'.$e->getMessage());
            global $err_msg;
            $err_msg[$key] = $e->getMessage();
        }
    }
}

// 合計(時間)の算出
function sumHourTime($dbPerfData){
    $total_time = 0;
    $total_hour = 0;
    $total_minute = 0;
    foreach ($dbPerfData['data'] as $key => $val) {
        $total_hour = $total_hour + (int)mb_substr($val[ 'action_time'],0,2)*3600;
        $total_minute = $total_minute + (int)mb_substr($val['action_time'],3,2)*60;
    }
    $total_time = (int)$total_hour + (int)$total_minute;
    
    $total_hour = (int)($total_time/3600);
    if((int)$total_hour >= 0 && (int)$total_hour < 10){
        $total_hour = '0'. $total_hour;
    }
 
    return $total_hour;
    
}

// 合計(分)の算出
function sumMinuteTime($dbPerfData){
    $total_time = 0;
    $total_hour = 0;
    $total_minute = 0;
    foreach ($dbPerfData['data'] as $key => $val) {
        $total_hour = $total_hour + (int)mb_substr($val[ 'action_time'],0,2)*3600;
        $total_minute = $total_minute + (int)mb_substr($val['action_time'],3,2)*60;
    }
    $total_time = (int)$total_hour + (int)$total_minute;
    $total_minute = (int)(($total_time/60)%60);
    
    if((int)$total_minute >= 0 && (int)$total_minute < 10){
        $total_minute = '0'. $total_minute;
    }
    return $total_minute;
}

// 時刻計算(加算)
function timeSum($dbPerfData)
{
    $total_time = 0;
    foreach ($dbPerfData['data'] as $key => $val) {
        $total_time = $total_time + strtotime($val['action_time']);
    }

    return floor($total_time/3600) . ':' . floor( $total_time /60)%60;
}


// ページング
function pagination($currentPageNum, $totalPageNum, $c_id = '', $sort = '', $start_year = '',$start_month = '',$end_year = '', $end_month = '', $search = '', $link = '', $pageColNum = 5){
    // 現在のページが、総ページ数と同じ かつ 総ページ数が表示項目数以上なら、左にリンク4個出す
    if ($currentPageNum == $totalPageNum && $totalPageNum > $pageColNum) {
        $minPageNum = $currentPageNum - 4;
        $maxPageNum = $currentPageNum;
        // 現在のページが、総ページ数の1ページ前なら、左にリンク３個、右に１個出す
    } elseif ($currentPageNum == ($totalPageNum - 1) && $totalPageNum > $pageColNum) {
        $minPageNum =  $currentPageNum - 3;
        $maxPageNum =  $currentPageNum + 1;
        // 現ページが2の場合は左にリンク1個、右にリンク3個出す。
    } elseif ($currentPageNum == 2 && $totalPageNum > $pageColNum) {
        $minPageNum =  $currentPageNum - 1;
        $maxPageNum =  $currentPageNum + 3;
        // 現在のページが１の場合は、左に何も出さない。右に5個出す。
    } elseif ($currentPageNum == 1 && $totalPageNum > $pageColNum) {
        $minPageNum =  $currentPageNum;
        $maxPageNum =  5;
        // 総ページ数が表示項目より少ない場合は、総ページ数をループのmax、ループのminを1に設定
    } elseif ($totalPageNum < $pageColNum) {
        $minPageNum = 1;
        $maxPageNum = $totalPageNum;
        // それ以外は左に2個出す。
    } else {
        $minPageNum = $currentPageNum - 2;
        $maxPageNum = $currentPageNum + 2;
    }


    echo '<div class="pagination">';
    echo '<ul class="pagination-list">';
    if(!empty($search)){
        if ($currentPageNum != 1) {
        echo '<li class="list-item"><a href="mypage.php?p=1' . $link . '&c_id='.$c_id.'&sort='.$sort.'&start_year='.$start_year.'&start_month='.$start_month.'&end_year='.$end_year.'&end_month='.$end_month.'&search='.$search.'">&lt;</a></li>';
        }
            for ($i = $minPageNum; $i <= $maxPageNum; $i++) {
                echo '<li class="list-item ';
                if ($currentPageNum == $i) {
                    echo 'active';
                }
                echo '"><a href="mypage.php?p=' . $i . $link . '&c_id='.$c_id.'&sort='.$sort.'&start_year='.$start_year.'&start_month='.$start_month.'&end_year='.$end_year.'&end_month='.$end_month.'&search='.$search.'">' . $i . '</a></li>';
            }
        if ($currentPageNum != $maxPageNum && $maxPageNum > 1) {
            echo '<li class="list-item"><a href="mypage.php?p=' . $totalPageNum . $link . '&c_id='.$c_id.'&sort='.$sort.'&start_year='.$start_year.'&start_month='.$start_month.'&end_year='.$end_year.'&end_month='.$end_month.'&search='.$search.'">&gt;</a></li>';
        }

    }else{
        if ($currentPageNum != 1) {
        echo '<li class="list-item"><a href="mypage.php?p=1' . $link . '">&lt;</a></li>';
        }
            for ($i = $minPageNum; $i <= $maxPageNum; $i++) {
                echo '<li class="list-item ';
                if ($currentPageNum == $i) {
                    echo 'active';
                }
                echo '"><a href="mypage.php?p=' . $i . $link . '">' . $i . '</a></li>';
            }
        if ($currentPageNum != $maxPageNum && $maxPageNum > 1) {
            echo '<li class="list-item"><a href="mypage.php?p=' . $totalPageNum . $link . '">&gt;</a></li>';
        }

    }
    echo '</ul>';
    echo '</div>';
}

// GETパラメータの付与
// $del_key：付与から取り除きたいGETパラメータのキー
function appendGetParam($del_key = array()){
    if(!empty($_GET)){
        $str = '?';
        foreach($_GET as $key => $val) {
            if(!in_array($key, $del_key,true)){
                $str .=$key.'='.$val.'&';
            }
        }
        $str = mb_substr($str, 0, -1, "UTF-8");
        return $str;
    }
}

?>