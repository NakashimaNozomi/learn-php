"use strict";

// APIのURLが違う場合はこちらを変更
// const APIROOT = "http://127.0.0.1:3100";
const APIROOT = "http://localhost:4001";
let whisperList = [];
let workInProgress = false;

document.addEventListener("DOMContentLoaded", async () => {
  await showWhisper();

  // リスト外のイベント定義
  scrolltoTopEventRegister();
  onSubmitClick();
});

const showWhisper = async () => {
  const url = APIROOT + "/whispers/";
  const whisperList = await fetch(url)
    .then((response) => {
      if (response.status !== 200) {
        throw Error(
          "response status: " +
            response.status +
            ". Message: " +
            response.statusText
        );
      }
      return response.json();
    })
    .catch((error) => {
      console.error(error);
      return [];
    });
  if (whisperList.length > 0) {
    renderWhisper(whisperList);
  } else {
    M.toast({ html: "投稿がないか取得に失敗しました..." });
  }
};

/**
 * つぶやき一覧表示
 * @param array list
 */
const renderWhisper = (list) => {
  const whisperListDOM = document.querySelector(".js-whisterList");
  const template = document.querySelector("#whisperTemplate").content;
  const fragment = document.createDocumentFragment();

  whisperListDOM.innerHTML = "";
  for (const _l of list) {
    const whisperDOM = document.importNode(template, true);
    whisperDOM.querySelector(".js-content").innerHTML = _l.content.replaceAll(
      "\n",
      "<br>"
    );
    whisperDOM.querySelector(".js-editContent").defaultValue = _l.content;
    whisperDOM.querySelector(".js-time").textContent = _l.updated_at;
    whisperDOM.querySelector(".js-time").textContent = _l.updated_at;
    whisperDOM.querySelector(".js-wrapper").dataset.id = _l.id;
    fragment.appendChild(whisperDOM);
  }

  whisperListDOM.appendChild(fragment);

  listEventsRegister();
};

// 以下イベント定義 ///////
/** Topへスクロールイベント */
const scrolltoTopEventRegister = () => {
  document.querySelector(".js-toTop").onclick = () => {
    window.scroll({ top: 0, behavior: "smooth" });
  };
};

let modalInstance;
/** modalイベント登録処理 */
const modalRegister = () => {
  const elems = document.querySelectorAll(".modal");
  modalInstance = M.Modal.init(elems);
};
document.addEventListener("DOMContentLoaded", modalRegister);

const listEventsRegister = () => {
  editEventRegister();
  deleteEventRegister();
};

/**
 * 送信(つぶやくボタン)を押されたら発火する新規登録イベント
 */
const onSubmitClick = () => {
  const submitButton = document.querySelector(".js-submitButton");
  submitButton.addEventListener("click", async (e) => {
    e.preventDefault();
    if (workInProgress) return;
    workInProgress = true;
    const request = {
      content: document.querySelector(".js-wispText").value,
    };

    const newWhisper = await fetch(APIROOT + "/whisper/", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify(request),
    })
      .then((response) => {
        if (response.status !== 201) {
          throw Error(
            "response status: " +
              response.status +
              ". Message: " +
              response.statusText
          );
        }
        return response.json();
      })
      .catch((error) => {
        console.error(error);
        return undefined;
      });

    if (newWhisper === undefined) {
      M.toast({ html: "投稿に失敗しました...", classes: "error" });
      workInProgress = false;
      return;
    }

    document.querySelector(".js-wispText").value = "";
    M.toast({ html: "投稿に成功しました!" });
    showWhisper();

    workInProgress = false;
  });
};

/**
 * 更新関連のイベント定義
 */
const editEventRegister = () => {
  const editButtonDOMList = document.querySelectorAll(".js-edit");
  editButtonDOMList.forEach((elm) =>
    elm.addEventListener("click", editButtonClick)
  );

  const editSubmitButtonDOMList = document.querySelectorAll(
    ".js-editSubmitButton"
  );
  editSubmitButtonDOMList.forEach((elm) =>
    elm.addEventListener("click", editSubmitButtonClick)
  );
};

const editButtonClick = (e) => {
  const wrapperDOM = e.currentTarget.closest(".js-wrapper");
  wrapperDOM.querySelector(".js-detailField").classList.toggle("hide");
  wrapperDOM.querySelector(".js-editField").classList.toggle("hide");
};

const editSubmitButtonClick = async (e) => {
  e.preventDefault();
  if (workInProgress) return;
  workInProgress = true;

  const wrapperDOM = e.currentTarget.closest(".js-wrapper");
  const request = {
    content: wrapperDOM.querySelector(".js-editContent").value,
  };
  const url = APIROOT + "/whisper/" + wrapperDOM.dataset.id;
  const newWhisper = await fetch(url, {
    method: "PUT",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify(request),
  })
    .then((response) => {
      if (response.status !== 200) {
        throw Error(
          "response status: " +
            response.status +
            ". Message: " +
            response.statusText
        );
      }
      return response.json();
    })
    .catch((error) => {
      console.error(error);
      return undefined;
    });
  if (newWhisper === undefined) {
    M.toast({ html: "編集に失敗しました...", classes: "error" });
    workInProgress = false;
    return;
  }

  M.toast({ html: "編集に成功しました!" });
  showWhisper();
  workInProgress = false;
};

/**
 * 削除関連のイベント定義
 */
const deleteEventRegister = () => {
  const deleteConfirmButtonDOMList =
    document.querySelectorAll(".js-deleteConfirm");
  deleteConfirmButtonDOMList.forEach((elm) =>
    elm.addEventListener("click", onDeleteConfirmClick)
  );

  const deleteSubmitButtonDOMList = document.querySelectorAll(".js-delete");
  deleteSubmitButtonDOMList.forEach((elm) =>
    elm.addEventListener("click", onDeleteSubmitClick)
  );
};

const onDeleteConfirmClick = (e) => {
  const wrapperDOM = e.currentTarget.closest(".js-wrapper");
  const id = wrapperDOM.dataset.id;
  console.log(id);
  document.querySelector(".js-delete").dataset.id = id;
};

const onDeleteSubmitClick = async (e) => {
  e.preventDefault();
  if (workInProgress) return;
  workInProgress = true;
  const id = e.currentTarget.dataset.id;
  const url = APIROOT + "/whisper/" + id;
  const result = await fetch(url, {
    method: "DELETE",
    headers: {
      "Content-Type": "application/json",
    },
  })
    .then((response) => {
      if (response.status !== 204) {
        throw Error(
          "response status: " +
            response.status +
            ". Message: " +
            response.statusText
        );
      }
      return true;
    })
    .catch((error) => {
      console.error(error);
      return false;
    });

  modalInstance[0].close();
  if (!result) {
    M.toast({ html: "削除に失敗しました...", classes: "error" });
    workInProgress = false;
    return;
  }

  M.toast({ html: "削除に成功しました!" });
  showWhisper();
  workInProgress = false;
};
