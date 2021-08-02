<?php

namespace TEC\Tickets\Commerce\Gateways\PayPal\REST;

use TEC\Tickets\Commerce\Gateways\PayPal\Repositories\Order;


use TEC\Tickets\Commerce\Gateways\PayPal\REST;
use Tribe__Tickets__REST__V1__Endpoints__Base;
use Tribe__REST__Endpoints__CREATE_Endpoint_Interface;
use Tribe__Documentation__Swagger__Provider_Interface;
use WP_Error;
use WP_REST_Request;
use WP_REST_Response;


/**
 * Class Create_Order
 *
 * @since   TBD
 *
 * @package TEC\Tickets\Commerce\Gateways\PayPal\REST
 */
class Orders
	extends Tribe__Tickets__REST__V1__Endpoints__Base
	implements Tribe__REST__Endpoints__CREATE_Endpoint_Interface,
	Tribe__Documentation__Swagger__Provider_Interface
{

	/**
	 * The REST API endpoint path.
	 *
	 * @since TBD
	 *
	 * @var string
	 */
	protected $path = '/tickets-commerce/paypal/on-boarding';

	/**
	 * Gets the Endpoint path for the on boarding process.
	 *
	 * @since TBD
	 *
	 * @return string
	 */
	public function get_endpoint_path() {
		return $this->path;
	}

	/**
	 * Get the REST API route URL.
	 *
	 * @since TBD
	 *
	 * @return string The REST API route URL.
	 */
	public function get_route_url() {
		$rest = tribe( REST::class );

		return rest_url( '/' . $rest->namespace . $this->get_endpoint_path(), 'https' );
	}

	/**
	 * {@inheritDoc}
	 *
	 * @todo WIP -- Still using pieces from Give.
	 *
	 * @since TBD
	 *
	 * @param WP_REST_Request $request   The request object.
	 * @param bool            $return_id Whether the created post ID should be returned or the full response object.
	 *
	 * @return WP_Error|WP_REST_Response|int An array containing the data on success or a WP_Error instance on failure.
	 */
	public function create( WP_REST_Request $request, $return_id = false ) {
		$event   = $request->get_body();
		$headers = $request->get_headers();

		$formId   = absint( tribe_get_request_var( 't' ) );

		$data = [
			'formId'              => $formId,
			'formTitle'           => give_payment_gateway_item_title( [ 'post_data' => $postData ], 127 ),
			'paymentAmount'       => isset( $postData['give-amount'] ) ? (float) apply_filters( 'give_payment_total', give_maybe_sanitize_amount( $postData['give-amount'], [ 'currency' => give_get_currency( $formId ) ] ) ) : '0.00',
			'payer'               => [
				'firstName' => $postData['give_first'],
				'lastName'  => $postData['give_last'],
				'email'     => $postData['give_email'],
			],
			'application_context' => [
				'shipping_preference' => 'NO_SHIPPING',
			],
		];

		$data = [
			'success' => true,
			'id' => $result,
		];

		return new WP_REST_Response( $data );
	}

	/**
	 * {@inheritDoc}
	 *
	 * @since TBD
	 *
	 * @return array
	 */
	public function CREATE_args() {
		// Webhooks do not send any arguments, only JSON content.
		return [];
	}

	/**
	 * {@inheritDoc}
	 *
	 * @since TBD
	 *
	 * @return bool Whether the current user can post or not.
	 */
	public function can_create() {
		// Always open, no further user-based validation.
		return true;
	}

	/**
	 * {@inheritDoc}
	 *
	 * @since TBD
	 */
	public function get_documentation() {
		return [
			'post' => [
				'consumes'   => [
					'application/json',
				],
				'parameters' => [],
				'responses'  => [
					'200' => [
						'description' => __( 'Processes the Webhook as long as it includes valid Payment Event data', 'event-tickets' ),
						'content'     => [
							'application/json' => [
								'schema' => [
									'type'       => 'object',
									'properties' => [
										'success' => [
											'description' => __( 'Whether the processing was successful', 'event-tickets' ),
											'type'        => 'boolean',
										],
									],
								],
							],
						],
					],
					'403' => [
						'description' => __( 'The webhook was invalid and was not processed', 'event-tickets' ),
						'content'     => [
							'application/json' => [
								'schema' => [
									'type' => 'object',
								],
							],
						],
					],
				],
			],
		];
	}
}