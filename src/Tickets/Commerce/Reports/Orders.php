<?php

namespace TEC\Tickets\Commerce\Reports;

use TEC\Tickets\Commerce;
use TEC\Tickets\Commerce\Module;
use TEC\Tickets\Commerce\Utils\Price;
use WP_Post;

use Tribe__Tickets__Main as Plugin;
use Tribe__Utils__Array as Arr;


/**
 * Class Orders Report.
 *
 * @since   TBD
 *
 * @package TEC\Tickets\Commerce\Reports
 */
class Orders extends Report_Abstract {

	/**
	 * Slug of the admin page for orders
	 *
	 * @since TBD
	 *
	 * @var string
	 */
	public static $page_slug = 'tickets-commerce-orders';

	/**
	 * @var string
	 */
	public static $tab_slug = 'tribe-tickets-paypal-orders-report';

	/**
	 * @var string The menu slug of the orders page
	 */
	public $orders_page;

	public function get_title() {
		return __( 'Orders Report', 'event-tickets' );
	}

	/**
	 * Links to sales report for all tickets in Tickets Commerce for this event.
	 *
	 * @since TBD
	 *
	 * @param int  $event_id
	 * @param bool $url_only
	 *
	 * @return string
	 */
	public function get_event_link( $event_id, $url_only = false ) {
		$ticket_ids = (array) tribe( Module::class )->get_tickets_ids( $event_id );
		if ( empty( $ticket_ids ) ) {
			return '';
		}
		$post = get_post( $event_id );

		$query = [
			'post_type' => $post->post_type,
			'page'      => static::$page_slug,
			'post_id'   => $event_id,
		];

		$report_url = add_query_arg( $query, admin_url( 'admin.php' ) );

		/**
		 * Filter the PayPal Ticket Orders (Sales) Report URL
		 *
		 * @var string $report_url Report URL
		 * @var int    $event_id   The post ID
		 * @var array  $ticket_ids An array of ticket IDs
		 *
		 * @return string
		 */
		$report_url = apply_filters( 'tribe_tickets_paypal_report_url', $report_url, $event_id, $ticket_ids );

		return $url_only
			? $report_url
			: '<small> <a href="' . esc_url( $report_url ) . '">' . esc_html__( 'Sales report', 'event-tickets' ) . '</a> </small>';
	}

	/**
	 * Links to the sales report for a given ticket.
	 *
	 * @since TBD
	 *
	 * @param int|string $event_id
	 * @param int|string $ticket_id
	 *
	 * @return string
	 */
	public function get_ticket_link( $event_id, $ticket_id ) {
		if ( empty( $ticket_id ) ) {
			return '';
		}
		$post = get_post( $event_id );

		$query = [
			'post_type'   => $post->post_type,
			'page'        => static::$page_slug,
			'product_ids' => $ticket_id,
			'post_id'     => $event_id,
		];

		$report_url = add_query_arg( $query, admin_url( 'admin.php' ) );

		return '<span><a href="' . esc_url( $report_url ) . '">' . esc_html__( 'Report', 'event-tickets' ) . '</a></span>';
	}

	/**
	 * Returns the link to the "Orders" report for this post.
	 *
	 * @since TBD
	 *
	 * @param WP_Post $post
	 *
	 * @return string The absolute URL.
	 */
	public static function get_tickets_report_link( $post ) {
		$url = add_query_arg( [
			'post_type' => $post->post_type,
			'page'      => static::$page_slug,
			'post_id'   => $post->ID,
		], admin_url( 'edit.php' ) );

		return $url;
	}

	/**
	 * Hooks the actions and filter required by the class.
	 *
	 * @since TBD
	 */
	public function hook() {
		add_filter( 'post_row_actions', [ $this, 'add_orders_row_action' ], 10, 2 );
		add_action( 'tribe_tickets_attendees_page_inside', [ $this, 'render_tabbed_view' ] );
		add_action( 'admin_menu', [ $this, 'register_orders_page' ] );

		// register the tabbed view
		$paypal_tabbed_view = new \Tribe__Tickets__Commerce__PayPal__Orders__Tabbed_View();
		$paypal_tabbed_view->register();
	}

