<?php
session_start();
require_once('../dbconnect.php');
include_once('../functions.php');

//セッションがあれば値を格納、なければindex.phpに戻す
if (isset($_SESSION['nickname']) && isset($_SESSION['username']) && isset($_SESSION['password']) && isset($_SESSION['initial_savings'])) :
  $nickname = $_SESSION['nickname'];
  $username = $_SESSION['username'];
  $password = $_SESSION['password'];
  $initial_savings = $_SESSION['initial_savings'];
else :
  header('Location: index.php');
  exit();
endif;

//登録ボタン押下でデータを登録
if ($_SERVER['REQUEST_METHOD'] === 'POST') :
  $sql = 'INSERT INTO user(nickname, username, password, initial_savings) VALUES(?, ?, ?, ?)';
  $stmt = $db->prepare($sql);
  $encryption = password_hash($password, PASSWORD_DEFAULT);
  $stmt->bind_param('sssi', $nickname, $username, $encryption, $initial_savings);
  sql_check($stmt, $db);

  unset($_SESSION['nickname'], $_SESSION['username'], $_SESSION['password'], $_SESSION['initial_savings']);

  $_SESSION['login_times'] = 'first';
  
  header('Location: thanks.php');
endif;

$page_title = '登録情報確認';
include_once('./header.php');
?>

  <main class="l-main">
    <section class="p-section p-section__join-confirm">
      <form class="p-form p-form--join" action="" method="post">
        <div class="p-form__vertical-input">
          <p>ニックネーム</p>
          <p>【<?php echo h($nickname); ?>】</p>
        </div>
        <div class="p-form__vertical-input">
          <p>ユーザー名</p>
          <p>【<?php echo h($username); ?>】</p>
        </div>
        <div class="p-form__vertical-input">
          <p>パスワード</p>
          <p>【セキュリティ上表示されません】</p>
        </div>
        <div class="p-form__vertical-input">
          <p>年齢</p>
          <p>【<?php echo $initial_savings !== '' ? number_format(h($initial_savings)) . '歳' : '未登録'; ?>】</p>
        </div>
        <div class="u-flex-box">
          <a class="c-button c-button--bg-gray" href="./index.php?mode=modify">修正する</a>
          <input class="c-button c-button--bg-blue" type="submit" value="登録する">
        </div>
      </form>
    </section>
  </main>

  <footer id="footer" class="l-footer">
    <p>カテほう</p>
  </footer>

</body>

</html>