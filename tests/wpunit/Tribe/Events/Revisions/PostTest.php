<?php
namespace Tribe\Events\Revisions;

use Tribe__Events__Main as Main;
use Tribe__Events__Revisions__Post as Post;

class PostTest extends \Codeception\TestCase\WPTestCase {

	public function setUp() {
		// before
		parent::setUp();

		// unhook the tracker to avoid issues during the factory post creations
		tribe( 'tracker' )->unhook();
	}

	public function tearDown() {
		// and re-hook the tracker after the tests
		tribe( 'tracker' )->hook();
// then
		parent::tearDown();
	}

	/**
	 * @test
	 * it should be instantiatable
	 */
	public function it_should_be_instantiatable() {
		$sut = $this->make_instance();

		$this->assertInstanceOf( Post::class, $sut );
	}

	/**
	 * @return Post
	 */
	private function make_instance() {
		$post = $this->factory()->post->create_and_get( [ 'post_status' => 'publish' ] );

		return new Post( $post );
	}

	public function post_types() {
		return [
			'post'      => [ 'post', 'Tribe__Events__Revisions__Post' ],
			'page'      => [ 'page', 'Tribe__Events__Revisions__Post' ],
			'event'     => [ 'tribe_events', 'Tribe__Events__Revisions__Event' ],
			'venue'     => [ 'tribe_venue', 'Tribe__Events__Revisions__Venue' ],
			'organizer' => [ 'tribe_organizer', 'Tribe__Events__Revisions__Organizer' ],
		];
	}

	/**
	 * @test
	 * it should return the right type of revision object
	 *
	 * @dataProvider post_types
	 */
	public function it_should_return_the_right_type_of_revision_object( $post_type, $expected_class ) {
		if ( in_array( $post_type, [ Main::ORGANIZER_POST_TYPE, Main::VENUE_POST_TYPE ] ) ) {
			$this->markTestSkipped( ucfirst( str_replace( 'tribe_', '', $post_type ) ) . ' revisions are not suported yet!' );
		}
		$id          = $this->factory()->post->create( [ 'post_type' => $post_type, 'post_status' => 'publish' ] );
		// re-hook the tracker to have a real world scenario
		tribe( 'tracker' )->hook();
		global $wpdb;
		$wpdb->update( $wpdb->posts, [ 'post_title' => 'Update' ], [ 'ID' => $id ] );
		clean_post_cache( $id );
		$revision_id = wp_save_post_revision( $id );
		$revision    = get_post( $revision_id );

		$this->assertInstanceOf( $expected_class, Post::new_from_post( $revision ) );
	}
}