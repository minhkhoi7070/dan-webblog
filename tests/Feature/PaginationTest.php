<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaginationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Case 1: 20 total results, 10 per page -> Page 1 shows 1-10 of 20, Page 2 shows 11-20 of 20, only 2 pages.
     */
    public function test_case_1_twenty_results_ten_per_page(): void
    {
        Post::factory()->count(20)->published()->create();

        // Page 1
        $response1 = $this->get(route('posts.index'));
        $response1->assertOk();
        $text1 = preg_replace('/\s+/', ' ', strip_tags($response1->getContent()));
        $this->assertStringContainsString('Showing 1 to 10 of 20 results', $text1);
        $response1->assertSee('page=2');
        $response1->assertDontSee('page=3');

        // Page 2
        $response2 = $this->get(route('posts.index', ['page' => 2]));
        $response2->assertOk();
        $text2 = preg_replace('/\s+/', ' ', strip_tags($response2->getContent()));
        $this->assertStringContainsString('Showing 11 to 20 of 20 results', $text2);
        $response2->assertDontSee('page=3');
    }

    /**
     * Case 2: 18 total results, 10 per page -> Page 1 shows 1-10 of 18, Page 2 shows 11-18 of 18.
     */
    public function test_case_2_eighteen_results_ten_per_page(): void
    {
        Post::factory()->count(18)->published()->create();

        // Page 1
        $response1 = $this->get(route('posts.index'));
        $response1->assertOk();
        $text1 = preg_replace('/\s+/', ' ', strip_tags($response1->getContent()));
        $this->assertStringContainsString('Showing 1 to 10 of 18 results', $text1);

        // Page 2
        $response2 = $this->get(route('posts.index', ['page' => 2]));
        $response2->assertOk();
        $text2 = preg_replace('/\s+/', ' ', strip_tags($response2->getContent()));
        $this->assertStringContainsString('Showing 11 to 18 of 18 results', $text2);
    }

    /**
     * Case 3: 10 total results, 10 per page -> 1 page only, pagination navigation hidden.
     */
    public function test_case_3_ten_results_ten_per_page(): void
    {
        Post::factory()->count(10)->published()->create();

        $response = $this->get(route('posts.index'));
        $response->assertOk();
        $response->assertDontSee('aria-label="Pagination Navigation"', false);
    }

    /**
     * Case 4: 9 total results, 10 per page -> 1 page only.
     */
    public function test_case_4_nine_results_ten_per_page(): void
    {
        Post::factory()->count(9)->published()->create();

        $response = $this->get(route('posts.index'));
        $response->assertOk();
        $response->assertDontSee('aria-label="Pagination Navigation"', false);
    }

    /**
     * Case 5: Search query + pagination preserves ?q=laravel&page=2
     */
    public function test_case_5_search_query_preserves_pagination_parameter(): void
    {
        Post::factory()->count(15)->published()->create([
            'title' => fn () => 'Laravel News '.fake()->unique()->sentence(),
        ]);

        $response = $this->get(route('posts.index', ['q' => 'Laravel News']));
        $response->assertOk();
        $response->assertSee('q=Laravel%20News', false);
        $response->assertSee('page=2');
    }

    /**
     * Case 6: Category filter + pagination preserves category query parameter.
     */
    public function test_case_6_category_filter_preserves_pagination_parameter(): void
    {
        $category = Category::factory()->create(['slug' => 'tech-category']);
        Post::factory()->count(15)->published()->create(['category_id' => $category->id]);

        $response = $this->get(route('posts.index', ['category' => 'tech-category']));
        $response->assertOk();
        $response->assertSee('category=tech-category');
        $response->assertSee('page=2');
    }

    /**
     * Case 7: Invalid page (e.g. ?page=999) redirects safely to last valid page.
     */
    public function test_case_7_invalid_page_redirects_safely(): void
    {
        Post::factory()->count(20)->published()->create();

        // 20 items = 2 pages. Page 999 should redirect to Page 2.
        $response = $this->get(route('posts.index', ['page' => 999]));
        $response->assertRedirect(route('posts.index', ['page' => 2]));

        // Page 0 should redirect to Page 1
        $response0 = $this->get(route('posts.index', ['page' => 0]));
        $response0->assertRedirect(route('posts.index', ['page' => 1]));
    }
}
