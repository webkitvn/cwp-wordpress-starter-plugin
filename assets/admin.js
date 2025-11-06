import { d as debugLog, P as PLUGIN_NAME, a as PLUGIN_VERSION } from "./utils-B5RUkea-.js";
function initAdmin() {
  debugLog(`${PLUGIN_NAME} v${PLUGIN_VERSION} - Admin initialized`);
  document.addEventListener("DOMContentLoaded", () => {
    debugLog("Admin DOM loaded");
  });
}
initAdmin();
//# sourceMappingURL=admin.js.map
