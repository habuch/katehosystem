<?php
include_once('./session.php');
include_once('./functions.php');
$caution = "";

$page_title = "アカウント管理";
include_once('./header.php');
?>
  <main class="l-main">

  <!-- 操作完了コンテンツ -->
  <?php if ($_GET['dataOperation'] && ($_GET['dataOperation'] === 'pwerror' || $_GET['dataOperation'] === 'update' || $_GET['dataOperation'] === 'error')) : ?>
    <section class="p-section p-section__full-screen" id="doneOperateBox">
      <div class="p-message-box <?php echo ($_GET['dataOperation'] === 'error' || $_GET['dataOperation'] === 'pwerror') ? 'line-red' : 'line-blue'; ?>">
        <p id="doneText">
          <?php
          if ($_GET['dataOperation'] === 'error') {
            echo '正しく処理されませんでした';
          } elseif ($_GET['dataOperation'] === 'pwerror') {
            echo '現在のパスワードが一致しません';
          } elseif ($_GET['dataOperation'] === 'update') {
            echo '更新しました';
          }
          ?>
        </p>
        <button class="c-button <?php echo ($_GET['dataOperation'] === 'error' || $_GET['dataOperation'] === 'pwerror') ? 'c-button--bg-darkred' : 'c-button--bg-blue'; ?>" onclick="onClickOkButton('');">OK</button>
      </div>
    </section>
  <?php endif; ?>
  <!-- //操作完了コンテンツ -->
  
  <?php if (($_SERVER['REQUEST_METHOD'] === 'POST') && (isset($_POST['modify_nickname']) || isset($_POST['modify_username']) || isset($_POST['modify_password']) || isset($_POST['modify_initial_savings']))) : ?>
    <?php
    if (isset($_POST['modify_nickname'])) {
      $item_label = "ニックネーム";
      $column = "nickname";
      $caution = "※12文字以内";
      $modify_item = $_POST['nickname'];
    } elseif (isset($_POST['modify_username'])) {
      $item_label = "ユーザー名";
      $column = "username";
      $caution = "※半角英数字6〜12文字";
      $modify_item = $_POST['username'];
    } elseif (isset($_POST['modify_password'])) {
      $item_label = "パスワード";
      $column = 'password';
      $caution = "※半角英数字6〜12文字";
      $modify_item = "非表示";
    } elseif (isset($_POST['modify_initial_savings'])) {
      $item_label = "初期貯蓄額";
      $column = "initial_savings";
      $modify_item = number_format($_POST['initial_savings']) . '円';
    }
    ?>
      <section class="p-section p-section__full-screen">
        <form class="p-form p-form--account-edit" action="./account-update.php" method="POST">
        <input type="hidden" name="column_value" value="<?php echo h($column); ?>">
          <div class="p-form__vertical-input">
          <p>現在の<?php echo h($item_label); ?></p>
          <?php if ($column === 'password') : ?>
            <input type="password" name="now_password" required>
          <?php else : ?>
            <p><?php echo h($modify_item); ?></p>
          <?php endif; ?>
          </div>
          <div class="p-form__vertical-input">
          <p>新しい<?php echo h($item_label) . h($caution); ?></p>
          <?php if (isset($_POST['modify_nickname'])) : ?>
            <input type="text" name="modify_value" maxlength="12" required>
          <?php elseif (isset($_POST['modify_username'])) : ?>
            <input type="text" name="modify_value" minlength="6" maxlength="12" pattern="^[0-9a-zA-Z]+$" required>
          <?php elseif (isset($_POST['modify_password'])) : ?>
            <input type="password" name="modify_value" minlength="6" maxlength="12" pattern="^[0-9a-zA-Z]+$" required>
          <?php elseif (isset($_POST['modify_initial_savings'])) : ?>
            <input type="number" name="modify_value" required>
          <?php endif; ?>
          </div>
          <div class="p-form__center">
            <a class="c-button" href="./account.php">キャンセル</a>
            <?php if ($column === 'password') : ?>
              <input class="c-button c-button--bg-blue" type="submit" name="password_modify" value="変更する">
            <?php else : ?>
              <input class="c-button c-button--bg-blue" type="submit" name="other_modify" value="変更する">
            <?php endif; ?>
          </div>
        </form>
      </section>
    <?php endif; ?>

    
    <section class="p-section">
      <h2 class="c-text c-text__subtitle">【アカウント管理】</h2>
        <form class="p-form p-form--account" action="" method="POST">
          <div class="info">
            <p>ニックネーム</p>
            <input type="hidden" name="nickname" id="nickname" value="<?php echo h($nickname);?>">
            <p><?php echo h($nickname); ?></p>
            <input class="c-button c-button--bg-blue" type="submit" name="modify_nickname" value="変更">
          </div>
          <div class="info">
            <p>ユーザー名</p>
            <input type="hidden" name="username" id="username" value="<?php echo h($username);?>">
            <p><?php echo h($username);?></p>
            <input class="c-button c-button--bg-blue" type="submit" name="modify_username" value="変更">
          </div>
          <div class="info">
            <p>パスワード</p>
            <input type="hidden" name="password" id="password">
            <p>セキュリティ上非表示</p>
            <input class="c-button c-button--bg-blue" type="submit" name="modify_password" value="変更">
          </div>
          <div class="info">
            <p>年齢</p>
            <input type="hidden" name="initial_savings" id="initialSavings" value="<?php echo h($initial_savings);?>">
            <p><?php echo h($initial_savings !== '' ? number_format($initial_savings) . '歳' : '未登録'); ?></p>
            <input class="c-button c-button--bg-blue" type="submit" name="modify_initial_savings" value="変更">
          </div>
        </form>
    </section>


    <section class="p-section p-section__back-home">
      <a class="c-button c-button--bg-gray" href="./index.php">ホームへ戻る</a>
    </section>
  </main>

  <footer id="footer" class="l-footer">
    <p>カテほう</p>
  </footer>

  <script src="./js/footer-fixed.js"></script>
  <script src="./js/import.js"></script>
  <script src="./js/functions.js"></script>
</body>

</html>