<?php

namespace Tribe\Events\ORM\Events;

use Tribe\Events\Test\Factories\Event;
use Tribe\Events\Test\Factories\Organizer;
use Tribe\Events\Test\Factories\Venue;

class FetchOtherFiltersTest extends \Codeception\TestCase\WPTestCase {

	public function setUp() {
		// before
		parent::setUp();

		// your set up methods here
		$this->factory()->event     = new Event();
		$this->factory()->organizer = new Organizer();
		$this->factory()->venue     = new Venue();
	}

	/**
	 * It should allow getting events by featured status
	 *
	 * @test
	 */
	public function should_allow_getting_events_by_featured_status() {
		$matching     = $this->factory()->event->create_many( 2, [ 'meta_input' => [ Tribe__Events__Featured_Events::FEATURED_EVENT_KEY => 'yes' ] ] );
		$not_matching = $this->factory()->event->create_many( 3 );

		$this->assertEqualSets( $matching, tribe_events()->where( 'featured', true )->get_ids() );
		$this->assertEqualSets( $not_matching, tribe_events()->where( 'featured', false )->get_ids() );
		$this->assertCount( 5, tribe_events()->get_ids() );
	}

	/**
	 * It should allow getting events by hidden status
	 *
	 * @test
	 */
	public function should_allow_getting_events_by_hidden_status() {
		$matching     = $this->factory()->event->create_many( 2, [ 'meta_input' => [ '_EventHideFromUpcoming' => 'yes' ] ] );
		$not_matching = $this->factory()->event->create_many( 3 );

		$this->assertEqualSets( $matching, tribe_events()->where( 'hidden', true )->get_ids() );
		$this->assertEqualSets( $not_matching, tribe_events()->where( 'hidden', false )->get_ids() );
		$this->assertCount( 5, tribe_events()->get_ids() );
	}

	/**
	 * It should allow getting events by sticky status
	 *
	 * @test
	 */
	public function should_allow_getting_events_by_sticky_status() {
		$matching     = $this->factory()->event->create_many( 2, [ 'menu_order' => - 1 ] );
		$not_matching = $this->factory()->event->create_many( 3 );

		$this->assertEqualSets( $matching, tribe_events()->where( 'sticky', true )->get_ids() );
		$this->assertEqualSets( $not_matching, tribe_events()->where( 'sticky', false )->get_ids() );
		$this->assertCount( 5, tribe_events()->get_ids() );
	}

	/**
	 * It should allow getting events by linked post
	 *
	 * @test
	 */
	public function should_allow_getting_events_by_linked_post() {
		$linked_post  = $this->factory()->event->create();
		$linked_post2 = $this->factory()->event->create();

		$matching      = $this->factory()->event->create_many( 2, [ '_EventCustomRelatedID' => $linked_post ] );
		$matching2     = $this->factory()->event->create_many( 2, [ '_EventCustomRelatedID' => $linked_post2 ] );
		$not_matching  = $this->factory()->event->create_many( 3 );

		$this->assertEqualSets( $matching, tribe_events()->where( 'linked_post', '_EventCustomRelatedID', $linked_post )->get_ids() );
		$this->assertEqualSets( $matching, tribe_events()->where( 'linked_post', '_EventCustomRelatedID', get_post( $linked_post ) )->get_ids() );
		$this->assertEqualSets( array_merge( $matching, $matching2 ), tribe_events()->where( 'linked_post', '_EventCustomRelatedID', [ get_post( $linked_post ), $linked_post2 ] )->get_ids() );
		$this->assertCount( 7, tribe_events()->get_ids() );
	}

	/**
	 * It should allow getting events by organizer
	 *
	 * @test
	 */
	public function should_allow_getting_events_by_organizer() {
		$linked_post  = $this->factory()->organizer->create();
		$linked_post2 = $this->factory()->organizer->create();

		$matching      = $this->factory()->event->create_many( 2, [ '_EventOrganizerID' => $linked_post ] );
		$matching2     = $this->factory()->event->create_many( 2, [ '_EventOrganizerID' => $linked_post2 ] );
		$not_matching  = $this->factory()->event->create_many( 3 );

		$this->assertEqualSets( $matching, tribe_events()->where( 'organizer', $linked_post )->get_ids() );
		$this->assertEqualSets( $matching, tribe_events()->where( 'organizer', get_post( $linked_post ) )->get_ids() );
		$this->assertEqualSets( array_merge( $matching, $matching2 ), tribe_events()->where( 'organizer', [ get_post( $linked_post ), $linked_post2 ] )->get_ids() );
		$this->assertCount( 7, tribe_events()->get_ids() );
	}

	/**
	 * It should allow getting events by venue
	 *
	 * @test
	 */
	public function should_allow_getting_events_by_venue() {
		$linked_post  = $this->factory()->venue->create();
		$linked_post2 = $this->factory()->venue->create();

		$matching      = $this->factory()->event->create_many( 2, [ '_EventVenueID' => $linked_post ] );
		$matching2     = $this->factory()->event->create_many( 2, [ '_EventVenueID' => $linked_post2 ] );
		$not_matching  = $this->factory()->event->create_many( 3 );

		$this->assertEqualSets( $matching, tribe_events()->where( 'venue', $linked_post )->get_ids() );
		$this->assertEqualSets( $matching, tribe_events()->where( 'venue', get_post( $linked_post ) )->get_ids() );
		$this->assertEqualSets( array_merge( $matching, $matching2 ), tribe_events()->where( 'venue', [ get_post( $linked_post ), $linked_post2 ] )->get_ids() );
		$this->assertCount( 7, tribe_events()->get_ids() );
	}

