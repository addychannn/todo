import { ref } from "vue";
import Router from "@/router";
const devt = ref(false);
const devtAttempts = ref(0);
const _options = ref({});
const j = [
  "(fun",
  "ct",
  "io",
  "n a",
  "() {\n ",
  "de",
  "bu",
  "g",
  "ge",
  "r \n}",
  "",
  ")()",
];

const detectDevTool = (allow = 75) => {
  if (isNaN(+allow)) allow = 75;
  var start = +new Date();
  eval(j.join(""));
  var end = +new Date();
  if (isNaN(start) || isNaN(end) || end - start > allow) {
    if (!!_options.value?.redirect) {
      Router.push(_options.value.redirect);
    }
    devt.value = true;
  }
};
const attachEvents = (evt) => {
  if (window.attachEvent) {
    if (
      document.readyState === "complete" ||
      document.readyState === "interactive"
    ) {
      evt();
      window.attachEvent("onresize", evt);
      window.attachEvent("onmousemove", evt);
      window.attachEvent("onfocus", evt);
      window.attachEvent("onblur", evt);
    } else {
      setTimeout(argument.callee, 0);
    }
  } else {
    window.addEventListener("load", evt);
    window.addEventListener("resize", evt);
    window.addEventListener("mousemove", evt);
    window.addEventListener("focus", evt);
    window.addEventListener("blur", evt);
  }
};

const preventShortcuts = () => {
  window.addEventListener("contextmenu", (e) => {
    e.preventDefault();
    devtAttempts.value++;
  });
  window.addEventListener("keydown", (e) => {
    let combo = [
      e.code == "F12",
      e.ctrlKey && e.code == "KeyI",
      e.ctrlKey && e.code == "KeyU",
      e.ctrlKey && e.shiftKey && e.code == "KeyC",
      e.ctrlKey && e.shiftKey && e.code == "KeyJ",
      e.ctrlKey && e.shiftKey && e.code == "KeyK",
    ];
    if (combo.some((v) => v === true)) {
      e.preventDefault();
      devtAttempts.value++;
    }
  });
};

export default {
  install: async (app, options) => {
    app.provide("devt", devt);
    app.provide("devtAttempts", devtAttempts);
    _options.value = options;
    if (!options.wagmongsubukan) {
      attachEvents(detectDevTool);
      preventShortcuts();
    }
  },
};
