<?php
/**
 * A Controller to register basic functionalities common to all the ticket types handled by the feature.
 *
 * @since   TBD
 *
 * @package TEC\Tickets\Flexible_Tickets;
 */

namespace TEC\Tickets\Flexible_Tickets;

use TEC\Common\Contracts\Provider\Controller;
use TEC\Events_Pro\Custom_Tables\V1\Series\Post_Type as Series_Post_Type;
use TEC\Tickets\Admin\Editor_Data;
use TEC\Tickets\Flexible_Tickets\Templates\Admin_Views;

/**
 * Class Base.
 *
 * @since   TBD
 *
 * @package TEC\Tickets\Flexible_Tickets;
 */
class Base extends Controller {

	/**
	 * {@inheritDoc}
	 *
	 * @since TBD
	 *
	 * @return void
	 */
	protected function do_register(): void {
		$this->container->singleton( Repositories\Ticket_Groups::class, Repositories\Ticket_Groups::class );
		$this->container->singleton( Repositories\Posts_And_Ticket_Groups::class, Repositories\Posts_And_Ticket_Groups::class );

		add_action( 'tec_tickets_ticket_form_main_start', [ $this, 'render_ticket_type_options' ] );
	}

	/**
	 * {@inheritDoc}
	 *
	 * @since TBD
	 *
	 * @return void
	 */
	public function unregister(): void {
		remove_action( 'tec_tickets_ticket_form_main_start', [ $this, 'render_ticket_type_options' ] );
	}

	/**
	 * Renders the ticket type options.
	 *
	 * @since TBD
	 *
	 * @param int $post_id The ID of the post being edited.
	 *
	 * @return void The ticket type options are rendered.
	 */
	public function render_ticket_type_options( $post_id ): void {
		if ( ! (
			is_numeric( $post_id )
			&& ( $post = get_post( $post_id ) ) instanceof \WP_Post
			&& $post->post_type === Series_Post_Type::POSTTYPE
		) ) {
			return;
		}

		$this->container->get( Admin_Views::class )->template( 'ticket-type-options' );
	}
}