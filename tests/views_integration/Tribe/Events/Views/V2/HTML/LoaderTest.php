<?php
namespace Tribe\Events\Views\V2\Views\HTML;

use Tribe\Events\Views\V2\TestHtmlCase;

class LoaderTest extends TestHtmlCase {

	/**
	 * @test
	 */
	public function it_should_contain_correct_html_classes() {
		$template = $this->template->template( 'loader' );
		$html = $this->document->html( $template );

		$this->assertEquals(
			$html->find( '.tribe-events-view-loader' )->count(),
			1,
			'Loader HTML needs to contain one ".tribe-events-view-loader" element'
		);


		$this->assertTrue(
			$html->find( '.tribe-events-view-loader' )->is( '.tribe-common-a11y-hidden' ),
			'Loader element needs to be ".tribe-common-a11y-hidden" by default'
		);

		$this->assertTrue(
			$html->find( '.tribe-events-view-loader' )->children()->is( '.tribe-events-view-loader__spinner' ),
			'Loader HTML needs to contain ".tribe-events-view-loader__spinner" element'
		);
	}

	/**
	 * @test
	 */
	public function it_should_contain_a11y_attributes() {
		$template = $this->template->template( 'loader' );
		$html = $this->document->html( $template );
		$loader = $html->find( '.tribe-events-view-loader' );

		$this->assertTrue(
			$loader->is( '[role="alert"]' ),
			'Loader needs to be role="alert"'
		);

		$this->assertTrue(
			$loader->is( '[aria-live="assertive"]' ),
			'Loader needs to be aria-live="assertive"'
		);
	}

	/**
	 * @test
	 */
	public function it_should_contain_text() {
		$text = '%placeholder-text%';
		$template = $this->template->template( 'loader', [ 'text' => $text ] );
		$html = $this->document->html( $template );
		$spinner = $html->find( '.tribe-events-view-loader__spinner' );

		$this->assertEquals(
			$text,
			trim( $spinner->text() )
		);
	}

	/**
	 * @test
	 */
	public function it_should_escape_html_for_text() {
		$text = '<strong class="find-me">%placeholder-text%</strong>';
		$template = $this->template->template( 'loader', [ 'text' => $text ] );
		$html = $this->document->html( $template );
		$spinner = $html->find( '.tribe-events-view-loader__spinner' );
		$find_element = $html->find( '.find-me' );

		$this->assertEquals(
			$find_element->count(),
			0,
			'Text shouldnt be converted into HTML'
		);
	}
}
