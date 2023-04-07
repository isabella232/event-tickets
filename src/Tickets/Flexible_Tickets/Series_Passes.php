<?php
/**
 * Handles the Series Passes integration at different levels.
 *
 * @since TBD
 *
 * @package TEC\Tickets\Flexible_Tickets;
 */

namespace TEC\Tickets\Flexible_Tickets;

use tad_DI52_Container;
use TEC\Common\Provider\Controller;
use TEC\Events_Pro\Custom_Tables\V1\Series\Post_Type as Series_Post_Type;
use TEC\Tickets\Flexible_Tickets\Templates\Admin_Views;
use Tribe__Tickets__Tickets as Tickets;
use Tribe__Tickets__RSVP as RSVP;

/**
 * Class Series_Passes.
 *
 * @since TBD
 *
 * @package TEC\Tickets\Flexible_Tickets;
 */
class Series_Passes extends Controller {

	/**
	 * A reference to the templates handler.
	 *
	 * @since TBD
	 *
	 * @var Admin_Views
	 */
	private Admin_Views $admin_views;

	/**
	 * Series_Passes constructor.
	 *
	 * since TBD
	 *
	 * @param tad_DI52_Container $container The container instance.
	 * @param Admin_Views $admin_views The templates handler.
	 */
	public function __construct(
		tad_DI52_Container $container,
		Admin_Views $admin_views
	) {
		parent::__construct( $container );
		$this->admin_views = $admin_views;
	}

	/**
	 * {@inheritDoc}
	 *
	 * @since TBD
	 *
	 * @return void
	 */
	protected function do_register(): void {
		add_action( 'tribe_events_tickets_new_ticket_buttons', [ $this, 'render_form_toggle' ] );
		add_action( 'tec_tickets_ticket_added_series_pass', [ $this, 'add_pass_custom_tables_data' ], 10, 3 );

	}

	/**
	 * {@inheritDoc}
	 *
	 * @since TBD
	 *
	 * @return void
	 */
	public function unregister(): void {
		remove_action( 'tribe_events_tickets_new_ticket_buttons', [ $this, 'render_form_toggle' ] );
		remove_action( "tec_tickets_ticket_added_series_pass", [ $this, 'add_pass_custom_tables_data' ] );
	}

	/**
	 * Adds the toggle to the new ticket form.
	 *
	 * @since TBD
	 *
	 * @param int $post_id The post ID.
	 *
	 * @return void The toggle is added to the new ticket form.
	 */
	public function render_form_toggle( $post_id ): void {
		if ( ! ( is_numeric( $post_id ) && $post_id > 0 ) ) {
			return;
		}

		$post = get_post( $post_id );

		if ( ! ( $post instanceof \WP_Post && $post->post_type === Series_Post_Type::POSTTYPE ) ) {
			return;
		}

		$ticket_providing_modules = array_diff_key( Tickets::modules(), [ RSVP::class => true ] );
		$this->admin_views->template( 'form-toggle', [
			'disabled' => count( $ticket_providing_modules ) === 0,
		] );
	}

	public function add_pass_custom_tables_data( $post_id, $ticket_id, $ticket_data ): void {
		$check_args = is_int( $post_id ) && $post_id > 0
		              && is_int( $ticket_id ) && $ticket_id > 0
		              && is_array( $ticket_data )
		              && (
			              ( $series = get_post( $post_id ) ) instanceof \WP_Post
			              && $series->post_type === Series_Post_Type::POSTTYPE
		              );

		if ( ! $check_args ) {
			return;
		}

		$ticket = get_post( $ticket_id );
		$ticket_meta = get_post_meta( $ticket_id );
	}
}