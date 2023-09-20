<?php
/**
 * My TIckets Page
 * 
 * Override this template in your own theme by creating a file at [your-theme]/tribe-events/tickets/my-tickets.php
 * 
 * @since TBD
 * 
 * @version TBD
 *
 * @var  array  $orders              The orders for the current user.
 * @var  int    $post_id             The ID of the post the tickets are for.
 * @var  string $title               The title of the ticket section.
 *
 */



?>
<div class="tribe-tickets">
	<?php 
		$this->template( 'tickets/my-tickets/orders-list' ); 
	?>
</div>