import { d as debugLog, P as PLUGIN_NAME, a as PLUGIN_VERSION } from "./utils-B5RUkea-.js";
function initPublic() {
  debugLog(`${PLUGIN_NAME} v${PLUGIN_VERSION} - Public initialized`);
  document.addEventListener("DOMContentLoaded", () => {
    debugLog("Public DOM loaded");
    initComponents();
  });
}
function initComponents() {
  debugLog("Initializing public components");
}
initPublic();
//# sourceMappingURL=public.js.map
