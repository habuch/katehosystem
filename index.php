<?php
//DB接続ファイルの読み込み
require_once('./dbconnect.php');
echo "接続完了";
include_once('./functions.php');
include_once('./session.php');

//日付データ検索
if(isset($_POST['search'])&& isset($_POST['date-search'])):
  $date_from = filter_input(INPUT_POST, 'date_from', FILTER_SANITIZE_SPECIAL_CHARS);
  $date_to = filter_input(INPUT_POST, 'date_to', FILTER_SANITIZE_SPECIAL_CHARS);
  $searchDateFrom = $date_from;
  $searchDateTo = $date_to;

elseif(isset($_POST['search']) && isset($_POST['all-search'])):
  $searchDateFrom = '';
  $searchDateTo = date('Y-m-d');
  //検索が押されたときの処理
else:
  $searchDateFrom = date('Y-m-01');
  $searchDateTo = date('Y-m-d');
endif;

$page_title = "ホーム";
include_once('./header.php')

?>


  <main class="l-main">
    <!-- 操作完了コンテンツ -->
  <?php if (isset($_GET['dataOperation']) && ($_GET['dataOperation'] === 'delete'|| $_GET['dataOperation'] === 'update' || $_GET['dataOperation'] === 'error'|| $_GET['dataOperation'] === 'numberError')) : ?>
    
    <section class="p-section p-section__full-screen" id="doneOperateBox">
    <div class="p-message-box <?php echo ($_GET['dataOperation'] === 'error'|| $_GET['dataOperation'] === 'numberError') ? 'line-red' : 'line-blue'; ?>">
    <p id="doneText">
    <?php
    if ($_GET['dataOperation'] === 'error') :
      echo '正しく処理されませんでした';
    elseif ($_GET['dataOperation'] === 'delete') :
      echo '削除しました';
    elseif ($_GET['dataOperation'] === 'update') :
      echo '更新しました';
    elseif ($_GET['dataOperation'] === 'numberError') :
      echo '負の金額は入力できません';
    endif;
    ?>
  </p>
      <button class="c-button <?php echo ($_GET['dataOperation'] === 'error'|| $_GET['dataOperation'] === 'numberError') ? 'c-button--bg-darkred' : 'c-button--bg-blue'; ?>" onclick="onClickOkButton('');">OK</button>
    </div>
    </section>

  <?php endif; ?>
    <!-- //操作完了コンテンツ -->
