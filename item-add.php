<?php
require_once('./dbconnect.php');
include_once('./functions.php');
include_once('./session.php');

echo "item-add.phpに移動しました";
$editItem = filter_input(INPUT_POST, 'editItem', FILTER_SANITIZE_NUMBER_INT);
$name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_SPECIAL_CHARS);

$table_list = ['spending_category', 'income_category', 'payment_method', 'creditcard', 'qr'];
$table_name = $table_list[$editItem];

$sql = "SELECT COUNT(*) FROM {$table_name} WHERE name=? AND user_id=?";
$stmt = $db->prepare($sql);
$stmt->bind_param('si', $name, $user_id);
sql_check($stmt, $db);
$stmt->bind_result($count);
$stmt->fetch();
if ($count > 0) :
  header('location: ./item-edit.php?editItem=' . $editItem . '&dataOperation=duplicate');
  exit();
endif;
$stmt->close();

echo $editItem . $name . $table_name;
if ($table_name !== null) :
    $sql = "INSERT INTO {$table_name} (name, user_id) VALUES(?,?)";
    $stmt = $db->prepare($sql);
    $stmt->bind_param('si', $name, $user_id);
    sql_check($stmt, $db);
    header('Location: ./item-edit.php?editItem=' . ($editItem + 1));
  else :
    header('Location: ./item-edit.php?editItem=' . $editItem);
  endif;
  
  exit();