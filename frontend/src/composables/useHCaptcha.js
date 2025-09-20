import { ref } from "vue";

export function useHCaptcha(sitekey) {
  const hcaptchaToken = ref(null);
  let widgetId = null;

  function render(containerId = "hcaptcha-container") {
    if (!window.hcaptcha) return;

    const container = document.getElementById(containerId);
    if (container) container.innerHTML = "";

    widgetId = window.hcaptcha.render(containerId, {
      sitekey,
      callback: (token) => (hcaptchaToken.value = token),
      "expired-callback": () => (hcaptchaToken.value = null),
    });
  }

  function reset() {
    if (window.hcaptcha && widgetId !== null) {
      window.hcaptcha.reset(widgetId);
      hcaptchaToken.value = null;
    }
  }

  return { hcaptchaToken, render, reset };
}
