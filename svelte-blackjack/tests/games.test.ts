import {cleanup, render, screen, waitFor} from '@testing-library/svelte';
import { describe, it, expect, beforeEach, afterEach, vi } from 'vitest';
import GamesPage from '../src/routes/(game)/user/games/+page.svelte';

// Mock the SvelteKit navigationAdd commentMore actions
vi.mock('$app/navigation', () => ({
    goto: vi.fn()
}));

// Mock fetch for API calls
global.fetch = vi.fn();

describe('Issue #5: Resume button missing in user games', () => {
    beforeEach(() => {
        // Clear localStorage and mocks before each test
        localStorage.clear();
        vi.clearAllMocks();

        // Set up a fake token
        localStorage.setItem('token', 'fake-token');
    });

    afterEach(() => {
        cleanup();
    });
    it('should display a "Resume" button for each game in "Playing"', async () => {
        const mockGames = [
            { id: 1, status: 'playing' },
            { id: 2, status: 'finished' },
            { id: 3, status: 'playing' },
        ];

        // Mock the fetch call to return our test dataAdd commentMore actions
        (global.fetch as any).mockResolvedValueOnce({
            status: 200,
            json: () => Promise.resolve(mockGames)
        });

        // Render the games page
        render(GamesPage);

        // Wait for the component to load the games
        await waitFor(() => {
            expect(screen.getByText('My games')).toBeTruthy();
        });


        const resumeButtons = screen.getAllByText('Resume');
        expect(resumeButtons).toHaveLength(mockGames.filter(game => game.status === 'playing').length);
    });
});