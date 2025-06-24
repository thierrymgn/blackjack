import { describe, it, expect, vi, beforeEach } from 'vitest';
import { render } from '@testing-library/svelte';
import GamePage from '../src/routes/(game)/user/games/[id]/+page.svelte';

// Mock the SvelteKit environment for the game page
vi.mock('$app/stores', () => ({
	page: {
		subscribe: vi.fn((callback) => {
			callback({ params: { id: 'test-game-123' } });
			return () => {};
		})
	}
}));

vi.mock('$app/navigation', () => ({
	goto: vi.fn()
}));

describe('Issue #7: Game page (/user/games/[id]) is empty when turns array is empty', () => {
	let consoleErrors: string[] = [];
	
	beforeEach(() => {
		// Clear localStorage and set up token
		localStorage.clear();
		localStorage.setItem('token', 'fake-token');
		
		// Capture console errors to detect the "ctx[1] is undefined" error
		consoleErrors = [];
		const originalError = console.error;
		console.error = vi.fn((...args) => {
			consoleErrors.push(args.join(' '));
			originalError(...args);
		});
		
		vi.clearAllMocks();
	});

	it('should display wager interface when game has no turns yet', async () => {
		// Mock API response with empty turns array (new game scenario)
		global.fetch = vi.fn().mockResolvedValueOnce({
			status: 200,
			json: () => Promise.resolve({
				id: 'test-game-123',
				status: 'playing',
				turns: [] // Empty turns array - this triggers the bug
			})
		});

		const { container } = render(GamePage);

		// Wait for async loading
		await new Promise(resolve => setTimeout(resolve, 1000));

    expect(global.fetch).toHaveBeenCalled();

		// Test should FAIL: When bug is present, page is empty due to ctx[1] undefined
		// The page should show wager interface even with no turns
		expect(container.textContent).toContain('Wager:'); // SHOULD FAIL - page is empty
		
		// Should have wager input field
		const wagerInput = container.querySelector('input[name="wager"]');
		expect(wagerInput).toBeTruthy(); // SHOULD FAIL - no input shown
		
		// Should have wage button
		const wageButton = container.querySelector('button[type="submit"]');
		expect(wageButton).toBeTruthy(); // SHOULD FAIL - no button shown
		expect(wageButton?.textContent).toContain('Wage'); // SHOULD FAIL
	});

	it('should not be completely empty when turns array is empty', async () => {
		// Mock empty turns response
		global.fetch = vi.fn().mockResolvedValueOnce({
			status: 200,
			json: () => Promise.resolve({
				id: 'test-game-123',
				turns: [] // Empty array causes the bug
			})
		});

		const { container } = render(GamePage);

		// Wait for loading
		await new Promise(resolve => setTimeout(resolve, 1000));

    expect(global.fetch).toHaveBeenCalled();

		// Test should FAIL: When bug is present, page content is empty
		const textContent = container.textContent?.trim() || '';
		expect(textContent.length).toBeGreaterThan(0); // SHOULD FAIL - page is empty
		
		// Should have some meaningful content, not just loading text
		expect(textContent).not.toBe('Loading...'); // SHOULD FAIL
		expect(textContent).not.toBe(''); // SHOULD FAIL - completely empty
	});

	it('should not produce ctx[1] undefined error in console', async () => {
		// Mock empty turns to trigger the error
		global.fetch = vi.fn().mockResolvedValueOnce({
			status: 200,
			json: () => Promise.resolve({
				id: 'test-game-123',
				turns: [] // This causes currentTurn = undefined, leading to ctx[1] error
			})
		});

		render(GamePage);

		// Wait for component to render and any reactive statements to execute
		await new Promise(resolve => setTimeout(resolve, 1000));

    expect(global.fetch).toHaveBeenCalled();

		// Test should FAIL: When bug is present, console shows ctx[1] undefined
		expect(consoleErrors).toHaveLength(0); // SHOULD FAIL - errors present
		
		// Specifically check for the ctx[1] undefined error
		const ctxErrors = consoleErrors.filter(error => 
			error.includes('ctx[1] is undefined') || 
			error.includes('ctx[1]') ||
			error.includes('Cannot read properties of undefined')
		);
		expect(ctxErrors).toHaveLength(0); // SHOULD FAIL - this error occurs
	});

	it('should render without throwing errors when no turns exist', async () => {
		// Mock API call that returns game with empty turns
		global.fetch = vi.fn().mockResolvedValueOnce({
			status: 200,
			json: () => Promise.resolve({
				id: 'test-game-123',
				status: 'playing',
				turns: [] // No turns yet - should not crash component
			})
		});

		let renderError = null;
		
		try {
			const { container } = render(GamePage);
			
			// Wait for async operations
			await new Promise(resolve => setTimeout(resolve, 1000));

      expect(global.fetch).toHaveBeenCalled();
			
			// Component should render successfully
			expect(container).toBeDefined();
		} catch (error) {
			renderError = error;
		}

		// Test should FAIL: When bug is present, component crashes
		expect(renderError).toBeNull(); // SHOULD FAIL - component throws error
	});
});
