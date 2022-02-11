<?php

namespace TEC\Tickets\Commerce\Gateways\Stripe\REST;

use TEC\Tickets\Commerce\Gateways\Contracts\Abstract_REST_Endpoint;
use TEC\Tickets\Commerce\Gateways\Stripe\Settings;

use TEC\Tickets\Commerce\Gateways\Stripe\Status;
use TEC\Tickets\Commerce\Gateways\Stripe\Webhooks;
use WP_REST_Server;
use WP_REST_Request;
use WP_REST_Response;

use Tribe__Utils__Array as Arr;

/**
 * Class Webhook Endpoint.
 *
 * @since   TBD
 *
 * @package TEC\Tickets\Commerce\Gateways\Stripe\REST
 */
class Webhook_Endpoint extends Abstract_REST_Endpoint {

	/**
	 * The REST API endpoint path.
	 *
	 * @since TBD
	 *
	 * @var string
	 */
	protected $path = '/commerce/stripe/webhook';

	/**
	 * Register the actual endpoint on WP Rest API.
	 *
	 * @since TBD
	 */
	public function register(): void {
		$namespace     = tribe( 'tickets.rest-v1.main' )->get_events_route_namespace();
		$documentation = tribe( 'tickets.rest-v1.endpoints.documentation' );

		register_rest_route(
			$namespace,
			$this->get_endpoint_path(),
			[
				'methods'             => WP_REST_Server::CREATABLE,
				'args'                => $this->incoming_request_args(),
				'callback'            => [ $this, 'handle_incoming_request' ],
				'permission_callback' => [ $this, 'verify_incoming_request_permission' ],
			]
		);

		$documentation->register_documentation_provider( $this->get_endpoint_path(), $this );
	}

	/**
	 * Arguments for the incoming request endpoint.
	 *
	 * @since TBD
	 *
	 * @return array
	 */
	public function incoming_request_args(): array {
		return [];
	}

	/**
	 * Handles the request that creates an order with Tickets Commerce and the Stripe gateway.
	 *
	 * If there is any request related error handling it should happen here, webhook related ones should go in
	 * the Handler methods.
	 *
	 * @since TBD
	 *
	 * @param WP_REST_Request $request The request object.
	 *
	 * @return WP_REST_Response
	 */
	public function handle_incoming_request( WP_REST_Request $request ): WP_REST_Response {
		// Setup a base response.
		$response = new WP_REST_Response( null, 200 );

		// Flag that the webhooks are working as expected.
		tribe_update_option( Webhooks::$option_is_valid_webhooks, true );

		// After this point we are ready to do individual modifications based on the Webhook value.
		return Webhooks\Handler::process_webhook_response( $request, $response );
	}

	/**
	 * Given a WP Rest request we determine if it has the correct Stripe signature.
	 *
	 * @since TBD
	 *
	 * @param WP_REST_Request $request Which request we are validating.
	 *
	 * @return bool
	 */
	public function verify_incoming_request_permission( WP_REST_Request $request ): bool {
		return $this->signature_is_valid( $request->get_header( 'Stripe-Signature' ), $request->get_body() );
	}

	/**
	 * Verifies the Stripe-Signature against the stored Webhook Signing Secret to make sure it's authentic.
	 *
	 * @link  https://stripe.com/docs/webhooks/signatures
	 *
	 * @since TBD
	 *
	 * @param string $header the Stripe-Signature request header
	 * @param string $body   the raw json request body
	 *
	 * @return bool
	 */
	protected function signature_is_valid( string $header, string $body ): bool {
		if ( ! $header || ! $body ) {
			return false;
		}

		$signing_secret = tribe_get_option( Webhooks::$option_webhooks_signing_key );

		if ( empty( $signing_secret ) ) {
			return false;
		}

		$time            = time();
		$header_parts    = explode( ',', $header );
		$signature_parts = [];

		foreach ( $header_parts as $part ) {
			$pair = explode( '=', $part );

			if ( in_array( $pair[0], [ 't', 'v1' ], true ) ) {
				$signature_parts[ $pair[0] ] = $pair[1];
			}
		}

		if ( empty( $signature_parts['t'] ) || empty( $signature_parts['v1'] ) ) {
			return false;
		}

		// By default, we are using the same 5 minutes threshold that the official Stripe libs do.
		if ( ( $time - (int) $signature_parts['t'] ) > 5 * MINUTE_IN_SECONDS ) {
			return false;
		}

		$signed_payload = implode( '', [ $signature_parts['t'], '.', $body ] );
		$hmac_signature = hash_hmac( 'sha256', $signed_payload, $signing_secret );

		return hash_equals( $signature_parts['v1'], $hmac_signature );
	}
}
