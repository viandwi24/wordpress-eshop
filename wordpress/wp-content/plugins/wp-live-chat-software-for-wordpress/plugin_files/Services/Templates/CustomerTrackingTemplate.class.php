<?php
/**
 * Class CustomerTrackingTemplate
 *
 * @package LiveChat\Services\Templates
 */

namespace LiveChat\Services\Templates;

use LiveChat\Services\TemplateParser;
use LiveChat\Services\User;

/**
 * Class CustomerTrackingTemplate
 *
 * @package LiveChat\Services\Templates
 */
class CustomerTrackingTemplate extends Template {
	/**
	 * User instance.
	 *
	 * @var User
	 */
	private $user;

	/**
	 * CustomerTrackingTemplate constructor.
	 *
	 * @param User           $user            User instance.
	 * @param TemplateParser $template_parser TemplateParser instance.
	 */
	public function __construct( $user, $template_parser ) {
		parent::__construct( $template_parser );
		$this->user = $user;
	}

	/**
	 * Renders customer tracking script.
	 *
	 * @return null|string
	 */
	public function render() {
		$context = array(
			'lcConnectJSON' => wp_json_encode(
				array(
					'ajaxUrl'  => admin_url( 'admin-ajax.php' ),
					'customer' => $this->user->get_user_data(),
				)
			),
		);

		return $this->template_parser->parse_template( 'customer_tracking.html.twig', $context, false );
	}

	/**
	 * Returns new instance of CustomerTrackingTemplate.
	 *
	 * @return static
	 */
	public static function create() {
		return new static(
			User::get_instance(),
			TemplateParser::create( '../templates' )
		);
	}
}
