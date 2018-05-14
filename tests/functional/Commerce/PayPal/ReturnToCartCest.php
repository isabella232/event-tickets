<?php

namespace Commerce\PayPal;


use FunctionalTester;
use Tribe\Tickets\Test\Commerce\PayPal\Ticket_Maker as PayPal_Ticket_Maker;

class ReturnToCartCest {
	use PayPal_Ticket_Maker;

	/**
	 * @var \WP_UnitTest_Factory
	 */
	protected $factory;

	/**
	 * @var string
	 */
	private $return_to_cart_link = '.tribe-commerce.return-to-cart';


	public function _before( FunctionalTester $I ) {
		// set the factory to satisfy the PayPal_Ticket_Maker trait
		$this->factory = $I->factory();
		// let's make sure we're not redirected to the welcome page when accessing admin
		$I->haveOptionInDatabase( 'tribe_skip_welcome', '1' );
		// let's make sure posts can be ticketed
		$I->setTribeOption( 'ticket-enabled-post-types', [ 'post' ] );
		// let's make sure Tribe Commerce is enabled and configured
		$I->setTribeOption( 'ticket-paypal-enable', 'yes' );
		$I->setTribeOption( 'ticket-paypal-email', 'admin@tribe.localhost' );
		$I->setTribeOption( 'ticket-paypal-sandbox', 'yes' );
		$I->setTribeOption( 'ticket-paypal-configure', 'yes' );
		$I->setTribeOption( 'ticket-paypal-ipn-config-status', 'yes' );
		$I->setTribeOption( 'ticket-paypal-ipn-enabled', 'yes' );
		$I->setTribeOption( 'ticket-paypal-ipn-address-set', 'yes' );
	}

	/**
	 * It should not display a return to cart link if there are no PayPal tickets in the cart
	 *
	 * @test
	 */
	public function should_not_display_a_return_to_cart_link_if_there_are_no_pay_pal_tickets_in_the_cart( FunctionalTester $I ) {
		$post_id = $I->havePostInDatabase( [ 'post_status' => 'publish' ] );
		$this->make_ticket( $post_id, 1 );

		$I->amOnPage( "/?p={$post_id}" );

		$I->dontSeeElement( $this->return_to_cart_link );
	}

	/**
	 * It should display a return to cart link if there are PayPal tickets in the cart
	 *
	 * @test
	 */
	public function should_display_a_return_to_cart_link_if_there_are_pay_pal_tickets_in_the_cart( FunctionalTester $I ) {
		$post_id   = $I->havePostInDatabase( [ 'post_status' => 'publish' ] );
		$ticket_id = $this->make_ticket( $post_id, 1 );
		$transient = \Tribe__Tickets__Commerce__PayPal__Cart__Unmanaged::get_transient_name( '123foo' );
		$I->haveTransientInDatabase( $transient, [ $ticket_id => 2 ] );

		$I->amOnPage( "/?p={$post_id}&tpp_invoice=123foo" );

		$I->seeElement( $this->return_to_cart_link );
		$link = $I->grabAttributeFrom( $this->return_to_cart_link, 'href' );
		parse_str( parse_url( $link, PHP_URL_QUERY ), $query_args );
		$I->assertArrayHasKey( 'shopping_url', $query_args );
		parse_str( parse_url( urldecode( $query_args['shopping_url'] ), PHP_URL_QUERY ), $shopping_url_query_args );
		$I->assertArrayHasKey( 'tpp_invoice', $shopping_url_query_args );
		$I->assertEquals( '123foo', $shopping_url_query_args['tpp_invoice'] );
	}
}
