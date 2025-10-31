/**
 * Admin JavaScript Entry Point
 *
 * This is the main entry point for admin-facing JavaScript.
 */

import './styles.scss';
import { PLUGIN_NAME, PLUGIN_VERSION } from '@shared/constants';
import { debugLog } from '@shared/utils';

/**
 * Initialize admin functionality
 */
function initAdmin(): void {
  debugLog(`${PLUGIN_NAME} v${PLUGIN_VERSION} - Admin initialized`);

  // Add your admin initialization code here
  document.addEventListener('DOMContentLoaded', () => {
    debugLog('Admin DOM loaded');
  });
}

// Initialize when script loads
initAdmin();
