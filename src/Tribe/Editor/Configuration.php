<?php
use Tribe__Events__Main as TEC;
/**
 * Class Tribe__Events__Editor__Configuration
 *
 * @since 4.7
 */
class Tribe__Events__Editor__Configuration implements Tribe__Editor__Configuration_Interface  {

	/**
	 * Hook used to attach actions / filters
	 *
	 * @since 4.7
	 */
	public function hook() {
		add_filter( 'tribe_editor_config', array( $this, 'editor_config' ) );
	}

	/**
	 * Setup the initial Event Object.
	 *
	 * @since TBD
	 *
	 * @todo @backend Improve this method and verify if this is the correct place for this to live.
	 *
	 * @param null|int|WP_Post $event WP_Post object of event, post ID, or null.
	 *
	 * @return array Setup the Data for the Event Post Object.
	 */
	public function get_event_object( $event = null ) {
		$data = [
			'is_new_post' => true,
		];

		if ( ! $event ) {
			$event = tribe_get_request_var( 'post', false );
		}

		if ( $event ) {
			$event = get_post( $event );
		}

		if ( ! $event instanceof WP_Post ) {
			return $data;
		}

		$meta = get_post_meta( $event->ID );
		$meta = array_map(
			static function( $value ) {
				return count( $value ) === 1 ? reset( $value ) : $value;
			},
			$meta
		);

		$data['is_new_post'] = false;
		$data['meta']        = $meta;

		return $data;
	}

	/**
	 * Add custom variables to be localized
	 *
	 * @since 4.7
	 *
	 * @param array $editor_config
	 * @return array
	 */
	public function editor_config( $editor_config ) {
		$tec                     = empty( $editor_config['events'] ) ? [] : $editor_config['events'];
		$editor_config['events'] = array_merge( (array) $tec, $this->localize() );

		$post_objects                  = empty( $editor_config['post_objects'] ) ? [] : $editor_config['post_objects'];
		$editor_config['post_objects'] = array_merge( (array) $post_objects, [ TEC::POSTTYPE => $this->get_event_object() ] );

		return $editor_config;
	}

	/**
	 * Return the variables to be localized
	 *
	 * @since 4.7
	 *
	 * @return array
	 */
	public function localize() {
		/** @var Tribe__Events__Admin__Event_Meta_Box $events_meta_box */
		$events_meta_box = tribe( 'tec.admin.event-meta-box' );

		$data = [
			'settings'      => tribe( 'events.editor.settings' )->get_options(),
			'timezoneHTML'  => tribe_events_timezone_choice( Tribe__Events__Timezones::get_event_timezone_string() ),
			'priceSettings' => [
				'defaultCurrencySymbol'   => tribe_get_option( 'defaultCurrencySymbol', '$' ),
				'defaultCurrencyPosition' => (
					tribe_get_option( 'reverseCurrencyPosition', false ) ? 'suffix' : 'prefix'
				),
			],
			'dateSettings'  => array(
				'datepickerFormat' => Tribe__Date_Utils::datepicker_formats( tribe_get_option( 'datepickerFormat' ) ),
			),
			'editor'        => array(
				'isClassic' => $this->post_is_from_classic_editor( tribe_get_request_var( 'post', 0 ) ),
			),
			'googleMap'     => array(
				'embed' => tribe_get_option( 'embedGoogleMaps', true ),
				'zoom'  => apply_filters( 'tribe_events_single_map_zoom_level', (int) tribe_get_option( 'embedGoogleMapsZoom', 8 ) ),
				'key'   => tribe_get_option( 'google_maps_js_api_key' ),
			),
			'timeZone'     => array(
				'showTimeZone' => tribe_get_option( 'tribe_events_timezones_show_zone', false ),
				'label'        => $this->get_timezone_label(),
			),
			'defaultTimes' => array(
				'start' => $events_meta_box->get_timepicker_default( 'start' ),
				'end' => $events_meta_box->get_timepicker_default( 'end' ),
			),
		];

		return $data;
	}


	/**
	 * Check if post is from classic editor
	 *
	 * @since 4.7
	 *
	 * @param int|WP_Post $post
	 *
	 * @return bool
	 */
	public function post_is_from_classic_editor( $post ) {
		if ( ! $post instanceof WP_Post ) {
			$post = get_post( $post );
		}

		if ( empty( $post ) || ! ( $post instanceof WP_Post ) ) {
			return false;
		}

		/** @var Tribe__Editor $editor */
		$editor = tribe( 'editor' );
		return tribe_is_truthy( get_post_meta( $post->ID, $editor->key_flag_classic_editor, true ) );
	}

	/**
	 * Returns the site timezone as a string
	 *
	 * @since 4.7.2
	 *
	 * @return string
	 */
	public function get_timezone_label() {
		return class_exists( 'Tribe__Timezones' )
			? Tribe__Timezones::wp_timezone_string()
			: get_option( 'timezone_string', 'UTC' );
	}
}
