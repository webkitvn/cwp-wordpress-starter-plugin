/**
 * Jest Setup File
 *
 * This file runs before all tests and sets up the testing environment.
 */

// Mock WordPress globals if needed
global.wp = {
  i18n: {
    __: (text) => text,
    _x: (text) => text,
    _n: (single, plural, number) => (number === 1 ? single : plural),
  },
  element: {
    createElement: jest.fn(),
  },
};

// Add any global test configuration here
