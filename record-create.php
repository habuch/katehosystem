<?php
//DB接続
require_once('./dbconnect.php');
include_once('./functions.php');
include_once('./session.php');

//送信データ受け取り
$date = filter_input(INPUT_POST, 'date', FILTER_SANITIZE_SPECIAL_CHARS);
$title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_SPECIAL_CHARS);
$amount = filter_input(INPUT_POST, 'amount', FILTER_SANITIZE_NUMBER_INT);
$spending_category = filter_input(INPUT_POST, 'spending_category', FILTER_SANITIZE_NUMBER_INT);
$income_category = filter_input(INPUT_POST, 'income_category', FILTER_SANITIZE_NUMBER_INT);
$type = filter_input(INPUT_POST, 'type', FILTER_SANITIZE_NUMBER_INT);
$payment_method = filter_input(INPUT_POST, 'payment_method', FILTER_SANITIZE_NUMBER_INT);
$credit = filter_input(INPUT_POST, 'credit', FILTER_SANITIZE_NUMBER_INT);
$qr = filter_input(INPUT_POST, 'qr', FILTER_SANITIZE_NUMBER_INT);
$memo = filter_input(INPUT_POST, 'memo', FILTER_SANITIZE_SPECIAL_CHARS);
$input_time = filter_input(INPUT_POST, 'input_time', FILTER_SANITIZE_SPECIAL_CHARS);

if ($amount < 0) :
  $_SESSION['r_date'] = $date;
  $_SESSION['r_title'] = $title;
  $_SESSION['r_amount'] = $amount;
  $_SESSION['r_type'] = $type;
  $_SESSION['r_spendingCat'] = $spending_category;
  $_SESSION['r_paymentMethod'] = $payment_method;
  header('location: ./index.php?dataOperation=numberError');
  exit();
endif;

//SQL実行
if (isset($_POST['record_create']) && $_POST['record_create'] === '登録') :
    $sql = 'INSERT INTO records VALUES(0,?,?,?,?,?,?,?,?,?,?,?,?)';
    $stmt = $db->prepare($sql);
    $stmt->bind_param('ssiiiiiiissi', $date, $title, $amount, $spending_category, $income_category, $type, $payment_method,  $credit, $qr, $memo, $input_time,$user_id);
  
  elseif (isset($_POST['record_update']) && $_POST['record_update'] === '更新') :
    $id = filter_input(INPUT_POST, 'record_id', FILTER_SANITIZE_SPECIAL_CHARS);
    $sql = 'UPDATE records SET date=?, title=?, amount=?, spending_category=?, income_category=?, type=?, payment_method=?, credit=?, qr=?, memo=?, input_time=? WHERE id=? AND user_id = ?';
    $stmt = $db->prepare($sql);
    $stmt->bind_param('ssiiiiiiissii', $date, $title, $amount, $spending_category, $income_category, $type, $payment_method,  $credit, $qr, $memo, $input_time, $id, $user_id);
  
  else :
    header('Location: ./index.php?dataOperation=error');
    exit();
  endif;

//SQLチェック関数
sql_check($stmt, $db);

if (isset($_POST['record_create']) && $_POST['record_create'] === '登録') :
    header('Location: ./index.php');
  elseif (isset($_POST['record_update']) && $_POST['record_update'] === '更新') :
    header('Location: ./index.php?dataOperation=update');
  endif;
  
  exit();