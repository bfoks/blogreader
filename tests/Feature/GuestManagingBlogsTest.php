<?php

namespace Tests\Feature;

use App\Blog;
use App\Platforms\Clients\FakeWP;
use App\Platforms\ClientsProvider;
use App\Platforms\FakeClientsProvider;
use App\Post;
use App\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class GuestManagingBlogsTest extends TestCase
{
    use DatabaseMigrations;

    protected function setUp()
    {
        parent::setUp();

        $this->app->instance(ClientsProvider::class, new FakeClientsProvider());
//        $this->withExceptionHandling();
        $this->withoutExceptionHandling();

    }

    /* Adding a new blog */

    /** @test */
    public function guest_can_see_form_to_add_a_new_blog()
    {
        $this->get(route('blogs.create'))
            ->assertViewIs('blogs.create')
            ->assertStatus(200)
            ->assertSee("Paste blog's URL");
    }

    /** @test */
    public function if_passed_blog_already_exists_on_database_new_blog_entity_is_not_created()
    {
        // ensure that there is no blogs
        $this->assertEquals(0, Blog::all()->count());

        // add example blog for the first time
        $this->post(route('blogs.store', ['url' => 'http://example.com']));
        $this->assertEquals(1, Blog::all()->count());

        // second the same blog submit cannot create a new blog entity
        $this->post(route('blogs.store', ['url' => 'http://example.com']));
        $this->assertEquals(1, Blog::all()->count());

        // third the same blog (but with a slash in url) submit cannot create a new blog entity
        $this->post(route('blogs.store', ['url' => 'http://example.com/']));
        $this->assertEquals(1, Blog::all()->count());

    }

    /** @test */
    public function get_query_params_are_removed_from_submitted_blog_url()
    {
        $this->post(route('blogs.store', ['url' => 'http://example.com?siema']));
        $this->assertEquals(0, Blog::where('url', 'http://example.com?siema')->get()->count());
        $this->assertEquals(1, Blog::where('url', 'http://example.com')->get()->count());

        $this->post(route('blogs.store', ['url' => 'http://example2.com/?siema']));
        $this->assertEquals(0, Blog::where('url', 'http://example2.com/?siema')->get()->count());
        $this->assertEquals(1, Blog::where('url', 'http://example2.com')->get()->count());

        $this->post(route('blogs.store', ['url' => 'http://example3.com/?siema&name=johnny']));
        $this->assertEquals(0, Blog::where('url', 'http://example3.com/?siema&name=johnny')->get()->count());
        $this->assertEquals(1, Blog::where('url', 'http://example3.com')->get()->count());

    }

//
//    /** @test */
//    public function user_can_add_blog_to_own_collection()
//    {
//        $user = factory(User::class)->create();
//
//        $blogData = [
//            'url' => 'http://example.com'
//        ];
//
//        $this->actingAs($user)->post(route('blogs.store'), $blogData);
//
//        $this->assertCount(1, $user->fresh()->blogs);
//        $this->assertDatabaseHas('blogs', $blogData);
//    }
//
//    /** @test */
//    public function additional_slash_at_the_end_of_blog_url_is_trimmed()
//    {
//        $user = factory(User::class)->create();
//
//        $blogData = [
//            'url' => 'http://example.com/'
//        ];
//
//        $this->actingAs($user)->post(route('blogs.store'), $blogData);
//
//        $this->assertEquals('http://example.com', $user->fresh()->blogs->first()->url);
//    }
//
//    /** @test */
//    public function added_blog_is_automatically_initialized_with_first_post()
//    {
//        $user = factory(User::class)->create();
//        $this->signIn($user);
//
//        $blogData = [
//            'url' => 'http://example.com'
//        ];
//
//        $this->post(route('blogs.store'), $blogData);
//
//        tap($user->fresh()->blogs->first()->posts->first(), function ($post) {
//            $this->assertEquals("First post's title", $post->title);
//            $this->assertEquals(70, $post->local_id);
//            $this->assertEquals('https://example.com/first-post/', $post->link);
//        });
//
//    }
//
//    /** @test */
//    public function name_of_blog_is_set_automatically_from_gathered_json_data()
//    {
//        $user = factory(User::class)->create();
//        $this->signIn($user);
//
//        $this->post(route('blogs.store', [
//            'url' => 'https://example.com'
//        ]));
//
//        $this->assertDatabaseHas('blogs', [
//            'name' => 'Example Blog Name',
//            'url' => 'https://example.com',
//        ]);
//    }
//
//    /** @test */
//    public function if_client_can_t_get_blog_name_user_gets_error_and_blog_is_not_added()
//    {
//        $this->app->instance(FakeWP::class, new FakeWP([
//            'findBlogName' => false,
//        ]));
//        $this->app->instance(ClientsProvider::class, new FakeClientsProvider());
//
//        $user = factory(User::class)->create();
//        $this->signIn($user);
//
//        $this->post(route('blogs.store'), [
//            'url' => 'http://example.com'
//        ])
//            ->assertSessionHas('flash_message', 'Nieobsługiwany blog ( ͡° ʖ̯ ͡°)');
//
//        $this->assertEmpty(Blog::all());
//    }
//
//    /** @test */
//    public function if_url_points_to_valid_blog_but_blog_has_not_any_posts_then_blog_is_not_added()
//    {
//        $this->app->instance(FakeWP::class, new FakeWP([
//            'findFirstPost' => false
//        ]));
//        $this->app->instance(ClientsProvider::class, new FakeClientsProvider());
//
//        $user = factory(User::class)->create();
//        $this->signIn($user);
//
//        $this->post(route('blogs.store'), [
//            'url' => 'http://example.com'
//        ])
//            ->assertSessionHas('flash_message', 'Podany blog nie posiada żadnych wpisów.');
//
//        $this->assertEmpty(Blog::all());
//    }
//
//    /** @test */
//    public function after_adding_a_blog_user_is_redirected_to_blogs_posts_index()
//    {
//        $this->signIn();
//
//        $this->post(route('blogs.store'), [
//            'url' => 'http://www.example.com',
//        ])->assertRedirect(route('blogs.posts.index', [Blog::first()]));
//    }
//
//    /** @test */
//    public function added_blog_contains_key_which_define_blog_platform()
//    {
//        $user = factory(User::class)->create();
//        $this->signIn($user);
//
//        $this->post(route('blogs.store', [
//            'url' => 'https://example.com'
//        ]));
//
//        $this->assertDatabaseHas('blogs', [
//            'url' => 'https://example.com',
//            'platform_name' => 'WP_FAKE',
//        ]);
//    }
//
//    /* Index */
//
//    /** @test */
//    public function users_blogs_list_contains_only_blogs_observed_by_him()
//    {
//        $user = factory(User::class)->create();
//        $this->signIn($user);
//
//        $blogA = factory(Blog::class)->create(['name' => 'Name of example blog A', 'url' => 'http://example.com/A']);
//        $blogB = factory(Blog::class)->create(['name' => 'Name of example blog B', 'url' => 'http://example.com/B']);
//        $blogC = factory(Blog::class)->create(['name' => 'Name of example blog C', 'url' => 'http://example.com/C']);
//
//        $user->blogs()->attach($blogA);
//        $user->blogs()->attach($blogB);
//
//        $this->get(route('blogs.index'))
//            ->assertStatus(200)
//            ->assertViewIs('blogs.index')
//            ->assertSeeText($blogA->name)
//            ->assertSeeText($blogA->url)
//            ->assertSeeText($blogB->name)
//            ->assertSeeText($blogB->url)
//            ->assertDontSeeText($blogC->name)
//            ->assertDontSeeText($blogC->url);
//    }
//
//    /** @test */
//    public function user_can_delete_blog_from_own_list_and_is_redirected_to_blogs_index()
//    {
//        $this->withoutExceptionHandling();
//
//        $user = factory(User::class)->create();
//        $this->signIn($user);
//
//        $blog = factory(Blog::class)->create();
//        $user->blogs()->attach($blog);
//
//        $this->assertNotEmpty($user->fresh()->blogs);
//
//        $this->delete(route('blogs.destroy', [$blog]))
//            ->assertRedirect(route('blogs.index'));
//
//        $this->assertEmpty($user->fresh()->blogs);
//    }
//
//    /** @test */
//    public function posts_are_automatically_deleted_with_deleted_blog()
//    {
//        $user = factory(User::class)->create();
//        $this->signIn($user);
//
//        $blog = factory(Blog::class)->create();
//        $user->blogs()->attach($blog);
//
//        factory(Post::class)->create(['blog_id' => $blog->id]);
//        factory(Post::class)->create(['blog_id' => $blog->id]);
//
//        $this->assertNotEmpty($user->blogs->first()->posts);
//
//        $this->delete(route('blogs.destroy', [$blog]));
//
//        $this->assertEmpty(Post::all());
//
//    }
//
//    /* Validation */
//
//    /** @test */
//    public function blogs_name_is_not_required()
//    {
//        $this->signIn();
//
//        $this->post(route('blogs.store'), [
//            'url' => 'http://example.com'
//        ])
//            ->assertSessionMissing('errors');
//
//        $this->assertNotEmpty(Blog::all());
//    }
//
//    // URL
//
//    /** @test */
//    public function blogs_url_is_required()
//    {
//        $this->signIn();
//
//        $this->post(route('blogs.store'), ['name' => 'name'])
//            ->assertSessionHasErrors('url');
//
//        $this->assertEmpty(Blog::all());
//    }
//
//    /** @test */
//    public function blogs_url_is_string()
//    {
//        $this->signIn();
//
//        $this->post(route('blogs.store'), [
//            'name' => 'name',
//            'url' => UploadedFile::fake()->image('fake.jpg')
//        ])
//            ->assertSessionHasErrors('url');
//
//        $this->assertEmpty(Blog::all());
//    }
//
//    /** @test */
//    public function blog_url_is_a_valid_url()
//    {
//        $this->signIn();
//
//        $this->post(route('blogs.store'), [
//            'name' => 'name',
//            'url' => 'Jakiś randomowy string...'
//        ])
//            ->assertSessionHasErrors('url');
//
//        $this->assertEmpty(Blog::all());
//    }


}
