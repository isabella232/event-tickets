<?php
/**
 * Series summary for attendee page.
 *
 * @since TBD
 *
 * @version TBD
 *
 * @var string $title The title with link for series.
 * @var string $edit_link The edit link for series.
 * @var array $action_links Array of action links for series.
 */
?>

<li class="post-type series-summary">
	<strong><?php echo esc_html__( 'Series', 'event-tickets' ); ?> : </strong>
	<?php echo wp_kses( $edit_link, [
		'a' => [
			'href'   => [],
			'target' => [],
		],
	] ); ?>
</li>
<li class="series-actions">
	<?php echo implode( ' | ', $action_links ); ?>
</li>
