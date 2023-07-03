<?php
require_once('./dbconnect.php');
include_once('./functions.php');
include_once('./session.php');

$table_list = ['spending_category', 'income_category', 'payment_method', 'creditcard', 'qr'];

$id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_SPECIAL_CHARS);
$editItem = filter_input(INPUT_POST, 'editItem', FILTER_SANITIZE_NUMBER_INT);
$name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_SPECIAL_CHARS);

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

$sql = "UPDATE {$table_name} SET name=? WHERE id=? AND user_id=?";
$stmt = $db->prepare($sql);
$stmt->bind_param('sii', $name, $id, $user_id);

if (!$stmt) :
  header('Location: ./item-edit.php?editItem=' . $editItem . '&dataOperation=error');
  exit();
endif;

$success = $stmt->execute();

if (!$success) :
  header('Location: ./item-edit.php?editItem=' . $editItem . '&dataOperation=error');
  exit();
endif;

header('Location: ./item-edit.php?editItem=' . $editItem . '&dataOperation=update');
exit();