	/**
	 * Adds order related actions to the available row actions for the post.
	 *
	 * @since TBD
	 *
	 * @param array $actions
	 * @param       $post
	 *
	 * @return array
	 */
	public function add_orders_row_action( array $actions, $post ) {
		$post_id = \Tribe__Main::post_id_helper( $post );
		$post    = get_post( $post_id );

		// only if tickets are active on this post type
		if ( ! in_array( $post->post_type, Plugin::instance()->post_types(), true ) ) {
			return $actions;
		}
		$commerce = tribe( Module::class );

		if ( ! $commerce->post_has_tickets( $post ) ) {
			return $actions;
		}

		$url         = $commerce->get_event_reports_link( $post->ID, true );
		$post_labels = get_post_type_labels( get_post_type_object( $post->post_type ) );
		$post_type   = strtolower( $post_labels->singular_name );

		$actions['tickets_orders'] = sprintf(
			'<a title="%s" href="%s">%s</a>',
			sprintf( esc_html__( 'See PayPal purchases for this %s', 'event-tickets' ), $post_type ),
			esc_url( $url ),
			esc_html__( 'PayPal Orders', 'event-tickets' )
		);

		return $actions;
	}

	/**
	 * Renders the tabbed view header before the report.
	 *
	 * @since TBD
	 *
	 * @param \Tribe__Tickets__Attendees $attendees
	 */
	public function render_tabbed_view( \Tribe__Tickets__Attendees $attendees ) {
		$post = $attendees->get_post();

		$commerce = tribe( Module::class );

		$has_tickets = $commerce->get_attendees_count( $post->ID );
		if ( ! $has_tickets ) {
			return;
		}

		$tabbed_view = new \Tribe__Tickets__Commerce__PayPal__Orders__Tabbed_View();
		$tabbed_view->register();
	}

	/**
	 * Registers the PayPal orders page as a plugin options page.
	 *
	 * @since TBD
	 */
	public function register_orders_page() {
		$candidate_post_id = tribe_get_request_var( 'post_id', 0 );
		$candidate_post_id = tribe_get_request_var( 'event_id', $candidate_post_id );

		if ( ( $post_id = absint( $candidate_post_id ) ) != $candidate_post_id ) {
			return;
		}

		$cap = 'edit_posts';
		if ( ! current_user_can( 'edit_posts' ) && $post_id ) {
			$post = get_post( $post_id );

			if ( $post instanceof WP_Post && get_current_user_id() === (int) $post->post_author ) {
				$cap = 'read';
			}
		}

		$page_title        = __( 'PayPal Orders', 'event-tickets' );
		$this->orders_page = add_submenu_page(
			null,
			$page_title,
			$page_title,
			$cap,
			static::$page_slug,
			[ $this, 'render_page' ]
		);

		/** @var \Tribe__Tickets__Attendees $attendees */
		$attendees = tribe( 'tickets.attendees' );

		add_filter( 'tribe_filter_attendee_page_slug', [ $this, 'add_attendee_resources_page_slug' ] );
		add_action( 'admin_enqueue_scripts', [ $attendees, 'enqueue_assets' ] );
		add_action( 'admin_enqueue_scripts', [ $attendees, 'load_pointers' ] );
		add_action( 'load-' . $this->orders_page, [ $this, 'attendees_page_screen_setup' ] );
	}

	/**
	 * Filter the page slugs that the attendee resources will load to add the order page
	 *
	 * @since TBD
	 *
	 * @param $slugs
	 *
	 * @return array
	 */
	public function add_attendee_resources_page_slug( $slugs ) {
		$slugs[] = $this->orders_page;

		return $slugs;
	}

