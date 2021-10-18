<?php
/**
 * Class ContactButtonWidgetTemplate
 *
 * @package LiveChat\Services\Templates
 */

namespace LiveChat\Services\Templates;

use LiveChat\Services\TemplateParser;

/**
 * Class ContactButtonWidgetTemplate
 *
 * @package LiveChat\Services\Templates
 */
class ContactButtonWidgetTemplate extends Template {
	/**
	 * Renders contact button widget for Elementor plugin.
	 *
	 * @return string
	 */
	public function render() {
		return $this->template_parser->parse_template( 'contact_button_widget.html.twig', array() );
	}

	/**
	 * Returns instance of ContactButtonWidgetTemplate class.
	 *
	 * @return ContactButtonWidgetTemplate|static
	 */
	public static function create() {
		return new static( TemplateParser::create( '../templates' ) );
	}
}
