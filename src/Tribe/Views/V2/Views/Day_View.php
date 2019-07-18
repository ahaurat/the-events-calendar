<?php
/**
 * The Day View.
 *
 * @package Tribe\Events\Views\V2\Views
 * @since 4.9.4
 */

namespace Tribe\Events\Views\V2\Views;

use Tribe\Events\Views\V2\View;
use Tribe__Utils__Array as Arr;

class Day_View extends View {
	/**
	 * Slug for this view
	 *
	 * @since 4.9.4
	 *
	 * @var string
	 */
	protected $slug = 'day';

	/**
	 * Visibility for this view.
	 *
	 * @since 4.9.4
	 *
	 * @var bool
	 */
	protected $publicly_visible = true;


	/**
	 * {@inheritDoc}
	 */
	protected function setup_repository_args( \Tribe__Context $context = null ) {
		$context = null !== $context ? $context : $this->context;

		/*
		 * The View not care where the context comes from: from the View point of view the context is the only
		 * source of truth.
		 * The context might come from the main query, from a widget, a shortcode or a REST request.
		 */
		$context_arr = $context->to_array();

		/*
		 * Depending on the context contents let's set up the arguments to fetch the events.
		 */
		$args = [
			'posts_per_page' => $context_arr['posts_per_page'],
			'paged'          => max( Arr::get( $context_arr, 'page', 1 ), 1 ),
		];

		$date = Arr::get( $context_arr, 'event_date', 'now' );
		$event_display = Arr::get( $context_arr, 'event_display_mode', Arr::get( $context_arr, 'event_display' ), 'current' );

		if ( 'past' !== $event_display ) {
			$args['ends_after'] = $date;
		} else {
			$args['order']       = 'DESC';
			$args['ends_before'] = $date;
		}

		return $args;
	}
}
