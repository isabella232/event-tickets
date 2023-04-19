<?php
/**
 * Event Tickets Emails: Order Ticket Totals - Ticket Title
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/tickets/v2/emails/template-parts/body/order/ticket-totals/ticket-title.php
 *
 * See more documentation about our views templating system.
 *
 * @link https://evnt.is/tickets-emails-tpl Help article for Tickets Emails template files.
 *
 * @version TBD
 *
 * @since TBD
 *
 * @var Tribe__Template                    $this               Current template object.
 * @var \TEC\Tickets\Emails\Email_Abstract $email              The email object.
 * @var string                             $heading            The email heading.
 * @var string                             $title              The email title.
 * @var bool                               $preview            Whether the email is in preview mode or not.
 * @var string                             $additional_content The email additional content.
 * @var bool                               $is_tec_active      Whether `The Events Calendar` is active or not.
 * @var \WP_Post                           $order              The order object.
 */

$ticket_title = empty( $ticket['title'] ) ? '' : $ticket['title'];

?>
<td class="tec-tickets__email-table-content-order-ticket-totals-cell tec-tickets__email-table-content-order-align-left" align="left">
	<?php echo esc_html( $ticket_title ); ?>
</td>
