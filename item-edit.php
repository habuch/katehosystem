<?php
require_once('./dbconnect.php');
include_once('./functions.php');
include_once('./session.php');
?>
<?php
if (isset($_GET['editItem']) && $_GET['editItem'] >= 0 && $_GET['editItem'] <= 5) :
  $editItem = $_GET['editItem'];
else :
  header('location: ./index.php');
  exit();
endif;
if ($editItem === '0') :
  $subTitle = "";
elseif ($editItem === '1') :
  $subTitle = "";
elseif ($editItem === '2') :
  $subTitle = "";
elseif ($editItem === '3') :
  $subTitle = "";
elseif ($editItem === '4') :
  $subTitle = "";
endif;
echo $editItem;

$page_title = $subTitle . '編集';
include_once('./header.php');
?>
  <main class="l-main">
    <!-- 操作完了コンテンツ -->
    <?php if ($_GET['dataOperation'] && ($_GET['dataOperation'] === 'delete' || $_GET['dataOperation'] === 'update' || $_GET['dataOperation'] === 'error' || $_GET['dataOperation'] === 'duplicate')) : ?>
      <section class="p-section p-section__full-screen" id="doneOperateBox">
        <div class="p-message-box <?php echo ($_GET['dataOperation'] === 'error' || $_GET['dataOperation'] === 'duplicate') ? 'line-red' : 'line-blue'; ?>">
          <p id="doneText">
            <?php
            if ($_GET['dataOperation'] === 'error') :
              echo '正しく処理されませんでした';
            elseif ($_GET['dataOperation'] === 'delete') :
              echo '削除しました';
            elseif ($_GET['dataOperation'] === 'update') :
              echo '更新しました';
            elseif ($_GET['dataOperation'] === 'duplicate') :
              echo '既に登録済みです';
            endif;
            ?>
          </p>
          <button class="c-button <?php echo ($_GET['dataOperation'] === 'error' || $_GET['dataOperation'] === 'duplicate') ? 'c-button--bg-darkred' : 'c-button--bg-blue'; ?>" onclick="onClickOkButton('?editItem=<?php echo $editItem; ?>');">OK</button>
        </div>
      </section>
    <?php endif; ?>
<!-- //操作完了コンテンツ -->
    <h2 class="c-text c-text__subtitle"><?php echo '【' . $subTitle . '編集】'; ?></h2>
    <?php if (isset($_SESSION['login_times']) && $_SESSION['login_times'] === "first") : ?>
    <section class="p-section p-section__message">
        <div class="p-message-box p-message-box--success">
          <p>
            <?php echo h($nickname); ?>さん、カテほうアプリへようこそ！<br>
            まずは交通手段を登録しましょう。<br>
            交通手段が登録できたら、【他のカテゴリーを編集】から他の項目も登録してみましょう！<br>
            <span>※このメッセージは画面を更新・他ページへ遷移すると消えます</span>
          </p>
        </div>
    </section>

<?php unset($_SESSION['login_times']); ?>


    <?php endif; ?>

    <section class="p-section p-section__category-table">
      <div>
        <table class="p-table p-table--category">
        <?php
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
        if (in_array($table_name, $table_list) !== false) :
          if ($table_name === 'payment_method') :
            $stmt = $db->prepare("SELECT id, name FROM {$table_name} WHERE user_id=? OR user_id=0");
          else :
            $stmt = $db->prepare("SELECT id, name FROM {$table_name} WHERE user_id=?");
          endif;
          $stmt->bind_param('i', $user_id);
        sql_check($stmt, $db);
        $stmt->bind_result($id, $name);
        while ($stmt->fetch()) :
        ?>
          <tr class="p-table__item">
            <td><?php echo h($name); ?></td>
            <td>
              <?php if ($table_name === 'payment_method' && $id <= 3) : ?>
                操作不可
              <?php else : ?>
                <button class='c-button c-button--bg-green edit' onclick="onClickUpdate('<?php echo h($id); ?>', '<?php echo h($name); ?>');"><i class="fa-solid fa-pen"></i></button>
                <a class='c-button c-button--bg-red delete' id="<?php echo 'item' . h($id); ?>" href='./delete.php?id=<?php echo h($id); ?>&from=item-edit&table_number=<?php echo h($editItem); ?>'onclick="deleteConfirm('<?php echo h($name); ?>','<?php echo 'item' . h($id); ?>');"><i class="fa-regular fa-trash-can"></i></a>
              <?php endif; ?>
            </td>
          </tr>
      <?php
        endwhile;
      else :
        header('Location: ./index.php');
      endif;

      ?>

        </table>
      </div>
    </section>

    <section class="p-section p-section__category-edit">

      <form class="p-form p-form--cat-add" id="itemAddElement" action="./item-add.php" method="POST">
        <h2 class="c-text c-text__subtitle">【カテゴリーを追加】</h2>
        <input type="hidden" name="editItem" value="">
        <div class="p-form__vertical-input">
          <p>項目名<span>※スペースのみ不可</span></p>
          <input type="text" class="item-operate-name" id="name" name="name" value="" pattern="\S|\S.*?\S" required>
        </div>
        
        <input class="c-button c-button--bg-blue" type="submit" name="add" value="追加">
      </form>

      <form class="p-form p-form--cat-edit" id="itemEditElement" action="./item-update.php" method="POST">
        <h2 class="c-text c-text__subtitle">【カテゴリーを更新】</h2>
        <input type="hidden" name="id" id="updateId" value="">
        <input type="hidden" name="editItem" value="<?php echo $editItem; ?>">
        <div class="p-form__vertical-input">
          <p>項目名<span>※スペースのみ不可</span></p>
          <input type="text" id="updateName" class="item-operate-name" name="name" pattern="\S|\S.*?\S" required>
        </div>
        <input class="c-button c-button--bg-blue" type="submit" value="更新">
        <a class="c-button c-button--bg-gray" href="">キャンセル</a>
      </form>

    </section>

    <section class="p-section">
      <h2 class="c-text c-text__subtitle">【他のカテゴリーを編集】</h2>
      <div class="p-section__other-catbutton">
      <?php
      $item_name = ['', '', '', '', ''];
      for ($i = 0; $i < count($item_name); $i++) : ?>
        <a class="c-button c-button--bg-lightblue" href="./item-edit.php?editItem=<?php echo $i; ?>"><?php echo $item_name[$i]; ?></a>
      <?php endfor; ?>
      </div>
    </section>

    <section class="p-section p-section__back-home">
      <a href="./index.php" class="c-button c-button--bg-gray">ホームに戻る</a>
    </section>
  </main>

  <footer id="footer" class="l-footer">
    <p>カテほう</p>
  </footer>

  <script src="./js/import.js"></script>
  <script src="./js/functions.js"></script>
</body>

</html>