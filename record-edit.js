//支出が選択されていたら支出カテゴリーを表示
//収入が選択されていたら収入カテゴリーを表示
if (typeChecked[0].checked) { //支出ボタン選択時
    spendingCategoryBox.classList.add('show');
    paymentMethodBox.classList.add('show');
  } else if (typeChecked[1].checked) { //収入ボタン選択時
    incomeCategoryBox.classList.add('show');
  }