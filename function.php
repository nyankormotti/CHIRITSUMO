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
define('MSG14','文字で入力してください。');
define('MSG15','正しくありません。');
define('SUS03','メールを送信しました。');

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

        if(!empty(array_shift($result))){
            $err_msg['email'] = MSG08;
        }
    }catch(Exception $e) {
        error_log('エラー発生：'.$e->getMessage());
        $err_msg['common'] = MSG07;

    }

}

// パスワード同一チェック
function validMatch($str1,$str2,$key){
    if($str1 !== $str2) {
        global $err_msg;
        $err_msg[$key] = MSG05;
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
    // ユーザーデータがある場合
    if(!empty($dbFormData)){
        // フォームのエラーがある場合
        if(!empty($err_msg[$str])){
            // POSTにデータがある場合
            if(isset($method[$str])){
                return sanitize($method[$str]);
            } else{
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

?>