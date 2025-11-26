/**
 * Shared Utility Functions
 *
 * Common utility functions used across admin and public modules
 */

/**
 * Internal dependencies
 */
import { DEBUG_MODE } from './constants';

/**
 * Debug logging function that only logs in development mode
 *
 * @param message - The message to log
 * @param data    - Optional data to log
 */
export function debugLog(message: string, data?: unknown): void {
	if (DEBUG_MODE) {
		// eslint-disable-next-line no-console
		console.log(`[CWP Debug] ${message}`, data || '');
	}
}

/**
 * Safely parse JSON with error handling
 *
 * @param jsonString - JSON string to parse
 * @param fallback   - Fallback value if parsing fails
 * @return Parsed object or fallback value
 */
export function safeJsonParse<T>(jsonString: string, fallback: T): T {
	try {
		return JSON.parse(jsonString) as T;
	} catch (error) {
		debugLog('JSON parse error', error);
		return fallback;
	}
}

/**
 * Create a debounced function
 *
 * @param func - Function to debounce
 * @param wait - Wait time in milliseconds
 * @return Debounced function
 */
export function debounce<T extends (...args: unknown[]) => unknown>(
	func: T,
	wait: number
): (...args: Parameters<T>) => void {
	let timeout: ReturnType<typeof setTimeout> | null = null;

	return function executedFunction(...args: Parameters<T>) {
		const later = () => {
			timeout = null;
			func(...args);
		};

		if (timeout) {
			clearTimeout(timeout);
		}
		timeout = setTimeout(later, wait);
	};
}

/**
 * Add class to element
 *
 * @param element   - DOM element
 * @param className - Class name to add
 */
export function addClass(element: HTMLElement, className: string): void {
	if (element && !element.classList.contains(className)) {
		element.classList.add(className);
	}
}

/**
 * Remove class from element
 *
 * @param element   - DOM element
 * @param className - Class name to remove
 */
export function removeClass(element: HTMLElement, className: string): void {
	if (element && element.classList.contains(className)) {
		element.classList.remove(className);
	}
}

/**
 * Toggle class on element
 *
 * @param element   - DOM element
 * @param className - Class name to toggle
 */
export function toggleClass(element: HTMLElement, className: string): void {
	if (element) {
		element.classList.toggle(className);
	}
}
