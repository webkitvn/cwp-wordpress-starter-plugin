/**
 * Public JavaScript Entry Point
 *
 * This is the main entry point for public-facing JavaScript.
 */

/**
 * Internal dependencies
 */
import './styles.scss';
/**
 * External dependencies
 */
import { PLUGIN_NAME, PLUGIN_VERSION } from '@shared/constants';
import { debugLog } from '@shared/utils';

/**
 * Initialize public functionality
 */
function initPublic(): void {
	debugLog(`${PLUGIN_NAME} v${PLUGIN_VERSION} - Public initialized`);

	// Add your public initialization code here
	document.addEventListener('DOMContentLoaded', () => {
		debugLog('Public DOM loaded');

		// Example: Initialize components
		initComponents();
	});
}

/**
 * Initialize public components
 */
function initComponents(): void {
	// Add component initialization logic here
	debugLog('Initializing public components');
}

// Initialize when script loads
initPublic();
