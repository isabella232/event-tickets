<?php
/**
 * Event Tickets Emails: Purchase Receipt > Intro.
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/tickets/v2/emails/purchase-receipt/intro.php
 *
 * See more documentation about our views templating system.
 *
 * @link https://evnt.is/tickets-emails-tpl Help article for Tickets Emails template files.
 *
 * @version TBD
 *
 * @since TBD
 *
 * @var Tribe_Template $this               Current template object.
 * @var Email_Abstract $email              The email object.
 * @var string         $heading            The email heading.
 * @var string         $title              The email title.
 * @var bool           $preview            Whether the email is in preview mode or not.
 * @var string         $additional_content The email additional content.
 * @var bool           $is_tec_active      Whether `The Events Calendar` is active or not.
 * @var \WP_Post       $order              The order object.
 */

if ( empty( $order ) ) {
	return;
}

$hello = empty( trim( $order->purchaser_name ) ) ?
	__( 'Hello!', 'event-tickets' ) :
	sprintf(
		// Translators: %s - First name of purchaser.
		__( 'Hi, %s!', 'event-tickets' ),
		$order->purchaser_name
	);

?>
<tr>
	<td class="tec-tickets__email-table-content-greeting-container">
		<div>
			<?php echo esc_html( $hello ); ?>
		</div>
		<div>&nbsp;</div>
		<div>
			<?php
				echo esc_html__( 'Below are the details of your recent ticket purchase. Your tickets will arrive in a separate email.', 'event-tickets' );
			?>
		</div>
	</td>
</tr>
