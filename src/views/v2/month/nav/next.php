<?php
/**
 * View: Month View Nav Next Button
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/events/views/v2/month/nav/next.php
 *
 * See more documentation about our views templating system.
 *
 * @link {INSERT_ARTCILE_LINK_HERE}
 *
 * @var string $link The URL to the next page, if any, or an empty string.
 *
 * @version 4.9.3
 *
 */
?>
<a
	href="<?php echo esc_url( $link ); ?>"
	rel="next"
	class="tribe-events-c-nav__next"
	data-js="tribe-events-view-link"
>
	<?php echo esc_html( $label ); ?>
</a>
