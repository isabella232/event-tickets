<?php
/**
 * The Template for displaying the Tickets Commerce Stripe connection details.
 *
 * @version TBD
 *
 * @since TBD
 *
 * @var Tribe__Tickets__Admin__Views                  $this               [Global] Template object.
 * @var string                                        $plugin_url         [Global] The plugin URL.
 * @var TEC\Tickets\Commerce\Gateways\Stripe\Merchant $merchant           [Global] The merchant class.
 * @var TEC\Tickets\Commerce\Gateways\Stripe\Signup   $signup             [Global] The Signup class.
 * @var bool                                          $is_merchant_active    [Global] Whether the merchant is active or not.
 * @var bool                                          $is_merchant_connected [Global] Whether the merchant is connected or not.
 */

if ( empty( $is_merchant_connected ) ) {
	return;
}
?>

<div class="tec-tickets__admin-settings-tickets-commerce-stripe-connected-actions">
	<?php $this->template( 'settings/tickets-commerce/stripe/connect/active/actions/refresh-connection' ); ?>

	<div class="tec-tickets__admin-settings-tickets-commerce-stripe-connected-actions-debug">
		<?php $this->template( 'settings/tickets-commerce/stripe/connect/active/actions/refresh-access-token' ); ?>

		<?php $this->template( 'settings/tickets-commerce/stripe/connect/active/actions/refresh-user-info' ); ?>

		<?php $this->template( 'settings/tickets-commerce/stripe/connect/active/actions/refresh-webhook' ); ?>
	</div>
</div>
