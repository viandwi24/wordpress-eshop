<?php
/**
 * Class Template
 *
 * @package LiveChat\Services\Templates
 */

namespace LiveChat\Services\Templates;

use LiveChat\Services\TemplateParser;

/**
 * Class Template
 *
 * @package LiveChat\Services\Templates
 */
abstract class Template {
	/**
	 * Instance of TemplateParser.
	 *
	 * @var TemplateParser
	 */
	protected $template_parser;

	/**
	 * Template constructor.
	 *
	 * @param TemplateParser $template_parser Instance of TemplateParser.
	 */
	public function __construct( $template_parser ) {
		$this->template_parser = $template_parser;
	}

	/**
	 * Renders template.
	 *
	 * @return mixed
	 */
	abstract public function render();

	/**
	 * Returns new instance of Template.
	 *
	 * @return static
	 */
	public static function create() {
		return new static( TemplateParser::create( '../templates' ) );
	}
}