<!-- カレンダー日別収支詳細表示 76行目付近に以下を追加-->
<?php if (isset($_GET['detail'])) : ?>
  <section class="p-section p-section__full-screen" id="detailModalBox">
    <div class="p-detail-box">

      <!--タイトル出力-->
      <p class="p-detail-box__title">
        <?php
        $param_date = $_GET['detail'];
        $title_date = date('Y年n月j日', strtotime($param_date));
        echo $title_date;
        ?>
      </p>

      <!--詳細データ抽出-->
      <?php
      $sql = 'SELECT records.id, records.date, records.title, records.amount, spending_category.name, income_category.name, records.type, payment_method.name, creditcard.name, qr.name, records.memo
      FROM records 
      LEFT JOIN spending_category ON records.spending_category = spending_category.id
      LEFT JOIN income_category ON records.income_category = income_category.id
      LEFT JOIN payment_method ON records.payment_method = payment_method.id
      LEFT JOIN creditcard ON records.credit = creditcard.id
      LEFT JOIN qr ON records.qr = qr.id
      WHERE records.date=? AND records.user_id = ?';
      $stmt = $db->prepare($sql);
      $stmt->bind_param('si', $param_date, $user_id);
      sql_check($stmt, $db);
      $stmt->bind_result($id, $date, $title, $amount, $spending_cat, $income_cat, $type, $payment_method, $credit, $qr, $memo);
      while ($stmt->fetch()) :
      ?>

        <div class="p-detail-box__content">
          <div class="outline">
            <p>
              <?php echo $title; ?>
              <span>
                <?php
                if ($type === 0 && $spending_cat !== null) {
                  echo '(' . $spending_cat . ')';
                } elseif ($type === 1 && $income_cat !== null) {
                  echo '(' . $income_cat . ')';
                } else {
                  echo '';
                }
                ?>
              </span>
            </p>
            <p class="<?php echo $type === 0 ? 'text-red' : 'text-blue'; ?>"><?php echo $type === 0 ? '' . number_format($amount) : '' . number_format($amount); ?></p>
          </div>
          <?php if ($type === 0) : ?>
            <p class="detail">
              <?php
              echo ($payment_method != '') ? $payment_method : '';
              if ($credit !== null || $qr !== null) {
                echo '/' . $credit . $qr;
              }
              ?>
            </p>
          <?php endif; ?>

          <div class="p-detail-box__editbutton">
            <form action="./record-edit.php" method="POST">
              <input type="hidden" name="record_id" value="<?php echo h($id); ?>">
              <input type="submit" class='c-button c-button--bg-green edit fas' id="" value="編集">
            </form>
            <a class='c-button c-button--bg-red delete' id="delete<?php echo h($id); ?>" href='./delete.php?id=<?php echo h($id); ?>&from=index' onclick="deleteConfirm('<?php echo h($title); ?>','delete<?php echo h($id); ?>');"> 削除 </a>
          </div>
        </div>
      <?php endwhile; ?>

      <?php
      if (isset($_GET['ym'])) :
        $ym = $_GET['ym'];
      endif;
      if (isset($_GET['page_id'])) :
        $page_id = $_GET['page_id'];
      endif;

      if (isset($_GET['ym']) && isset($_GET['page_id'])) :
        $detail_ok_link = './index.php?ym=' . $ym . '&page_id=' . $page_id . '#calendar';
      elseif (isset($_GET['ym'])) :
        $detail_ok_link = './index.php?ym=' . $ym . '#calendar';
      elseif (isset($_GET['page_id'])) :
        $detail_ok_link = './index.php?page_id=' . $page_id . '#calendar';
      else : 
        $detail_ok_link = './index.php';
      endif;
      ?>
      <a class="c-button c-button--bg-blue" href="<?php echo $detail_ok_link; ?>">OK</a>
    </div>
  </section>
