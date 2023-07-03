<?php
session_start();
require_once('./dbconnect.php');
include_once('./functions.php');

//初回ログイン情報処理
if (isset($_SESSION['login_times']) && $_SESSION['login_times'] === "first") :
  $login_times = 'first';
else :
  $login_times = 'not_first';
endif;
//ログインボタン押下処理
if ($_SERVER['REQUEST_METHOD'] === 'POST') :
  $post_username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_SPECIAL_CHARS);
  $post_password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_SPECIAL_CHARS);
  //ログイン確認
  $sql = 'SELECT * FROM user WHERE username = ? LIMIT 1';
  $stmt = $db->prepare($sql);

  $stmt->bind_param('s', $post_username);
  sql_check($stmt, $db);

  $stmt->bind_result($user_id, $nickname, $username, $hash_password, $initial_savings);
  $stmt->fetch();

  if (password_verify($post_password, $hash_password)) : //ログイン成功時
  
    session_regenerate_id();
    $_SESSION['user_id'] = $user_id;
    $_SESSION['nickname'] = $nickname;
    $_SESSION['username'] = $username;
    $_SESSION['initial_savings'] = $initial_savings;
  
    if (isset($_POST['to_login'])) :
      header('Location: ./index.php');
      exit();
    elseif (isset($_POST['to_setting'])) :
      header('Location: ./item-edit.php?editItem=1');
      exit();
    endif;
  
    //echo 'ログイン成功';
    exit();
  
  //ログイン失敗時
  else :
    $login = 'error';
  endif;
endif;

?>

<!DOCTYPE html>
<html lang="ja">

<head>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@100;300;400;500;700;900&family=Zen+Maru+Gothic:wght@400;500;700;900&display=swap" rel="stylesheet">
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@100;300;400;500;700;900&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="./css/style.min.css">
  <script src="./js/footer-fixed.js"></script>
  <title>カテほう</title>
</head>

<body id="body" class="body">
  <header class="l-header--join">
    <h1 class="l-header__title l-header__title--join">カテほう</h1>
  </header>

  <main class="l-main">
  <?php if ($login === 'error') : ?>
    <section class="p-section p-section__message p-section__message--join">
      <div class="p-message-box p-message-box--error">
        <p>ユーザー名またはパスワードが間違えています。</p>
      </div>
    </section>
  <?php endif; ?>
    <section class="p-section p-section__login">
      <form class="p-form p-form--login" action="" method="POST">
        <div class="p-form__vertical-input">
          <p>ユーザー名<span>※半角英数字6〜12文字</span></p>
          <input type="text" name="username" autocomplete="off" minlength="6" maxlength="12" pattern="[0-9a-zA-Z]+$" value="" required>
        </div>
        <div class="p-form__vertical-input">
          <p>パスワード<span>※半角英数字6〜12文字</span></p>
          <input type="password" name="password" autocomplete="off" minlength="6" maxlength="12" pattern="[0-9a-zA-Z]+$" required>
        </div>
        <?php if ($login_times === 'not_first') : ?>
          <input class="c-button c-button--bg-blue" type="submit" name="to_login" value="ログイン">
        <?php endif; ?>
        <?php if ($login_times === 'first') : ?>
          <input class="c-button c-button--bg-blue" type="submit" name="to_setting" value="初期設定へ">
        <?php endif; ?>
      </form>

      <?php if ($login_times === 'not_first') : ?>
        <p>ユーザー登録がお済みでない方</p>
        <a class="c-button c-button--bg-blue" href="./join/index.php">新規ユーザー登録</a>
      <?php endif; ?>

    </section>
  </main>

  <footer id="footer" class="l-footer">
    <p>カテほう</p>
  </footer>

</body>

</html>