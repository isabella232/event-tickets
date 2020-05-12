<?php
/**
 * This template renders a Single Ticket content
 * composed by Title and Description currently
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/tickets/registration-js/attendees/fields/datetime.php
 *
 * @since  TBD
 *
 * @see Tribe__Tickets_Plus__Meta__Field__Datetime
 */

$required   = isset( $field->required ) && 'on' === $field->required ? true : false;
$option_id  = "tribe-tickets-meta_{$field->slug}_{$ticket->ID}{{data.attendee_id}}";
$field      = (array) $field;
$field_name = 'tribe-tickets-meta[' . $ticket->ID . '][{{data.attendee_id}}][' . esc_attr( $field['slug'] ) . ']';
$disabled   = false;
$classes    = [
	'tribe-common-b1',
	'tribe-field',
	'tribe-tickets__item__attendee__field__datetime',
	'tribe-tickets-meta-required' => $required,
];
?>
<div <?php tribe_classes( $classes ); ?> >
	<label
			class="tribe-common-b2--min-medium tribe-tickets-meta-label"
			for="<?php echo esc_attr( $option_id ); ?>"
	><?php echo wp_kses_post( $field['label'] ); ?><?php tribe_required_label( $required ); ?></label>
	<input
			type="date"
			id="<?php echo esc_attr( $option_id ); ?>"
			class="tribe-common-form-control-datetime__input ticket-meta"
			name="<?php echo esc_attr( $field_name ); ?>"
			value="<?php echo esc_attr( $value ); ?>"
			min="1900-01-01"
			max="<?php echo esc_attr( (int) date_i18n( 'Y' ) + 100 ); ?>-12-31"
			<?php tribe_required( $required ); ?>
			<?php tribe_disabled( $disabled ); ?>
	/>
</div>