<?php endif; ?>
<!-- カレンダー日別収支詳細表示 -->
    <!-- 収支データ入力 -->

  <div class="u-flex-box records-input-calendar">

    <section class="p-section p-section__records-input">

      <form class="p-form p-form--input-record" name="recordInput" action="./record-create.php" method="POST">
        <input type="hidden" name="input_time" id="input_time" value="<?php echo date("Y/m/d-H:i:s"); ?>">
        <div class="p-form__flex-input">
          <p>日付</p>
          <label for="date"><input type="date" name="date" id="date" value="<?php echo date("Y-m-d"); ?>" required></label>
        </div>

        <div class="p-form__flex-input">
          <p>科目</p>
          <input type="text" name="title" id="title" maxlength="15" required>
        </div>

        <div class="p-form__flex-input">
          <p>時間</p>
          <input type="number" name="amount" id="amount" step="1" maxlength="7" required>
        </div>

        <div class="p-form__flex-input type">
          <input id="spending" type="radio" name="type" value="0" onchange="onRadioChangeType(0);" required>
          <label for="spending">予定 </label>
          <input type="radio" name="type" id="income" value="1" onchange="onRadioChangeType(1);">
          <label for="income">報告 </label>
        </div>

        <div class="u-js__show-switch flex p-form__flex-input sp-change-order" id="spendingCategoryBox">
          <p class="long-name">交通手段</p>
          <select name="spending_category" id="spendingCategory">
            <option value="0"> </option>
            <?php
            $stmt_spendingcat = $db->prepare('SELECT id, name FROM spending_category WHERE user_id = ?');
            $stmt_spendingcat->bind_param('i', $user_id);
            sql_check($stmt_spendingcat, $db);
            $stmt_spendingcat->bind_result($id, $name);
            while ($stmt_spendingcat->fetch()) :
            ?>
              <option value="<?php echo h($id); ?>"><?php echo h($name); ?></option>
            <?php endwhile; ?>
          </select>
          <a class="c-button c-button--bg-gray" href="./item-edit.php?editItem=0">編集</a>
        </div>

        <div class="u-js__show-switch flex p-form__flex-input sp-change-order" id="incomeCategoryBox">
          <p class="long-name">交通手段</p>
          <select name="income_category" id="incomeCategory">
            <option value="0">選択してください</option>
            <?php
            $stmt_incomecat = $db->prepare('SELECT id, name FROM income_category WHERE user_id = ?');
            $stmt_incomecat->bind_param('i', $user_id);
            sql_check($stmt_incomecat, $db);
            $stmt_incomecat->bind_result($id, $name);
            while ($stmt_incomecat->fetch()) :
            ?>
              <option value="<?php echo h($id); ?>"><?php echo h($name); ?></option>
            <?php endwhile; ?>
          </select>
          <a class="c-button c-button--bg-gray" href="./item-edit.php?editItem=1">編集</a>
        </div>

        <div id="paymentMethodBox" class="u-js__show-switch flex p-form__flex-input sp-change-order">
          <p class="long-name">支払い方法</p>
          <select name="payment_method" id="paymentMethod" onchange="hasChildSelect('2', creditSelectBox, qrChecked);hasChildSelect('3', qrSelectBox, creditChecked);">
            <option value="0">選択してください</option>
            
            <?php
            $fixedPaymentMethod = ['現金', 'クレジット', 'スマホ決済'];
            $fixedPaymentMethod_id = ['', 'radioCredit', 'radioQr'];
            for ($i = 0; $i < 3; $i++) : ?>
              <option value="<?php echo $i; ?>" id="<?php echo $fixedPaymentMethod_id[$i]; ?>" ; ?><?php echo $fixedPaymentMethod[$i]; ?></option>
            <?php endfor; ?>

            <?php
            $stmt_paymethod = $db->prepare('SELECT id, name FROM payment_method WHERE id>3 AND user_id = ?');
            $stmt_paymethod->bind_param('i', $user_id);
            sql_check($stmt_paymethod, $db);
            $stmt_paymethod->bind_result($id, $name);
            while ($stmt_paymethod->fetch()) : ?>
              <option value="<?php echo h($id); ?>"><?php echo h($name); ?></option>
            <?php endwhile; ?>
          </select>
          <a class="c-button c-button--bg-gray" href="./item-edit.php?editItem=2">編集</a>
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
          <a class="c-button c-button--bg-gray" href="./item-edit.php?editItem=3">編集</a>
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
          <a class="c-button c-button--bg-gray" href="./item-edit.php?editItem=4">編集</a>
        </div>

        <div>
          <textarea name="memo" id="" cols="45" rows="5" placeholder="報告内容"></textarea>
        </div>

        <input class="c-button c-button--bg-blue" name="record_create" type="submit" value="登録">
      </form>
    </section>

    <section class="p-section p-section__calendar" id="calendar">
    <?php
    //パラメータ処理
    if(isset($_GET['ym'])){
      $ym = $_GET['ym'];
    }else{
      $ym = date('Y-m');
    }

    $base_date = strtotime($ym); //パラメータもしくは現在の年月のタイムスタンプ
    $prev = date('Y-m', strtotime('-1 month', $base_date)); //前月取得
    $next = date('Y-m', strtotime('+1 month', $base_date)); //次月取得
    $calendar_title = date('Y年n月', $base_date); //カレンダータイトル
    ?>
    <p>
      <a id="js-prevMonth" href="?ym=<?php echo $prev; ?>" onclick="onClickMonth('js-prevMonth');">＜</a>
      <?php echo $calendar_title; ?>
      <a id="js-nextMonth" href="?ym=<?php echo $next; ?>" onclick="onClickMonth('js-nextMonth');">＞</a>
      <a id="js-nowMonth" href="?ym=<?php echo date('Y-m'); ?>" onclick="onClickMonth('js-nowMonth');">今月</a>
    </p>
    <div class="p-calendar__sum">
      <?php
      $sql = 'SELECT (SELECT SUM(amount) FROM records WHERE type=0 AND date LIKE ? AND user_id=?)AS spending_sum, (SELECT SUM(amount) FROM records WHERE type=1 AND date LIKE ? AND user_id=?)AS income_sum FROM records WHERE user_id=? LIMIT 1';
      $stmt = $db->prepare($sql);
      $ym_param = $ym . '%';
      $stmt->bind_param('sisii', $ym_param, $user_id, $ym_param, $user_id, $user_id);
      sql_check($stmt, $db);
      $stmt->bind_result($month_spending_sum, $month_income_sum);
      $stmt->fetch();
      ?>
      
      <p>
        合計時間<span class="pc_only">：</span><br class="sp_only">
        <span class="text-blue"><?php echo number_format($month_income_sum); ?>分</span>
      </p>
      
      <?php $stmt->close(); ?>
    </div>

  <table class="p-calendar">
    <tr>
      <th>日</th>
      <th>月</th>
      <th>火</th>
      <th>水</th>
      <th>木</th>
      <th>金</th>
      <th>土</th>
    </tr>

    <?php
