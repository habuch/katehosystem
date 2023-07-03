<?php
require_once("./dbconnect.php");
include_once("./functions.php");
include_once('./session.php');

if (isset($_GET['id']) && isset($_GET['from'])) :
  $id = $_GET['id'];

//SQLの発行
if ($_GET['from'] === 'index') :
  $backpage = $_GET['from'] . '.php?';
  $sql = 'DELETE FROM records WHERE id = ? AND user_id = ?';
  $stmt = $db->prepare($sql);
  $stmt->bind_param('ii', $id, $user_id);
elseif ($_GET['from'] === 'item-edit' && isset($_GET['table_number'])) :
  $table_number = $_GET['table_number'];
  $table_list = ['spending_category', 'income_category', 'payment_method', 'creditcard', 'qr'];
  $table_name = $table_list[$table_number];
  $backpage = $_GET['from'] . '.php?editItem=' . $table_number . '&';
  //SQLの発行
  $sql = "DELETE FROM {$table_name} WHERE id = ? AND user_id=?";
  $stmt = $db->prepare($sql);
  $stmt->bind_param('ii', $id, $user_id);
  
endif;

  sql_check($stmt, $db);

  //ホーム画面にパラメータ付きで戻す
  header('Location: ./' . $backpage . 'dataOperation=delete');
  exit();

//不正な遷移が行われたときの処理
else :
  //ホーム画面にパラメータ付きで戻す
  header('Location: ./' . $backpage . 'dataOperation=error');
  exit();

endif;