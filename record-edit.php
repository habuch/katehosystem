<?php
require_once('./dbconnect.php');
include_once('./functions.php');
include_once('./session.php');

if ($_POST['record_id'] && $_POST['record_id'] > 0) {
  $record_id = $_POST['record_id'];
  echo $record_id;
} else {
  header('Location: ./index.php?dataOperation=error');
  exit();
}

// SQLの発行と実行
$sql = 'SELECT id, date, title, amount, spending_category, income_category, type, payment_method, credit, qr, memo FROM records WHERE id = ? AND user_id = ? LIMIT 1';
$stmt = $db->prepare($sql);
$stmt->bind_param('ii', $record_id, $user_id);
$stmt->execute();

// 取得した値の格納
$stmt->bind_result($id, $date, $title, $amount, $spending_category, $income_category, $type, $payment_method, $credit, $qr, $memo);
$stmt->fetch();

// 不正な遷移が行われた場合の処理
if (!$id) {
  header('Location: ./index.php?dataOperation=error');
  exit();
}

// 仮出力
$br = '<br>';
echo $br . $date . $br . $title . $br . $amount . $br . $spending_category . $br . $income_category . $br . $type . $br . $payment_method . $br . $credit . $br . $qr . $br . $memo;

$stmt->close();

$page_title = "レコード編集";
include_once('./header.php');
?>


  <main class="l-main">
    <!-- 収支データ編集 -->
    <section class="p-section p-section__records-input">
      <h2 class="c-text c-text__subtitle">【レコード編集】</h2>

      <form class="p-form p-form--input-record" name="recordInput" action="./record-create.php" method="POST">
        <input type="hidden" name="record_id" value="<?php echo h($id); ?>">
        <input type="hidden" name="input_time" id="input_time" value="<?php echo date("Y/m/d-H:i:s"); ?>">
        <input type="hidden" name="editItem" value="<?php echo $editItem; ?>">
        <div class="p-form__flex-input">
          <p>日付</p>
          <label for="date"><input type="date" name="date" id="date" value="<?php echo h($date); ?>" required></label>
        </div>

        <div class="p-form__flex-input">
          <p>タイトル</p>
          <input type="text" name="title" id="title" value="<?php echo h($title); ?>" maxlength="15" required>
        </div>

        <div class="p-form__flex-input">
          <p>金額</p>
          <input type="number" name="amount" id="amount" step="1" value="<?php echo h($amount); ?>" maxlength="7" required>
        </div>

        <div class="p-form__flex-input type">
          <input id="spending" type="radio" name="type" value="0" <?php echo $type === 0 ? 'checked' : ''; ?> onchange="onRadioChangeType(0);" required>
          <label for="spending">支出</label>
          <input type="radio" name="type" id="income" value="1" <?php echo $type === 1 ? 'checked' : ''; ?> onchange="onRadioChangeType(1);">
          <label for="income">収入</label>
        </div>

        <div class="u-js__show-switch flex p-form__flex-input sp-change-order" id="spendingCategoryBox">
          <p class="long-name">支出カテゴリー</p>
          <select name="spending_category" id="spendingCategory">
            <option value="0">選択してください</option>
            <?php
              //データベースと連携するためSQLを発行し実行
              $stmt_spendingcat = $db->prepare('SELECT id, name FROM spending_category WHERE user_id = ?');
              $stmt_spendingcat->bind_param('i', $user_id);
              sql_check($stmt_spendingcat, $db);
              $stmt_spendingcat->bind_result($id, $name);
              while ($stmt_spendingcat->fetch()) :
              ?>
                <!--選択項目をループ文で出力＆
                冒頭で取得したカテゴリーの数値とvalue属性の値が一致の場合はselectedを出力で選択状態に
                -->
                <option value="<?php echo h($id); ?>" <?php echo $spending_category === $id ? 'selected' : ''; ?>><?php echo h($name); ?></option>
              <?php endwhile; ?>
          </select>
        </div>

        <div class="u-js__show-switch flex p-form__flex-input sp-change-order" id="incomeCategoryBox">
          <p class="long-name">収入カテゴリー</p>
          <select name="income_category" id="incomeCategory">
            <option value="0">選択してください</option>
            <?php
            $stmt_incomecat = $db->prepare('SELECT id, name FROM income_category WHERE user_id = ?');
            $stmt_incomecat->bind_param('i', $user_id);
            sql_check($stmt_incomecat, $db);
            $stmt_incomecat->bind_result($id, $name);
            while ($stmt_incomecat->fetch()) :
            ?>
              <option value="<?php echo h($id); ?>" <?php echo $income_category === $id ? 'selected' : ''; ?>><?php echo h($name); ?></option>
            <?php endwhile; ?>
          </select>
        </div>

        <div id="paymentMethodBox" class="u-js__show-switch flex p-form__flex-input sp-change-order">
          <p class="long-name">支払い方法</p>
          <select name="payment_method" id="paymentMethod" onchange="hasChildSelect('2', creditSelectBox, qrChecked);hasChildSelect('3', qrSelectBox, creditChecked);">
            <option value="0">選択してください</option>
            <?php
            $fixedPaymentMethod = ['現金', 'クレジット', 'スマホ決済'];
            $fixedPaymentMethod_id = ['', 'radioCredit', 'radioQr'];
            for ($i = 0; $i < 3; $i++) : ?>
              <option value="<?php echo $i + 1; ?>" id="<?php echo $fixedPaymentMethod_id[$i]; ?>"><?php echo $fixedPaymentMethod[$i]; ?></option>
            <?php endfor; ?>

            <?php
            $stmt_paymethod = $db->prepare('SELECT id, name FROM payment_method WHERE id>3 AND user_id = ?');
            $stmt_paymethod->bind_param('i', $user_id);
            sql_check($stmt_paymethod, $db);
            $stmt_paymethod->bind_result($id, $name);
            while ($stmt_paymethod->fetch()) :
            ?>
              <option value="<?php echo h($id); ?>" <?php echo $payment_method === $id ? 'selected' : ''; ?>><?php echo h($name); ?></option>
            <?php endwhile; ?>
          </select>
        </div>

        <div class="u-js__show-switch flex p-form__flex-input sp-change-order" id="creditSelectBox">
          <p class="long-name">クレジットカード</p>
          <div class="p-form__item-box">
            <select name="credit">
              <option value="0">選択しない</option>
              <?php
              $stmt_credit = $db->prepare('SELECT id, name FROM creditcard WHERE user_id = ?');
              $stmt_credit->bind_param('i', $user_id);
              sql_check($stmt_credit, $db);
              $stmt_credit->bind_result($id, $name);
              while ($stmt_credit->fetch()) :
              ?>
                <option value="<?php echo h($id); ?>"><?php echo h($name); ?></option>
              <?php endwhile; ?>
            </select>
          </div>
        </div>

        <div class="u-js__show-switch flex p-form__flex-input sp-change-order" id="qrSelectBox">
          <p class="long-name">スマホ決済種類</p>
          <div class="p-form__item-box">
            <select name="qr">
              <option value="0">選択しない</option>
              <?php
              $stmt_qr = $db->prepare('SELECT id, name FROM qr WHERE user_id = ?');
              $stmt_qr->bind_param('i', $user_id);
              sql_check($stmt_qr, $db);
              $stmt_qr->bind_result($id, $name);
              while ($stmt_qr->fetch()) :
              ?>
                <option value="<?php echo h($id); ?>"><?php echo h($name); ?></option>
              <?php endwhile; ?>
            </select>
          </div>
        </div>

        <div>
          <textarea name="memo" id="" cols="45" rows="5" placeholder="入力収支の詳細"><?php echo h($memo); ?></textarea>
        </div>

        <input class="c-button c-button--bg-blue" name="record_update" type="submit" value="更新">
      </form>
    </section>
    <!-- 収支データ編集 -->

    <section class="p-section p-section__back-home">
      <a href="./index.php" class="c-button c-button--bg-gray">ホームに戻る</a>
    </section>
  </main>

  <footer id="footer" class="l-footer">
    <p>カテほう</p>
  </footer>

  <script src="./js/radio.js"></script>
  <script src="./js/import.js"></script>
  <script src="./js/functions.js"></script>
  <script src="./js/record-edit.js"></script>
</body>

</html>