/**
 * Shared Constants
 *
 * Constants used across admin and public modules
 */

export const PLUGIN_NAME = 'CWP WordPress Starter Plugin';
export const PLUGIN_VERSION = '1.0.0';
export const PLUGIN_PREFIX = 'cwp';

export const API_NAMESPACE = 'cwp/v1';
export const API_BASE_URL = '/wp-json';

export const DEBUG_MODE = process.env.NODE_ENV === 'development';

export const NONCE_KEY = 'cwp_nonce';
