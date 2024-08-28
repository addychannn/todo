import { ref } from "vue";
export default {
  install: (app, options) => {
    const deferredPrompt = ref(null);
    app.provide("pwaInstallPrompt", deferredPrompt);
    window.addEventListener("beforeinstallprompt", (e) => {
      e.preventDefault();
      deferredPrompt.value = e;
    });
  },
};
