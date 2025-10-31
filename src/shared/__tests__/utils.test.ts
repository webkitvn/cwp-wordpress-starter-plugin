/**
 * Tests for utility functions
 */

import { safeJsonParse, addClass, removeClass, toggleClass } from '../utils';

describe('Utility Functions', () => {
  describe('safeJsonParse', () => {
    it('should parse valid JSON', () => {
      const jsonString = '{"key": "value"}';
      const result = safeJsonParse(jsonString, {});
      expect(result).toEqual({ key: 'value' });
    });

    it('should return fallback for invalid JSON', () => {
      const invalidJson = '{invalid json}';
      const fallback = { default: true };
      const result = safeJsonParse(invalidJson, fallback);
      expect(result).toEqual(fallback);
    });
  });

  describe('DOM utility functions', () => {
    let element: HTMLElement;

    beforeEach(() => {
      element = document.createElement('div');
    });

    describe('addClass', () => {
      it('should add class to element', () => {
        addClass(element, 'test-class');
        expect(element.classList.contains('test-class')).toBe(true);
      });

      it('should not add duplicate class', () => {
        addClass(element, 'test-class');
        addClass(element, 'test-class');
        expect(element.classList.length).toBe(1);
      });
    });

    describe('removeClass', () => {
      it('should remove class from element', () => {
        element.classList.add('test-class');
        removeClass(element, 'test-class');
        expect(element.classList.contains('test-class')).toBe(false);
      });

      it('should handle removing non-existent class', () => {
        removeClass(element, 'non-existent');
        expect(element.classList.length).toBe(0);
      });
    });

    describe('toggleClass', () => {
      it('should toggle class on element', () => {
        toggleClass(element, 'test-class');
        expect(element.classList.contains('test-class')).toBe(true);
        
        toggleClass(element, 'test-class');
        expect(element.classList.contains('test-class')).toBe(false);
      });
    });
  });
});
