<?php
require_once('../dbconnect.php');
include_once('../functions.php');


//書き直しモードでセッション内容を復元

session_start();

if (isset($_GET['mode']) && $_GET['mode'] === 'modify' && isset($_SESSION['nickname'], $_SESSION['username'], $_SESSION['password'], $_SESSION['initial_savings'])) :
  $nickname = $_SESSION['nickname'];
  $username = $_SESSION['username'];
  $password = $_SESSION['password'];
  $initial_savings = $_SESSION['initial_savings'];
else :
  $nickname = '';
  $username = '';
  $password = '';
  $initial_savings = '';
endif;



if ($_SERVER['REQUEST_METHOD'] === 'POST') :
  //フォームデータ格納
  $nickname = filter_input(INPUT_POST, 'nickname', FILTER_SANITIZE_SPECIAL_CHARS);
  $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_SPECIAL_CHARS);
  $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_SPECIAL_CHARS);
  $password_confirm = filter_input(INPUT_POST, 'password_confirm', FILTER_SANITIZE_SPECIAL_CHARS);
  $initial_savings = filter_input(INPUT_POST, 'initial_savings', FILTER_SANITIZE_SPECIAL_CHARS);
  

  //ユーザーネーム重複確認
  $sql = 'SELECT COUNT(*) FROM user WHERE username = ?'; //入力されたユーザー名のデータの数を抽出するSQL文
  $stmt = $db->prepare($sql); //上記SQLをセット
  $stmt->bind_param('s', $username); //？の部分に入力されたユーザー名をセット
  sql_check($stmt, $db);

  $stmt->bind_result($count); //データの数を取得する
  $stmt->fetch();

  if ($count === 0) : //データの数が０なら
  $exist = 'notexist'; //存在しないことを示す文字列をセット
  else : //データの数が０以外なら
    $exist = 'exist'; //データが存在することを示す文字列をセット
  endif;

  //パスワード一致確認
  if ($password !== $password_confirm) :
    $password_match = 'notmatch';
  else :
    $password_match = 'match';
  endif;

  //ユーザー名とパスワードが異なる文字列か
  if ($username === $password) :
    $samestr = "same";
  else :
    $samestr = "different";
  endif;

endif;


session_start();

if ($exist === 'notexist' && $password_match === 'match' && $samestr === "different") :

  $_SESSION['nickname'] = $nickname;
  $_SESSION['username'] = $username;
  $_SESSION['password'] = $password;
  $_SESSION['initial_savings'] = $initial_savings;

  header('Location: confirm.php');
  exit();
endif;


$page_title = '新規ユーザー登録';
include_once('./header.php');

?>

  <main class="l-main">
  <?php if ($exist === 'exist'|| $password_match === 'notmatch'|| $samestr === 'same') : ?>
    <section class="p-section p-section__message p-section__message--join">
      <div class="p-message-box p-message-box--error">

      <?php if ($exist === 'exist') : ?>
        <p>すでに登録されているユーザー名です。</p>
      <?php endif; ?>

      <?php if ($password_match === 'notmatch') : ?>
        <p>パスワードが一致しません。</p>
      <?php endif; ?>
      </div>
    </section>
  <?php endif; ?>

    <section class="p-section p-section__join-input">
      <form class="p-form p-form--join" action="" method="post">
        <div class="p-form__vertical-input">
          <p>ニックネーム<span class="c-text--red">※必須</span><span>※12文字以内</span></p>
          <input type="text" name="nickname" maxlength="12" autocomplete="off" id="nickname" value="<?php echo h($nickname);?>" required>
        </div>
        <div class="p-form__vertical-input">
          <p>ユーザー名<span class="c-text--red">※必須</span><span>※半角英数字6〜12文字</span></p>
          <input type="text" name="username" autocomplete="off" id="username" minlength="6" maxlength="12" pattern="^[0-9a-zA-Z]+$" value="<?php echo h($username);?>" required>
        </div>
        <div class="p-form__vertical-input">
          <p>パスワード<span class="c-text--red">※必須</span><span>※半角英数字6〜12文字</span></p>
          <input type="password" name="password" autocomplete="off" id="password" minlength="6" maxlength="12" pattern="^[0-9a-zA-Z]+$" value="<?php echo h($password);?>" required>
        </div>
        <div class="p-form__vertical-input">
          <p>確認パスワード<span class="c-text--red">※必須</span></p>
          <input type="password" autocomplete="off" name="password_confirm" minlength="6" maxlength="12" id="passwordConfirm" pattern="^[0-9a-zA-Z]+$" value="" required>
        </div>
        <div class="p-form__vertical-input">
          <p>年齢<span>(任意)</span></p>
          <label><input type="number" autocomplete="off" name="initial_savings" id="initial_savings" value="<?php echo h($initial_savings);?>"> 歳</label>
        </div>
        <input class="c-button c-button--bg-blue" type="submit" value="確認画面へ">
      </form>
      <p>ユーザー登録がお済みの方</p>
      <a class="c-button c-button--bg-blue" href="../login.php">ログイン画面へ</a>
    </section>
  </main>

  <footer id="footer" class="l-footer">
    <p>カテほう</p>
  </footer>

</body>

</html>