	/**
	 * It should allow getting events by website
	 *
	 * @test
	 */
	public function should_allow_getting_events_by_website() {
		$matching = $this->factory()->event->create_many( 2, [ 'meta_input' => [ '_EventURL' => 'https://twitter.com/roblovestwitter' ] ] );

		$this->factory()->event->create_many( 3 );

		$this->assertEqualSets( $matching, tribe_events()->where( 'website', '://twitter.com/' )->get_ids() );
		$this->assertEqualSets( $matching, tribe_events()->where( 'website', '/.*:\/\/twitter.com\/.*/' )->get_ids() );
		$this->assertCount( 5, tribe_events()->get_ids() );
	}

	/**
	 * It should allow getting events by event_category
	 *
	 * @test
	 */
	public function should_allow_getting_events_by_event_category() {
		$terms = $this->factory()->term->create_many( 4, [ 'taxonomy' => \Tribe__Events__Main::TAXONOMY ] );

		$matching  = $this->factory()->event->create_many( 2, [ 'tax_input' => [ \Tribe__Events__Main::TAXONOMY => $terms[0] ] ] );
		$matching2 = $this->factory()->event->create_many( 2, [ 'tax_input' => [ \Tribe__Events__Main::TAXONOMY => $terms[1] ] ] );

		$not_matching = $this->factory()->event->create_many( 3 );

		$term_1 = get_term( $terms[0] );
		$term_2 = get_term( $terms[1] );
		$term_3 = get_term( $terms[2] );
		$term_4 = get_term( $terms[3] );

		$this->assertEqualSets( $matching, tribe_events()->where( 'event_category', $term_1 )->get_ids() );
		$this->assertEqualSets( $matching, tribe_events()->where( 'event_category', $term_1->term_id )->get_ids() );
		$this->assertEqualSets( $matching, tribe_events()->where( 'event_category', $term_1->slug )->get_ids() );
		$this->assertEqualSets( array_merge( $matching, $matching2 ), tribe_events()->where( 'event_category', [ $term_1->slug, $term_2 ] )->get_ids() );

		$this->assertEqualSets( array_merge( $not_matching, $matching ), tribe_events()->where( 'event_category_not_in', $term_2 )->get_ids() );
		$this->assertEqualSets( array_merge( $not_matching, $matching ), tribe_events()->where( 'event_category_not_in', $term_2->term_id )->get_ids() );
		$this->assertEqualSets( array_merge( $not_matching, $matching ), tribe_events()->where( 'event_category_not_in', $term_2->slug )->get_ids() );
		$this->assertEqualSets( $not_matching, tribe_events()->where( 'event_category_not_in', [ $term_1->slug, $term_2 ] )->get_ids() );
		$this->assertEqualSets( [], tribe_events()->where( 'event_category_not_in', [ $term_4->term_id, $term_3 ] )->get_ids() );

		$this->assertCount( 7, tribe_events()->get_ids() );
	}

	/**
	 * It should allow getting events by tag
	 *
	 * @test
	 */
	public function should_allow_getting_events_by_tag() {
		$terms = $this->factory()->term->create_many( 4, [ 'taxonomy' => 'post_tag' ] );

		$matching  = $this->factory()->event->create_many( 2, [ 'tax_input' => [ 'post_tag' => $terms[0] ] ] );
		$matching2 = $this->factory()->event->create_many( 2, [ 'tax_input' => [ 'post_tag' => $terms[1] ] ] );

		$not_matching = $this->factory()->event->create_many( 3 );

		$term_1 = get_term( $terms[0] );
		$term_2 = get_term( $terms[1] );
		$term_3 = get_term( $terms[2] );
		$term_4 = get_term( $terms[3] );

		$this->assertEqualSets( $matching, tribe_events()->where( 'tag', $term_1 )->get_ids() );
		$this->assertEqualSets( $matching, tribe_events()->where( 'tag', $term_1->term_id )->get_ids() );
		$this->assertEqualSets( $matching, tribe_events()->where( 'tag', $term_1->slug )->get_ids() );
		$this->assertEqualSets( array_merge( $matching, $matching2 ), tribe_events()->where( 'tag', [ $term_1->slug, $term_2 ] )->get_ids() );

		$this->assertEqualSets( array_merge( $not_matching, $matching ), tribe_events()->where( 'tag_not_in', $term_2 )->get_ids() );
		$this->assertEqualSets( array_merge( $not_matching, $matching ), tribe_events()->where( 'tag_not_in', $term_2->term_id )->get_ids() );
		$this->assertEqualSets( array_merge( $not_matching, $matching ), tribe_events()->where( 'tag_not_in', $term_2->slug )->get_ids() );
		$this->assertEqualSets( $not_matching, tribe_events()->where( 'tag_not_in', [ $term_1->slug, $term_2 ] )->get_ids() );
		$this->assertEqualSets( [], tribe_events()->where( 'tag_not_in', [ $term_4->term_id, $term_3 ] )->get_ids() );

		$this->assertCount( 7, tribe_events()->get_ids() );
	}
}
