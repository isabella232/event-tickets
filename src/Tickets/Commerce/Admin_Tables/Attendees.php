<?php

namespace TEC\Tickets\Commerce\Admin_Tables;

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/screen.php' );
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

use WP_List_Table;

class Attendees extends WP_List_Table {

	public function __construct( $args = array() ) {
		parent::__construct( $args );
	}

	/**
	 * Enqueues the JS and CSS for the attendees page in the admin
	 *
	 * @since 4.6.2
	 *
	 * @todo this needs to use tribe_assets()
	 *
	 * @param $hook
	 */
	public function enqueue_assets( $hook ) {
		/**
		 * Filter the Page Slugs the Attendees Page CSS and JS Loads
		 *
		 * @param array array( $this->page_id ) an array of admin slugs
		 */
		if ( ! in_array( $hook, apply_filters( 'tribe_filter_attendee_page_slug', array( $this->page_id ) ) ) ) {
			return;
		}

		$resources_url = plugins_url( 'src/resources', dirname( dirname( __FILE__ ) ) );

		wp_enqueue_style( 'tickets-report-css', $resources_url . '/css/tickets-report.css', array(), \Tribe__Tickets__Main::instance()->css_version() );
		wp_enqueue_style( 'tickets-report-print-css', $resources_url . '/css/tickets-report-print.css', array(), \Tribe__Tickets__Main::instance()->css_version(), 'print' );
		wp_enqueue_script( $this->slug() . '-js', $resources_url . '/js/tickets-attendees.js', array( 'jquery' ), \Tribe__Tickets__Main::instance()->js_version() );

		add_thickbox();

		$move_url_args = [
			'dialog'    => \Tribe__Tickets__Main::instance()->move_tickets()->dialog_name(),
			'check'     => wp_create_nonce( 'move_tickets' ),
			'TB_iframe' => 'true',
		];

		$config_data = [
			'nonce'             => wp_create_nonce( 'email-attendee-list' ),
			'required'          => esc_html__( 'You need to select a user or type a valid email address', 'event-tickets' ),
			'sending'           => esc_html__( 'Sending...', 'event-tickets' ),
			'ajaxurl'           => admin_url( 'admin-ajax.php' ),
			'checkin_nonce'     => wp_create_nonce( 'checkin' ),
			'uncheckin_nonce'   => wp_create_nonce( 'uncheckin' ),
			'cannot_move'       => esc_html__( 'You must first select one or more tickets before you can move them!', 'event-tickets' ),
			'move_url'          => add_query_arg( $move_url_args ),
			'confirmation'      => esc_html__( 'Please confirm that you would like to delete this attendee.', 'event-tickets' ),
			'bulk_confirmation' => esc_html__( 'Please confirm you would like to delete these attendees.', 'event-tickets' ),
		];

		/**
		 * Allow filtering the configuration data for the Attendee objects on Attendees report page.
		 *
		 * @since 5.0.4
		 *
		 * @param array $config_data List of configuration data to be localized.
		 */
		$config_data = apply_filters( 'tribe_tickets_attendees_report_js_config', $config_data );

		wp_localize_script( $this->slug() . '-js', 'Attendees', $config_data );
	}

	/**
	 * Loads the WP-Pointer for the Attendees screen
	 *
	 * @since 4.6.2
	 *
	 * @param $hook
	 */
	public function load_pointers( $hook ) {
		if ( $hook != $this->page_id ) {
		//	return;
		}

		$dismissed = explode( ',', (string) get_user_meta( get_current_user_id(), 'dismissed_wp_pointers', true ) );
		$pointer   = [];

		if ( version_compare( get_bloginfo( 'version' ), '3.3', '>' ) && ! in_array( 'attendees_filters', $dismissed ) ) {
			$pointer = array(
				'pointer_id' => 'attendees_filters',
				'target'     => '#screen-options-link-wrap',
				'options'    => array(
					'content' => sprintf( '<h3> %s </h3> <p> %s </p>', esc_html__( 'Columns', 'event-tickets' ), esc_html__( 'You can use Screen Options to select which columns you want to see. The selection works in the table below, in the email, for print and for the CSV export.', 'event-tickets' ) ),
					'position' => array( 'edge' => 'top', 'align' => 'right' ),
				),
			);
			wp_enqueue_script( 'wp-pointer' );
			wp_enqueue_style( 'wp-pointer' );
		}

		wp_localize_script( $this->slug() . '-js', 'AttendeesPointer', $pointer );
	}

	/**
	 * Returns the  list of columns.
	 *
	 * @return array An associative array in the format [ <slug> => <title> ]
	 * @since TBD
	 *
	 */
	public function get_columns() {
		$columns = [
			'ticket'        => __( 'Ticket', 'event-tickets' ),
			'primary_info'  => __( 'Primary Information', 'event-tickets' ),
			'security_code' => __( 'Security Code', 'event-tickets' ),
			'status'        => __( 'Status', 'event-tickets' ),
			'check_in'      => __( 'Check In', 'event-tickets' ),
		];

		return $columns;
	}


}