//表示中の月の日数を取得
$day_count = date('t', $base_date);

//曜日取得（日曜日なら0、月曜日なら1・・・土曜日なら6が入る）
$youbi = date('w', $base_date);

//カレンダー配列の初期化
$weeks = [];
$week = '';

//初週の空セルの作成（$youbiに格納されている数だけ空のtdタグを$weekに追加）
$week .= str_repeat('<td></td>', $youbi);

//日毎のデータ抽出(1日から表示月の末尾まで繰り返す)
for ($day = 1; $day <= $day_count; $day++, $youbi++) {

  //YYYY-mm-dd形式の文字列生成（セルの日付とリンクパラメータ、SQLのbind_paramに使用）
  if ($day < 10) :
    $date = $ym . '-' . '0' . $day;
  else :
    $date = $ym . '-' . $day;
  endif;

  //詳細表示のリンク生成
  if (isset($_GET['ym'])) :
    $detail_url = $_SERVER['REQUEST_URI'] . '&detail=' . $date;
  else :
    $detail_url = $_SERVER['REQUEST_URI'] . '?detail=' . $date;
  endif;

  //データ抽出
  $sql = 'SELECT (SELECT SUM(amount) FROM records WHERE type=0 AND user_id=? AND date=?)AS spending_sum, (SELECT SUM(amount) FROM records WHERE type=1 AND user_id=? AND date=?)AS income_sum FROM records WHERE user_id=? AND date=? LIMIT 1';
  $stmt = $db->prepare($sql);
  $stmt->bind_param('isisis', $user_id, $date, $user_id, $date, $user_id, $date);
  $stmt->execute();
  $stmt->bind_result($spending_sum, $income_sum);
  $stmt->fetch();

  //セル内HTML変数格納
  $cel_link = '<a href="' . $detail_url . '">';
  $cel_spending = '<span class="text-red">' . number_format($spending_sum) . '</span>';
  $cel_income = '<span class="text-blue">' . number_format($income_sum) . '</span>';
  $close_a = '</a>';

  //日付セルの中身生成
  if ($spending_sum > 0 && $income_sum > 0) :
    //支出と収入の両方が存在するとき
    $cel_content = $cel_link . $cel_spending . $cel_income . $close_a;
  elseif ($spending_sum > 0 && ($income_sum == null || $income_sum == 0)) :
    //支出のみ存在するとき
    $cel_content = $cel_link . $cel_spending . $close_a;
  elseif (($spending_sum == null || $spending_sum == 0) && $income_sum > 0) :
    //収入のみ存在するとき
    $cel_content = $cel_link . $cel_income . $close_a;
  else :
    //どちらも存在しないとき
    $cel_content = '';
  endif;

  //$weekにセルを追加
  $week .= '<td>' . $day . '<br>' . $cel_content . '</td>';

  //抽出結果の初期化
  $spending_sum = null;
  $income_sum = null;
  $stmt->close();

  //週末、月末の処理
  if ($youbi % 7 == 6 || $day == $day_count) :
    $weeks[] = '<tr>' . $week . '</tr>';
    $week = '';
  endif;
}

// 出力
foreach ($weeks as $week) {
  echo $week;
}
?>
  </table>
