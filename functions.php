<?php
//SQL実行チェック関数
function sql_check($stmt, $db){
  //SQLが正しくない場合はエラーを表示
  if (!$stmt) :
    die($db->error);
  endif;
  
  //正しければSQL実行
  $success = $stmt->execute();
  
  //実行されなかったらエラー表示
  if (!$success) :
    die($db->error);
  endif;
}
function h($value) {
    return htmlspecialchars($value);
  }