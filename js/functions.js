/*=====================================================
支出・収入radioボタンで表示するカテゴリー要素を切り替えるイベント
======================================================*/
const onRadioChangeType = (number) => {
  typeChecked[number].checked = true; //選択したradioボタンをcheckedとする

  //支出収入の切り替えが複数回行われたときにすでに1度選択した項目を初期値に戻す
  spendingCategory.selectedIndex = 0; //支出カテゴリーを初期値に戻す
  incomeCategory.selectedIndex = 0; //支出カテゴリーを初期値に戻す
  paymentMethod.selectedIndex = 0; //支払い方法を初期値に戻す
  creditChecked[0].checked = true; //クレジット選択を初期値に戻す
  qrChecked[0].checked = true; //スマホ決済選択を初期値に戻す

  //支出radioボタンが選択されたら
  if (typeChecked[0].checked) {
    paymentMethodBox.classList.add("show"); //支払い方法div要素にshowクラスを付与で表示
    spendingCategoryBox.classList.add("show"); //支出カテゴリーdiv要素にshowクラスを付与で表示
    incomeCategoryBox.classList.remove("show"); //収入カテゴリーdiv要素にshowクラス付与で非表示

  //収入radioボタンが選択されたら
  } else if (typeChecked[1].checked) {
    paymentMethodBox.classList.remove("show");
    spendingCategoryBox.classList.remove("show");
    incomeCategoryBox.classList.add("show");
    creditSelectBox.classList.remove("show");
    qrSelectBox.classList.remove("show");
  }
}

/*====================================
クレジットカードorスマホ決済選択時のイベント
=====================================*/
const hasChildSelect = (methodValue, parentElement, checkedItem) => {
  if (paymentMethod.value === methodValue) {
    parentElement.classList.add("show"); //クレジットorスマホ決済選択div要素にshowクラス付与で表示
  } else if(paymentMethod.value !== methodValue){
    parentElement.classList.remove("show"); //クレジットorスマホ決済選択div要素からshowクラス削除で非表示
    checkedItem[0].checked = true; //選択を初期値に戻す
  }
}

const deleteConfirm = (title) => {
  const confirmText = confirm(title + "を本当に削除しますか？");

  const targetRecord = document.getElementById(target);
  if (!confirmText) {
    targetRecord.setAttribute('href', '');
  }
  
}

if (doneOperateBox !== null) {
  body.classList.add('openedModal');
} else {
  body.classList.remove('openedModal');
}

const onClickOkButton = (param) => { //引数追加
  const url = new URL(window.location.href);
  url.searchParams.delete('dateOperation');
  history.pushState('', '', url.pathname + param); //第三引数をパラメータ前までのURLに引数（パラメータ）を足したものに指定
  location.reload(); 
}

const onClickUpdate = (id, name) => {
  const itemAddElement = document.getElementById('itemAddElement'); //追加formを取得
  const itemEditElement = document.getElementById('itemEditElement'); //更新formを取得
  itemAddElement.classList.add('hide'); //追加formにhideクラスを付与
  itemEditElement.classList.add('show'); //更新formにshowクラスを付与
  const updateId = document.getElementById('updateId'); //type=hiddenのidをセットするinputを取得する
  const updateName = document.getElementById('updateName'); //更新項目入力要素を取得する
  updateId.value = id; //idをセットするinputのvalueに引数idを挿入する
  updateName.value = name; //更新入力inputに引数nameを挿入する
}

//ログアウトボタン押下確認ダイアログイベント
const logoutConfirm = () => {
  const logoutButton = document.getElementById('logoutButton');
  const confirmText = confirm('ログアウトしますか？');
  if (!confirmText) {
    logoutButton.setAttribute('href', '');
  }
}

function showMemo(memo) {
  alert(memo);
}
const onClickMonth = (target) => {
  const windowWidth = window.outerWidth;
  const windowSp = 767;
  if (windowWidth <= windowSp) {
    const targetElement = document.getElementById(target);
    const targetLink = targetElement.getAttribute('href');
    const newHref = targetLink + '#calendar';
    targetElement.setAttribute('href', newHref);
  }
}

if (detailModalBox != null) {
  body.classList.add("openedModal");
} else {
  body.classList.remove("openedModal");
}