<?php
session_start();
if (!isset($_SESSION['login_times']) || !$_SESSION['login_times'] === 'first') :
  header('Location: ../login.php');
endif;


$page_title = '登録完了';
include_once('./header.php');

?>
  <main class="l-main">
    <section class="p-section p-section__thanks">
      <div class="p-message-box p-message-box--desc">
        <p>ユーザー登録が完了しました</p>
        <a class="c-button c-button--bg-blue" href="../login.php">ログイン画面へ</a>
      </div>
    </section>
  </main>

  <footer id="footer" class="l-footer">
    <p>カテほう</p>
  </footer>

</body>

</html>