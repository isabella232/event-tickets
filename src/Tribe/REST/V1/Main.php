<?php


/**
 * Class Tribe__Tickets__REST__V1__Main
 *
 * The main entry point for ET REST API.
 *
 * This class should not contain business logic and merely set up and start the ET REST API support.
 */
class Tribe__Tickets__REST__V1__Main extends Tribe__REST__Main {

	/**
	 * Event Tickets REST API URL prefix.
	 *
	 * This prefx is appended to the Modern Tribe REST API URL ones.
	 *
	 * @var string
	 */
	protected $url_prefix = '/tickets/v1';

	/**
	 * @var array
	 */
	protected $registered_endpoints = array();

	/**
	 * Binds the implementations needed to support the REST API.
	 *
	 * @since TBD
	 *
	 */
	public function bind_implementations() {
		tribe_singleton( 'tickets.rest-v1.messages', 'Tribe__Tickets__REST__V1__Messages' );
		tribe_singleton( 'tickets.rest-v1.headers-base', 'Tribe__Tickets__REST__V1__Headers__Base' );
		tribe_singleton( 'tickets.rest-v1.settings', 'Tribe__Tickets__REST__V1__Settings' );
		tribe_singleton( 'tickets.rest-v1.system', 'Tribe__Tickets__REST__V1__System' );
		tribe_singleton( 'tickets.rest-v1.validator', 'Tribe__Tickets__REST__V1__Validator__Base' );
		tribe_singleton( 'tickets.rest-v1.repository', 'Tribe__Tickets__REST__V1__Post_Repository' );

		include_once Tribe__Tickets__Main::instance()->plugin_path . 'src/functions/advanced-functions/rest-v1.php';
	}

	/**
	 * Hooks the filters and actions required for the REST API support to kick in.
	 *
	 * @since TBD
	 *
	 */
	public function hook() {
		$this->hook_headers();
		$this->hook_settings();

		/** @var Tribe__Tickets__REST__V1__System $system */
		$system = tribe( 'tickets.rest-v1.system' );

		if ( ! $system->supports_et_rest_api() || ! $system->et_rest_api_is_enabled() ) {
			return;
		}

		add_action( 'rest_api_init', array( $this, 'register_endpoints' ) );

	}

	/**
	 * Hooks the additional headers and meta tags related to the REST API.
	 *
	 * @since TBD
	 *
	 */
	protected function hook_headers() {
		/** @var Tribe__Tickets__REST__V1__System $system */
		$system = tribe( 'tickets.rest-v1.system' );
		/** @var Tribe__REST__Headers__Base_Interface $headers_base */
		$headers_base = tribe( 'tickets.rest-v1.headers-base' );

		if ( ! $system->et_rest_api_is_enabled() ) {
			if ( ! $system->supports_et_rest_api() ) {
				tribe_singleton( 'tickets.rest-v1.headers', new Tribe__REST__Headers__Unsupported( $headers_base, $this ) );
			} else {
				tribe_singleton( 'tickets.rest-v1.headers', new Tribe__REST__Headers__Disabled( $headers_base ) );
			}
		} else {
			tribe_singleton( 'tickets.rest-v1.headers', new Tribe__REST__Headers__Supported( $headers_base, $this ) );
		}

		/** @var Tribe__REST__Headers__Headers_Interface $headers */
		$headers = tribe( 'tickets.rest-v1.headers' );

		add_action( 'wp_head', array( $headers, 'add_header' ), 10, 0 );
		add_action( 'template_redirect', array( $headers, 'send_header' ), 11, 0 );
	}

	/**
	 * Hooks the additional Event Tickets Settings related to the REST API.
	 *
	 * @since TBD
	 *
	 */
	protected function hook_settings() {
		add_filter( 'tribe_addons_tab_fields', array( tribe( 'tickets.rest-v1.settings' ), 'filter_tribe_addons_tab_fields' ) );
	}

	/**
	 * Returns the URL where the API users will find the API documentation.
	 *
	 * @since TBD
	 *
	 * @return string
	 */
	public function get_reference_url() {
		return esc_attr( 'https://theeventscalendar.com/' );
	}

	/**
	 * Registers the endpoints, and the handlers, supported by the REST API
	 *
	 * @since TBD
	 *
	 * @param bool $register_routes Whether routes should be registered as well or not.
	 */
	public function register_endpoints( $register_routes = true ) {
		$this->register_documentation_endpoint( $register_routes );
	}

	/**
	 * Builds and hooks the documentation endpoint
	 *
	 * @since TBD
	 *
	 * @param bool $register_routes Whether routes for the endpoint should be registered or not.
	 */
	protected function register_documentation_endpoint( $register_routes = true ) {
		$endpoint = new Tribe__Tickets__REST__V1__Endpoints__Swagger_Documentation( $this->get_semantic_version() );

		tribe_singleton( 'tickets.rest-v1.endpoints.documentation', $endpoint );

		if ( $register_routes ) {
			register_rest_route( $this->get_events_route_namespace(), '/doc', array(
				'methods'  => WP_REST_Server::READABLE,
				'callback' => array( $endpoint, 'get' ),
			) );
		}

		/** @var Tribe__Documentation__Swagger__Builder_Interface $documentation */
		$documentation = tribe( 'tickets.rest-v1.endpoints.documentation' );
		$documentation->register_documentation_provider( '/doc', $endpoint );
		$documentation->register_definition_provider( 'Ticket', new Tribe__Tickets__REST__V1__Documentation__Ticket_Definition_Provider() );
	}

	protected function get_semantic_version() {
		return '1.0.0';
	}

	/**
	 * Returns the events REST API namespace string that should be used to register a route.
	 *
	 * @since TBD
	 *
	 * @return string
	 */
	protected function get_events_route_namespace() {
		return $this->get_namespace() . '/tickets/' . $this->get_version();
	}

	/**
	 * Returns the string indicating the REST API version.
	 *
	 * @since TBD
	 *
	 * @return string
	 */
	public function get_version() {
		return 'v1';
	}


	/**
	 * Returns the REST API URL prefix that will be appended to the namespace.
	 *
	 * The prefix should be in the `/some/path` format.
	 *
	 * @since TBD
	 *
	 * @return string
	 */
	protected function url_prefix() {
		return $this->url_prefix;
	}

}
