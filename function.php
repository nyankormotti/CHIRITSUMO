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
    debug('============画面表示処理開始');
    debug('セッションID：'.session_id());

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
define('SUS01', 'パスワードを変更しました。');
define('SUS02',' プロフィールを変更しました');
define('SUS03','メールを送信しました。');
define('SUS04','カテゴリーを登録しました。');
define('SUS05','カテゴリーを削除しました。');
define('SUS06','実績を記載しました。');


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
        debug('クエリ結果：'.print_r($stmt));
        debug('クエリ結果2：'.print_r($result));

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

// 日付形式チェック
// function validDateFormat($str,$key){
//     debug('日付'.$str);
//     $v_yaer = mb_substr($str,0,4);
//     $v_month = mb_substr($str,5,2);
//     $v_day = mb_substr($str,8);
//     debug('年：'. $v_yaer);
//     debug('月：'. $v_month);
//     debug('日：'. $v_day);
//     if (!strptime($str, '%Y-%m-%d') || !is_int($v_yaer) || !is_int($v_month) || !is_int($v_day)) {
//         global $err_msg;
//         $err_msg[$key] = MSG10;
        
//     }
    
// }

// 日付チェック
function validDate($str, $key){
    list($Y, $m, $d) = explode('-', $str);
    debug('年２：'.$Y);
    debug('月２：'.$m);
    debug('日２：'.$d);

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
    $dsn='mysql:dbname=chiritsumo;host=localhost;charset=utf8';
    $user='root';
    $password='root';
    $options = array(
        // SQL実行失敗時にはエラーコードのみ設定
        PDO::ATTR_ERRMODE => PDO::ERRMODE_SILENT,
        // デフォルトフェッチモードを連想配列方式に設定
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        // バッファモードクエリを使う(一度に結果セットを全て取得し、サーバー負荷を軽減)
        // SELECTで得た結果に対してもroeCountメソッドを使えるようにする
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
        debug('クエリに失敗しました。');
        debug('SQLエラー：'.print_r($stmt->errorInfo().true));
        debug('失敗したSQL：'.print_r($stmt,true));
        $err_msg['common'] = MSG07;
        return 0;
    }
    debug('クエリ成功。');
    debug('クエリ：', $stmt);
    return $stmt;
}

// ユーザー情報を取得
function getUser($u_id){
    debug('ユーザー情報を取得します。');
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
    debug('実績情報を取得します。');
    debug('ユーザーID：'.$u_id);
    debug('実績ID：'.$p_id);
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
        debug('SQLエラーが発生しました。');
    }
}

// ユーザーIDに紐づく全ての実績情報を取得
function getPerformanceAll($u_id,$category,$sort,$startDate,$endDate, $currentMinNum = 1 ,$span = 20)
{
    debug('実績情報を取得します。');
    debug('ユーザーID：' . $u_id);
    // 例外処理
    try {
        // DB接続
        $dbh = dbConnect();
        // SQL文作成
        $sql = 'SELECT title,action_date,action_time,pic1,pic2,pic3,category_name 
        FROM performance AS p INNER JOIN category AS c ON p.category_id = c.id
        WHERE p.user_id = :u_id AND p.delete_flg = 0';
        if(!empty($category)) $sql.=' AND category_id = '. $category;
        if(!empty($startDate) && !empty($endDate)) $sql.=' AND action_date >= '.$startDate.' AND action_date <= '.$endDate;
        if(!empty($sort)) {
            switch($sort){
                  case 1:
                    $sql .= ' ORDER BY action_date ASC';
                    break;
                case 2:
                    $sql .= ' ORDER BY action_date DESC';
                    break;
            }
        } else{
            $sql .=' ORDER BY action_date ASC';
        }
        $data = array(':u_id' => $u_id);
        // クエリ実行
        $stmt = queryPost($dbh, $sql, $data);
        $rst['total'] = $stmt->rowCount();
        $rst['total_page'] = ceil( $rst['total']/$span);

        // ページング用
        $sql = 'SELECT title,action_date,action_time,pic1,pic2,pic3,category_name 
        FROM performance AS p INNER JOIN category AS c ON p.category_id = c.id
        WHERE p.user_id = :u_id AND p.delete_flg = 0';
        if(!empty($category)) $sql.=' AND category_id = '.$category;
        if(! empty($startDate) && !empty($endDate)) $sql.=' AND action_date >= '. $startDate.' AND action_ d ate <= '.$endDate;
        if(!empty($sort)) {
             switch($sort){
                 case 1:
                    $sql .= ' ORDER BY action_date ASC';
                    break;
                case 2:
                    $sql .= ' ORDER BY action_date DESC';
                    break;
            }
        } else{
            $sql .=' ORDER BY action_date ASC';
        }
        $sql .= ' LIMIT '.$span.' OFFSET '.$currentMinNum;
        $data = array(':u_id' => $u_id);
        // クエリ実行
        $stmt = queryPost($dbh, $sql, $data);
        debug('SQL：'.$sql);

        if ($stmt && $stmt->rowCount() > 0) {
            //クエリ結果のデータを全レコードを格納
            $rst['data'] = $stmt->fetchAll();
            return $rst;
        } else {
            return false;
        }
    } catch (Exception $e) {
        error_log('エラー発生：' . $e->getMessage());
        debug('SQLエラーが発生しました。');
    }
}

