<?php

namespace TEC\Tickets\Commerce\Gateways\Stripe;

use TEC\Tickets\Commerce\Gateways\Stripe\REST\Order_Endpoint;
use TEC\Tickets\Commerce\Gateways\Stripe\REST\Publishable_Key_Endpoint;
use TEC\Tickets\Commerce\Success;

/**
 * Class Assets.
 *
 * @since   TBD
 *
 * @package TEC\Tickets\Commerce\Gateways\Stripe
 */
class Assets extends \tad_DI52_ServiceProvider {

	/**
	 * The nonce action to use when requesting a stripe publishable_key
	 *
	 * @since TBD
	 *
	 * @var string
	 */
	const PUBLISHABLE_KEY_NONCE_ACTION = 'stripe_pubkey';

	/**
	 * The nonce action to use when requesting the creation of a new order
	 *
	 * @since TBD
	 *
	 * @var string
	 */
	const ORDER_NONCE_ACTION = 'stripe_order';

	/**
	 * @inheritDoc
	 */
	public function register() {
		$plugin = \Tribe__Tickets__Main::instance();

		tribe_asset(
			$plugin,
			'tec-tickets-commerce-gateway-stripe-base',
			'https://js.stripe.com/v3/',
			[],
			null,
			[
				'type' => 'js',
			]
		);

		tribe_asset(
			$plugin,
			'tec-tickets-commerce-gateway-stripe-checkout',
			'commerce/gateway/stripe/checkout.js',
			[
				'jquery',
				'tribe-common',
				'tec-ky',
				'tribe-query-string',
				'tec-tickets-commerce-gateway-stripe-base',
				'tribe-tickets-loader',
				'tribe-tickets-commerce-js',
				'tribe-tickets-commerce-notice-js',
				'tribe-tickets-commerce-base-gateway-checkout-js',
			],
			'wp_enqueue_scripts',
			[
				'module'   => true,
				'groups'   => [
					'tec-tickets-commerce-gateway-stripe',
				],
				//'conditionals' => [ $this, 'should_enqueue_assets' ],
				'localize' => [
					'name' => 'tecTicketsCommerceGatewayStripeCheckout',
					'data' => static function () {
						return [
							'successUrl'   => tribe( Success::class )->get_url(),
							'orderEndpoint' => tribe( Order_Endpoint::class )->get_route_url(),
							'orderNonce'    => wp_create_nonce( static::ORDER_NONCE_ACTION ),
							'publishableKey' => tribe( Merchant::class )->get_publishable_key(),
						];
					},
				],
			]
		);
	}

	/**
	 * Define if the assets for `Stripe` should be enqueued or not.
	 *
	 * @since TBD
	 *
	 * @return bool If the `Stripe` assets should be enqueued or not.
	 */
	public function should_enqueue_assets() {
		return tribe( Gateway::class )->is_active();
	}
}