	/**
	 * Sets up the attendees page screen.
	 *
	 * @since TBD
	 */
	public function attendees_page_screen_setup() {
		$orders_table = tribe( Commerce\Admin_Tables\Orders::class );
		$orders_table->prepare_items();

		wp_enqueue_script( 'jquery-ui-dialog' );

		add_filter( 'admin_title', [ $this, 'filter_admin_title' ] );
	}

	/**
	 * Sets the browser title for the Orders admin page.
	 *
	 * @since TBD
	 *
	 * @param string $admin_title
	 *
	 * @return string
	 */
	public function filter_admin_title( $admin_title ) {
		if ( ! empty( $_GET['post_id'] ) ) {
			$event       = get_post( $_GET['post_id'] );
			$admin_title = sprintf( esc_html_x( '%s - Tickets Commerce Orders', 'Browser title', 'event-tickets' ), $event->post_title );
		}

		return $admin_title;
	}

	/**
	 * Renders the order page
	 *
	 * @since TBD
	 */
	public function render_page() {
		$this->get_template()->template( 'orders', $this->get_template_vars() );
	}

	/**
	 * Sets up the template variables used to render the Orders Report Page.
	 *
	 * @since TBD
	 *
	 * @return array
	 */
	public function setup_template_vars() {
		$post_id = tribe_get_request_var( 'post_id' );
		$post_id = tribe_get_request_var( 'event_id', $post_id );
		$post    = get_post( $post_id );

		$post_type_object    = get_post_type_object( $post->post_type );
		$post_singular_label = $post_type_object->labels->singular_name;

		$tickets    = \Tribe__Tickets__Tickets::get_event_tickets( $post_id );
		$ticket_ids = tribe_get_request_var( 'product_ids', false );

		if ( false !== $ticket_ids ) {
			$ticket_ids = array_map( 'absint', explode( ',', $ticket_ids ) );
			$ticket_ids = array_filter( $ticket_ids, static function ( $ticket_id ) {
				return get_post_type( $ticket_id ) === Commerce\Ticket::POSTTYPE;
			} );
			$tickets    = array_map( [ tribe( Commerce\Ticket::class ), 'get_ticket' ], $ticket_ids );
		}

		$tickets = array_filter( $tickets, static function ( $ticket ) {
			return Module::class === $ticket->provider_class;
		} );

		$event_data   = [];
		$tickets_data = [];

		foreach ( $tickets as $ticket ) {
			$quantities      = tribe( Commerce\Ticket::class )->get_status_quantity( $ticket->ID );
			$total_by_status = [];
			foreach ( $quantities as $status_slug => $status_count ) {
				if ( ! isset( $event_data['qty_by_status'][ $status_slug ] ) ) {
					$event_data['qty_by_status'][ $status_slug ] = 0;
				}
				if ( ! isset( $event_data['total_by_status'][ $status_slug ] ) ) {
					$event_data['total_by_status'][ $status_slug ] = [];
				}

				$event_data['total_by_status'][ $status_slug ][] = $total_by_status[ $status_slug ] = Price::sub_total( $ticket->price, $status_count );

				$event_data['qty_by_status'][ $status_slug ] += (int) $status_count;
			}
			$tickets_data[ $ticket->ID ] = [
				'total_by_status' => $total_by_status,
				'qty_by_status'   => $quantities,
			];
		}

		$event_data['total_by_status'] = array_map( static function ( $sub_totals ) {
			return Price::total( $sub_totals );
		}, $event_data['total_by_status'] );


		$this->template_vars = [
			'title'               => $this->get_title(),
			'orders_table'        => tribe( Commerce\Admin_Tables\Orders::class ),
			'post'                => $post,
			'post_id'             => $post_id,
			'post_type_object'    => $post_type_object,
			'post_singular_label' => $post_singular_label,
			'tickets'             => $tickets,
			'tickets_data'        => $tickets_data,
			'event_data'          => $event_data,
			'tooltip'             => tribe( 'tooltip.view' ),
		];

		return $this->template_vars;
	}


}