// カテゴリー情報を取得
function getCategory($u_id){
    debug('カテゴリー情報を取得します。');
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
        debug('SQLエラーが発生しました。');
    }
}

// カテゴリーIDに紐づく実績情報の件数を取得
function getPerfCate($c_id){
    debug('カテゴリー情報を取得します。');
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
            debug('true');
             return true;
        }else{
            debug('false');
             return false;
        }
    }catch (Exception $e){
         error_log('エラー発生：'.$e-> getMessage());
        debug('SQLエラーが発生しました。');
    }
}

// 累計時間を取得
function getTotalTime($u_id){
    debug('累計時間を取得します。');
    // 例外処理
    try{
        // DB接続
        $dbh = dbConnect();
        // SQL文作成
        $sql = 'SELECT * FROM total_active_time WHERE user_id = :u_id AND delete_flg = 0';
        $data = array(':u_id' => $u_id);
        // クエリ実行
        $stmt = queryPost($dbh, $sql, $data);
        if($stmt){
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } else {
            return false;
        }
    }catch(Exception $e){
        error_log('エラー発生：'.$e->getMessage());
        debug('SQLエラーが発生しました。');

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
            debug('メールを送信しました。');
        }else{
            debug('[エラー発生] メールの送信に失敗しました。');
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
    // debug('methodの情報：'.print_r($method,true));
    // debug('getFormDataのDB情報:'.print_r($dbFormData[$str],true));
    // ユーザーデータがある場合
    if(!empty($dbFormData)){
        // debug('ユーザー情報あり');
        // フォームのエラーがある場合
        if(!empty($err_msg[$str])){
            // debug('エラーあり');
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
                // debug( 'DB情報：' . print_r($dbFormData[$str], true));
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
        debug('if文中msg_success'.$data);
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
    debug('画像アップロード処理開始');
    debug('FILE情報'.print_r($file,true));

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

            debug('$file_error:'.print_r( $file['error'],true));

            $type = @exif_imagetype($file['tmp_name']);
            if(!in_array($type,[IMAGETYPE_GIF,IMAGETYPE_JPEG,IMAGETYPE_PNG], true)){
                throw new RuntimeException('画像形式が未対応です。');
            }

            $path = 'uploads/'.sha1_file($file['tmp_name']).image_type_to_extension($type);
            if(!move_uploaded_file($file['tmp_name'],$path)){
                throw new RuntimeException('ファイル保存時にエラーが発生しました');
            }

            chmod($path,0644);

            debug('ファイルパスは正常にアップロードされました。');
            debug('ファイルパス'.$path);
            return $path;

        } catch(Exception $e){

            error_log('エラー発生'.$e->getMessage());
            global $err_msg;
            $err_msg[$key] = $e->getMessage();
        }
    }
}

// 時刻計算(加算)
function timeSum($dbPerfData)
{
    $total_time = 0;
    // $total_minute = 0;
    foreach ($dbPerfData['data'] as $key => $val) {
        $total_time = $total_time + strtotime($val['action_time']);
        debug(''.print_r(idate($val['action_time']),true));
    }
    
    
    debug(''.print_r(( floor($total_time / 3600)%24 . ':' . floor($total_time / 60) % 60),true));

    return floor($total_time/3600) . ':' . floor( $total_time /60)%60;
}
// function getSumTime($source_time, $add_time)
// {
//     $source_times = explode(":", $source_time);
//     $add_times = explode(":", $add_time);
//     return date("H:i:s", mktime($source_times[0] + $add_times[0], $source_times[1] + $add_times[1], $source_times[2] + $add_times[2]));
// }

?>