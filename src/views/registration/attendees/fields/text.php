<?php
/**
 * This template renders a Single Ticket content
 * composed by Title and Description currently
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/tickets/registration/attendees/fields/text.php
 *
 * @since 4.9
 * @since 4.10.1 Update template paths to add the "registration/" prefix
 * @version 4.10.1
 *
 */
$attendee_id   = $key;
$required      = isset( $field->required ) && 'on' === $field->required ? true : false;
$option_id     = "tribe-tickets-meta_{$field->slug}_{$ticket->ID}" . ( $attendee_id ? '_' . $attendee_id : '' );
$field         = (array) $field;
$multiline     = isset( $field['extra'] ) && isset( $field['extra']['multiline'] ) ? $field['extra']['multiline'] : '';
$field_name    = 'tribe-tickets-meta[' . $ticket->ID . '][' . $attendee_id . '][' . esc_attr( $field['slug'] ) . ']';

?>
<div  class="tribe-common-b1 tribe-field tribe-block__tickets__item__attendee__field__text <?php echo $required ? 'tribe-tickets-meta-required' : ''; ?>">
	<label
		class="tribe-tickets-meta-label tribe-common-b1 tribe-common-b2--min-medium"
		for="<?php echo esc_attr( $option_id ); ?>"
	><?php echo wp_kses_post( $field['label'] ); ?><?php tribe_required_label( $required ); ?></label>
	<?php if ( $multiline ) : ?>
		<textarea
			id="<?php echo esc_attr( $option_id ); ?>"
			class="tribe-common-form-control-text__input"
			name="<?php echo esc_attr( $field_name ); ?>"
			<?php tribe_required( $required ); ?>
		><?php echo esc_textarea( $value ); ?></textarea>
	<?php else : ?>
		<input
			type="text"
			id="<?php echo esc_attr( $option_id ); ?>"
			class="tribe-common-form-control-text__input"
			name="<?php echo esc_attr( $field_name ); ?>"
			value="<?php echo esc_attr( $value ); ?>"
			<?php tribe_required( $required ); ?>
		/>
	<?php endif; ?>
</div>