</section>

    </div>
    <!-- 収支データ入力 -->

    <section class="p-section p-section__records-output">
      <h3>報告一覧</h3>

      <!-- 月検索 -->
      <form class="p-form p-form--center p-form--date-search" action="" method="POST">
        <input type="hidden" name="search">
        <label for="date-search">
          <input type="date" id="dateFrom" name="date_from" value="<?php echo $searchDateFrom; ?>">
          〜
          <input type="date" id="dateTo" name="date_to" value="<?php echo $searchDateTo; ?>">
        </label>
        <input class="c-button c-button--bg-gray c-button--search" type="submit" name="date-search" value="検索">
        <input class="c-button c-button--bg-gray" type="submit" name="all-search" value="全データ表示">
      </form>
      <!-- //月検索 -->

      <div class="pc_only">

        <table class="p-table p-table--record-output" id="table">



          <!-- タイトル行 -->
          <tr class="p-table__head">
            <th>日付</th>
            <th>科目</th>
            <th>時間</th>
            <th>報告内容</th>
            <th>操作</th>
          </tr>


          <?php
          $sql_dataoutput = 'SELECT records.id, records.date, records.title, records.amount, 
          spending_category.name, income_category.name, records.type, 
          payment_method.name, creditcard.name, qr.name, records.memo, records.input_time
          FROM records 
          LEFT JOIN spending_category ON records.spending_category = spending_category.id
          LEFT JOIN income_category ON records.income_category = income_category.id
          LEFT JOIN payment_method ON records.payment_method = payment_method.id
          LEFT JOIN creditcard ON records.credit = creditcard.id
          LEFT JOIN qr ON records.qr = qr.id
          WHERE records.date >=? AND records.date <=? AND records.user_id = ?
          ORDER BY date DESC, input_time DESC';
          $stmt_dataoutput = $db->prepare($sql_dataoutput);
          $stmt_dataoutput->bind_param('ssi', $searchDateFrom, $searchDateTo, $user_id);
          sql_check($stmt_dataoutput, $db);
          
          $stmt_dataoutput->bind_result(
            $id,
            $date,
            $title,
            $amount,
            $spending_category,
            $income_category,
            $type,
            $paymentmethod,
            $credit,
            $qr,
            $memo,
            $input_time
          );
          
          while ($stmt_dataoutput->fetch()) : ?>
          
          <tr class="p-table__item item<?php echo h($id); ?> <?php echo $memo !== '' ? 'hasmemo' : ''; ?>">
            <td><?php echo date('Y/m/d', strtotime($date)); ?></td>
            <td>
              <?php echo h($title); ?>
              <span>
                <?php
                if ($type === 0 && $spending_category !== null) :
                  echo '(' . h($spending_category) . ')';
                elseif ($type === 1 && $income_category !== null) :
                  echo '(' . $income_category . ')';
                else :
                  echo '';
                endif;
                ?>
            <i class="fa-regular fa-message" onclick="showMemo('<?php echo h($memo); ?>');"></i> </span>
            </td>
            <td>
              <?php echo $type === 1 ? '' . number_format(h($amount)) : ''; ?>
            </td>
       
            <td>
              <?php echo $paymentmethod === "クレジット" ? h($credit) : '' ?>
              <?php echo $paymentmethod === "スマホ決済" ? h($qr) : '' ?>
            </td>
            <td>
              <form action="./record-edit.php" method="POST">
                <input type="hidden" name="record_id" value="<?php echo h($id); ?>">
                <input type="submit" class='c-button c-button--bg-green edit fas' id="" value="">
              </form>
              <a class='c-button c-button--bg-red delete' id="delete<?php echo h($id); ?>" href='./delete.php?id=<?php echo h($id); ?>&from=index' onclick="deleteConfirm('<?php echo h($title); ?>', 'delete<?php echo h($id); ?>');">
                <i class="fa-regular fa-trash-can"></i>
              </a>
            </td>
          </tr>
        
        <?php endwhile;?>

        </table>

      </div>

      <div class="sp_only">
        <?php
        $stmt_dataoutput = $db->prepare($sql_dataoutput);
        $stmt_dataoutput->bind_param('ssi', $searchDateFrom, $searchDateTo, $user_id);
        sql_check($stmt_dataoutput, $db);
        $stmt_dataoutput->bind_result(
          $id,
          $date,
          $title,
          $amount,
          $spending_category,
          $income_category,
          $type,
          $paymentmethod,
          $credit,
          $qr,
          $memo,
          $input_time
        );

        while ($stmt_dataoutput->fetch()) : ?>
        

        <!-- 収支データ出力 -->
        <div class="p-sp-data-box item<?php echo h($id); ?>">
        <div class="u-flex-box p-sp-data-box__overview <?php echo $memo !== '' ? 'hasmemo' : ''; ?>">
          <p> <?php echo h($title); ?>
            <span>
              <?php
              if ($type === 0 && $spending_category !== null) {
                echo '(' . h($spending_category) . ')';
              } else if ($type === 1 && $income_category !== null) {
                echo '(' . h($income_category) . ')';
              } else {
                echo "";
              }
              ?>
              <i class="fa-regular fa-message" onclick="showMemo('<?php echo h($memo); ?>');"></i> </span>
          </p>
          <p class="<?php echo $type === 0 ? 'text-red' : 'text-blue' ?>">
            <?php echo h($type) === "0" ? '-¥' . number_format($amount) : ''; ?>
            <?php echo h($type) === "1" ? '+¥' . number_format($amount) : ''; ?>
          </p>
          </div>
          <div class="p-sp-data-box__detail">
            <p><?php echo date('Y/m/d', strtotime($date)); ?></p>
            <p>
              <?php
              //支払い方法の出力
              if ($type === 0 && $paymentmethod !== null) {
                echo '支払い方法：' . h($paymentmethod);
              } else if ($type === 1) {
                echo "";
              } else {
                echo "";
              }
              ?>
            </p>

            <!--静的コーディングスタイルから少し変更-->
            <?php if ($paymentmethod === "クレジット" || $paymentmethod === "スマホ決済") : ?>
              <p>
                <?php
                //クレジット、スマホ決済の詳細出力
                if ($paymentmethod === "クレジット") {
                  if ($credit !== null) {
                    echo 'カード種類：' . h($credit);
                  } else {
                    echo "";
                  }
                } else if ($paymentmethod === "スマホ決済") {
                  if ($qr !== null) {
                    echo 'スマホ決済種類：' . h($qr);
                  } else {
                    echo "";
                  }
                }
                ?>
              </p>
            <?php endif; ?>

          </div>
          <div class="u-flex-box p-sp-data-box__button">
            <form action="./record-edit.php" method="post">
              <input type="hidden" name="record_id" value="<?php echo h($id); ?>">
              <input type="submit" class="c-button c-button--bg-green edit" id="" value="編 集">
            </form>
            <a class="c-button c-button--bg-red delete" id="delete<?php echo h($id); ?>sp" href='./delete.php?id=<?php echo h($id); ?>&from=index' onclick="deleteConfirm('<?php echo h($title); ?>', 'delete<?php echo h($id); ?>sp');">削 除</a>
          </div>
        </div>
        <!-- //収支データ出力 -->

        <?php endwhile;?>

      </div>
    </section>
  </main>

  <footer id="footer" class="l-footer">
    <p>カテほう</p>
  </footer>

  <div class="p-back-top" id="page_top">
    <a href="#page-top"></a>
  </div>

  <script>
    //トップへ戻るボタン
    $(function() {
      let appear = false;
      const pagetop = $('#page_top');
      $(window).scroll(function() {
        if ($(this).scrollTop() > 300) { //1000pxスクロールしたら
          if (appear == false) {
            appear = true;
            pagetop.stop().animate({
              'bottom': '3.6rem' //下から3.6remの位置に
            }, 300); //0.3秒かけて現れる
          }
        } else {
          if (appear) {
            appear = false;
            pagetop.stop().animate({
              'bottom': '-5rem' //下から-5remの位置に
            }, 300); //0.3秒かけて隠れる
          }
        }
      });
      pagetop.click(function() {
        $('body, html').animate({
          scrollTop: 0
        }, 500); //0.5秒かけてトップへ戻る
        return false;
      });
    });
  </script>

  <script src="./js/radio.js"></script>
  <script src="./js/import.js"></script>
  <script src="./js/functions.js"></script>

</body>

</html>