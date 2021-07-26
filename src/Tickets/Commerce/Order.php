<?php

namespace TEC\Tickets\Commerce;

/**
 * Class Order
 *
 * @since   TBD
 *
 * @package TEC\Tickets\Commerce
 */
class Order {
	/**
	 * Tickets Commerce Order Post Type slug.
	 *
	 * @since TBD
	 *
	 * @var string
	 */
	const POSTTYPE = 'tec_tc_order';

	/**
	 * Which meta holds the cart items used to setup this order.
	 *
	 * @since TBD
	 *
	 * @var string
	 */
	public static $cart_items_meta_key = '_tec_tc_order_cart_items';

	/**
	 * Which meta holds the cart items used to setup this order.
	 *
	 * @since TBD
	 *
	 * @var string
	 */
	public static $total_value_meta_key = '_tec_tc_order_total_value';

	/**
	 * Which meta holds the cart items used to setup this order.
	 *
	 * @since TBD
	 *
	 * @var string
	 */
	public static $currency_meta_key = '_tec_tc_order_currency';

	/**
	 * Which meta holds the purchaser full name.
	 *
	 * @since TBD
	 *
	 * @var string
	 */
	public static $purchaser_full_name_meta_key = '_tec_tc_order_purchaser_full_name';

	/**
	 * Which meta holds the purchaser first name.
	 *
	 * @since TBD
	 *
	 * @var string
	 */
	public static $purchaser_first_name_meta_key = '_tec_tc_order_purchaser_first_name';

	/**
	 * Which meta holds the purchaser last name.
	 *
	 * @since TBD
	 *
	 * @var string
	 */
	public static $purchaser_last_name_meta_key = '_tec_tc_order_purchaser_last_name';

	/**
	 * Which meta holds the cart items used to setup this order.
	 *
	 * @since TBD
	 *
	 * @var string
	 */
	public static $purchaser_email_meta_key = '_tec_tc_order_purchaser_email';

	/**
	 * Register this Class post type into WP.
	 *
	 * @since TBD
	 */
	public function register_post_type() {
		$post_type_args = [
			'label'           => __( 'Orders', 'event-tickets' ),
			'public'          => false,
			'show_ui'         => false,
			'show_in_menu'    => false,
			'query_var'       => false,
			'rewrite'         => false,
			'capability_type' => 'post',
			'has_archive'     => false,
			'hierarchical'    => false,
		];

		/**
		 * Filter the arguments that craft the order post type.
		 *
		 * @see   register_post_type
		 *
		 * @since TBD
		 *
		 * @param array $post_type_args Post type arguments, passed to register_post_type()
		 */
		$post_type_args = apply_filters( 'tec_tickets_commerce_order_post_type_args', $post_type_args );

		register_post_type( static::POSTTYPE, $post_type_args );

		Order_Statuses::register_order_statuses();
	}

	/**
	 * Returns the list of PayPal tickets order stati.
	 *
	 * @since TBD
	 *
	 * @return array An associative array in the [ <slug> => <label> ] format.
	 */
	public function get_order_statuses() {
		/** @var \Tribe__Tickets__Status__Manager $status_mgr */
		$status_mgr = tribe( 'tickets.status' );

		$statuses       = $status_mgr->get_all_provider_statuses( \TEC\Tickets\Commerce::ABBR );
		$order_statuses = [];
		foreach ( $statuses as $status ) {
			$order_statuses[ $status->provider_name ] = _x( $status->name, 'a PayPal ticket order status', 'event-tickets' );
		}

		/**
		 * Filters the list of PayPal tickets order stati.
		 *
		 * @since 4.7
		 *
		 * @param array $order_statuses
		 *
		 * @return array An associative array in the [ <slug> => <label> ] format.
		 */
		return apply_filters( 'tribe_tickets_commerce_paypal_order_stati', $order_statuses );
	}

	/**
	 * Returns a list of attendees grouped by order.
	 *
	 * @since TBD
	 *
	 * @param int   $post_id
	 * @param array $ticket_ids An optional array of ticket IDs to limit the orders by.
	 *
	 * @return array An associative array in the format [ <order_number> => <order_details> ]
	 */
	public function get_orders_by_post_id( $post_id, array $ticket_ids = null, $args = array() ) {
		$find_by_args = wp_parse_args( $args, array(
			'post_id'   => $post_id,
			'ticket_id' => $ticket_ids,
		) );

		$orders = \Tribe__Tickets__Commerce__PayPal__Order::find_by( $find_by_args );

		$found    = array();
		$statuses = $this->get_order_statuses();

		if ( ! empty( $orders ) ) {
			/** @var \Tribe__Tickets__Commerce__PayPal__Order $order */
			foreach ( $orders as $order ) {
				$order_id        = $order->paypal_id();
				$status          = $order->get_status();
				$attendees       = $order->get_attendees();
				$refund_order_id = $order->get_refund_order_id();

				$found[ $order_id ] = array(
					'url'             => tribe( 'tickets.commerce.paypal.gateway' )->get_transaction_url( $order_id ),
					'number'          => $order_id,
					'status'          => $status,
					'status_label'    => \Tribe__Utils__Array::get( $statuses, $status, Order_Statuses::$undefined ),
					'purchaser_name'  => $order->get_meta( 'address_name' ),
					'purchaser_email' => $order->get_meta( 'payer_email' ),
					'purchase_time'   => $order->get_meta( 'payment_date' ),
					'attendees'       => $attendees,
					'items'           => $order->get_meta( 'items' ),
					'line_total'      => $order->get_line_total(),
				);

				if ( ! empty( $refund_order_id ) ) {
					$found[ $order_id ]['refund_number'] = $refund_order_id;
					$found[ $order_id ]['refund_url']    = tribe( 'tickets.commerce.paypal.gateway' )->get_transaction_url( $refund_order_id );
				}
			}
		}

		return $found;
	}

	/**
	 * Retrieve only order related information
	 * Important: On PayPal Ticket the order is the Attendee Object
	 *
	 *     order_id
	 *     purchaser_name
	 *     purchaser_email
	 *     provider
	 *     provider_slug
	 *
	 * @since 4.7
	 *
	 * @param string|int $order_id
	 *
	 * @return array
	 */
	public function get_order_data( $order_id ) {
		$name  = tribe( Attendee::class )->get_full_name( $order_id );
		$email = get_post_meta( $order_id, $this->email, true );

		$order = \Tribe__Tickets__Commerce__PayPal__Order::from_attendee_id(
			$order_id,
			[
				'address_name',
				'payer_email',
			]
		);

		if ( $order ) {
			$name  = $order->get_meta( 'address_name' );
			$email = $order->get_meta( 'payer_email' );
		}

		$data = array(
			'order_id'        => $order_id,
			'purchaser_name'  => $name,
			'purchaser_email' => $email,
			'provider'        => __CLASS__,
			'provider_slug'   => 'tpp',
			'purchase_time'   => get_post_time( \Tribe__Date_Utils::DBDATETIMEFORMAT, false, $order_id ),
		);

		/**
		 * Allow users to filter the Order Data
		 *
		 * @since 4.7
		 *
		 * @param array  $data     An associative array with the Information of the Order
		 * @param string $provider What Provider is been used
		 * @param string $order_id Order ID
		 *
		 */
		$data = apply_filters( 'tribe_tickets_order_data', $data, 'tpp', $order_id );

		return $data;
	}

	public function create_from_cart() {
		$cart = tribe( Cart::class );

		$items_in_cart = $cart->get_tickets_in_cart();


		$order_args = [
			'title'       => 'Order Test',
			'purchaser'   => 1,
			'total_value' => 30,
			'currency'    => 'USD',
			'tickets'     => [
				30 => 2
			]
		];
		$order      = tec_tc_orders()->set_args( $order_args )->create();

		// We were unable to create the order bail from here.
		if ( ! $order ) {
			return false;
		}
	}


	/**
	 * Redirects to the source post after a recoverable (logic) error.
	 *
	 * @todo  Determine if redirecting should be something relegated to some other method, and here we just actually
	 *       generate the order/Attendees.
	 *
	 * @see   \Tribe__Tickets__Commerce__PayPal__Errors for error codes translations.
	 * @since TBD
	 *
	 * @param bool $redirect   Whether to really redirect or not.
	 * @param int  $post_id    A post ID
	 *
	 * @param int  $error_code The current error code
	 *
	 */
	protected function redirect_after_error( $error_code, $redirect, $post_id ) {
		$url = add_query_arg( 'tpp_error', $error_code, get_permalink( $post_id ) );
		if ( $redirect ) {
			wp_redirect( esc_url_raw( $url ) );
		}
		tribe_exit();
	}

	/**
	 * Generate and store all the attendees information for a new order.
	 *
	 * @since TBD
	 *
	 * @param string $payment_status The tickets payment status, defaults to completed.
	 * @param bool   $redirect       Whether the client should be redirected or not.
	 *
	 */
	public function generate_order( $payment_status = 'completed', $redirect = true ) {
		/** @var \Tribe__Tickets__Commerce__PayPal__Gateway $gateway */
		$gateway = tribe( 'tickets.commerce.paypal.gateway' );

		$transaction_data = $gateway->get_transaction_data();

		/** @var \Tribe__Tickets__Commerce__PayPal__Cart__Interface $cart */
		$cart = tribe( 'tickets.commerce.paypal.cart' );

		/**
		 * The `invoice` variable is a passthrough one; if passed when adding items to the cart
		 * then it should be returned to us from PayPal. If we have it in the transaction data
		 * we can assume the cart associated with the invoice, if any, can be removed.
		 *
		 * @link https://developer.paypal.com/docs/classic/paypal-payments-standard/integration-guide/formbasics/#variations-on-basic-variables
		 */
		if ( ! empty( $transaction_data['custom'] ) ) {
			$decoded_custom = \Tribe__Tickets__Commerce__PayPal__Custom_Argument::decode( $transaction_data['custom'], true );
			if ( isset( $decoded_custom['invoice'] ) ) {
				$cart->set_id( $decoded_custom['invoice'] );
				$cart->clear();
			}
		}

		$raw_transaction_data = $gateway->get_raw_transaction_data();

		if ( empty( $transaction_data ) || empty( $transaction_data['items'] ) ) {
			return;
		}

		$has_tickets = $post_id = false;

		/**
		 * PayPal Ticket specific action fired just before a PayPalTicket-driven attendee tickets for an order are generated
		 *
		 * @since 4.7
		 *
		 * @param array $transaction_data PayPal payment data
		 */
		do_action( 'tribe_tickets_tpp_before_order_processing', $transaction_data );

		$order_id = $transaction_data['txn_id'];

		$is_refund = Order_Statuses::$refunded === $payment_status
		             || 'refund' === \Tribe__Utils__Array::get( $transaction_data, 'reason_code', '' );

		if ( $is_refund ) {
			$transaction_data['payment_status'] = $payment_status = Order_Statuses::$refunded;

			$refund_order_id = $order_id;
			$order_id        = \Tribe__Utils__Array::get( $transaction_data, 'parent_txn_id', $order_id );
			$order           = \Tribe__Tickets__Commerce__PayPal__Order::from_order_id( $order_id );

			$order->refund_with( $refund_order_id );

			unset( $transaction_data['txn_id'], $transaction_data['parent_txn_id'] );

			$order->hydrate_from_transaction_data( $transaction_data );
		} else {
			$order = \Tribe__Tickets__Commerce__PayPal__Order::from_transaction_data( $transaction_data );
		}

		$order->set_meta( 'transaction_data', $raw_transaction_data );

		$custom = \Tribe__Tickets__Commerce__PayPal__Custom_Argument::decode( $transaction_data['custom'], true );

		/*
		 * This method might run during a POST (IPN) PayPal request hence the
		 * purchasing user ID, if any, will be stored in a custom PayPal var.
		 * Let's fallback on the current user ID for GET requests (PDT); it will be always `0`
		 * during a PayPal POST (IPN) request.
		 */
		$attendee_user_id = ! isset( $custom['user_id'] ) ? get_current_user_id() : absint( $custom['user_id'] );

		$attendee_full_name = empty( $transaction_data['first_name'] ) && empty( $transaction_data['last_name'] )
			? ''
			: sanitize_text_field( "{$transaction_data['first_name']} {$transaction_data['last_name']}" );

		$attendee_email = empty( $transaction_data['payer_email'] ) ? null : sanitize_email( $transaction_data['payer_email'] );
		$attendee_email = is_email( $attendee_email ) ? $attendee_email : null;

		if ( ! empty( $attendee_user_id ) ) {
			$attendee = get_user_by( 'id', $attendee_user_id );

			// Check if the user was found.
			if ( $attendee ) {
				// Check if the user has an email address.
				if ( $attendee->user_email ) {
					$attendee_email = $attendee->user_email;
				}

				$user_full_name = trim( "{$attendee->first_name} {$attendee->last_name}" );

				// Check if the user has first/last name.
				if ( ! empty( $user_full_name ) ) {
					$attendee_full_name = $user_full_name;
				}
			}
		}

		/**
		 * This is an array of tickets IDs for which the user decided to opt-out.
		 *
		 * @see \Tribe__Tickets_Plus__Commerce__PayPal__Attendees::register_optout_choice()
		 */
		$attendee_optouts = \Tribe__Utils__Array::get( $custom, 'oo', [] );

		if ( ! $attendee_email || ! $attendee_full_name ) {
			$this->redirect_after_error( 101, $redirect, $post_id );

			return;
		}

		// Iterate over each product
		foreach ( (array) $transaction_data['items'] as $item ) {
			$order_attendee_id = 0;

			if ( empty( $item['ticket'] ) ) {
				continue;
			}

			/** @var \Tribe__Tickets__Ticket_Object $ticket_type */
			$ticket_type = $item['ticket'];
			$product_id  = $ticket_type->ID;

			// Get the event this tickets is for
			$post = $ticket_type->get_event();

			if ( empty( $post ) ) {
				continue;
			}

			$post_id = $post->ID;

			// if there were no PayPal tickets for the product added to the cart, continue
			if ( empty( $item['quantity'] ) ) {
				continue;
			}

			// get the PayPal status `decrease_stock_by` value
			$status_stock_size = 1;

			$ticket_qty = (int) $item['quantity'];

			// to avoid tickets from not being created on a status stock size of 0
			// let's take the status stock size into account and create a number of tickets
			// at least equal to the number of tickets the user requested
			$ticket_qty = $status_stock_size < 1 ? $ticket_qty : $status_stock_size * $ticket_qty;

			$qty = max( $ticket_qty, 0 );

			// Throw an error if Qty is bigger then Remaining
			if ( $payment_status === Order_Statuses::$completed && $ticket_type->managing_stock() ) {
				tribe( Module::class )->ignore_pending_stock_logic( true );
				$inventory = (int) $ticket_type->inventory();
				tribe( Module::class )->ignore_pending_stock_logic( false );

				$inventory_is_not_unlimited = - 1 !== $inventory;

				if ( $inventory_is_not_unlimited && $qty > $inventory ) {
					if ( ! $order->was_pending() ) {
						$this->redirect_after_error( 102, $redirect, $post_id );

						return;
					}

					/** @var \Tribe__Tickets__Commerce__PayPal__Oversell__Policies $oversell_policies */
					$oversell_policies = tribe( 'tickets.commerce.paypal.oversell.policies' );
					$oversell_policy   = $oversell_policies->for_post_ticket_order( $post_id, $ticket_type->ID, $order_id );

					$qty = $oversell_policy->modify_quantity( $qty, $inventory );

					if ( ! $oversell_policy->allows_overselling() ) {
						$oversold_attendees = tribe( Module::class )->get_attendees_by_order_id( $order_id );
						$oversell_policy->handle_oversold_attendees( $oversold_attendees );
						$this->redirect_after_error( 102, $redirect, $post_id );

						return;
					}
				}
			}

			if ( $qty === 0 ) {
				$this->redirect_after_error( 103, $redirect, $post_id );

				return;
			}

			$has_tickets = true;

			/**
			 * PayPal specific action fired just before a PayPal-driven attendee ticket for an event is generated
			 *
			 * @since 4.7
			 *
			 * @param int    $post_id     ID of event
			 * @param string $ticket_type Ticket Type object for the product
			 * @param array  $data        Parsed PayPal transaction data
			 */
			do_action( 'tribe_tickets_tpp_before_attendee_ticket_creation', $post_id, $ticket_type, $transaction_data );

			$existing_attendees = tribe( Module::class )->get_attendees_by_order_id( $order_id );

			$has_generated_new_tickets = false;

			/** @var \Tribe__Tickets__Commerce__Currency $currency */
			$currency        = tribe( 'tickets.commerce.currency' );
			$currency_symbol = $currency->get_currency_symbol( $product_id, true );

			// Iterate over all the amount of tickets purchased (for this product)
			for ( $i = 0; $i < $qty; $i ++ ) {
				$attendee_id       = null;
				$updating_attendee = false;

				/**
				 * Allow filtering the individual attendee name used when creating a new attendee.
				 *
				 * @since 5.0.3
				 *
				 * @param string                   $individual_attendee_name The attendee full name.
				 * @param int|null                 $attendee_number          The attendee number index value from the order, starting with zero.
				 * @param int                      $order_id                 The order ID.
				 * @param int                      $ticket_id                The ticket ID.
				 * @param int                      $post_id                  The ID of the post associated to the ticket.
				 * @param \Tribe__Tickets__Tickets $provider                 The current ticket provider object.
				 */
				$individual_attendee_name = apply_filters( 'tribe_tickets_attendee_create_individual_name', $attendee_full_name, $i, $order_id, $product_id, $post_id, $this );

				/**
				 * Allow filtering the individual attendee email used when creating a new attendee.
				 *
				 * @since 5.0.3
				 *
				 * @param string                   $individual_attendee_email The attendee email.
				 * @param int|null                 $attendee_number           The attendee number index value from the order, starting with zero.
				 * @param int                      $order_id                  The order ID.
				 * @param int                      $ticket_id                 The ticket ID.
				 * @param int                      $post_id                   The ID of the post associated to the ticket.
				 * @param \Tribe__Tickets__Tickets $provider                  The current ticket provider object.
				 */
				$individual_attendee_email = apply_filters( 'tribe_tickets_attendee_create_individual_email', $attendee_email, $i, $order_id, $product_id, $post_id, $this );

				// check if we already have an attendee or not
				$post_title        = $individual_attendee_name . ' | ' . ( $i + 1 );
				$criteria          = [ 'post_title' => $post_title, 'product_id' => $product_id, 'event_id' => $post_id ];
				$existing_attendee = wp_list_filter( $existing_attendees, $criteria );

				if ( ! empty( $existing_attendee ) ) {
					$existing_attendee = reset( $existing_attendee );
					$updating_attendee = true;
					$attendee_id       = $existing_attendee['attendee_id'];
					$attendee          = [];
				} else {
					$attendee = [
						'post_title' => $post_title,
					];

					// since we are creating at least one
					$has_generated_new_tickets = true;
				}

				$attendee_order_status = trim( strtolower( $payment_status ) );

				$repository = tribe_attendees( $this->orm_provider );

				$data = $attendee;

				$data['order_attendee_id'] = $order_attendee_id;
				$data['attendee_status']   = $attendee_order_status;

				if ( Order_Statuses::$refunded === $payment_status ) {
					$refund_order_id = \Tribe__Utils__Array::get( $transaction_data, 'txn_id', '' );

					$data['refund_order_id'] = $refund_order_id;
				}

				if ( ! $updating_attendee ) {
					$optout = \Tribe__Utils__Array::get( $attendee_optouts, 'ticket_' . $product_id, false );
					$optout = filter_var( $optout, FILTER_VALIDATE_BOOLEAN );
					$optout = $optout ? 'yes' : 'no';

					$data['ticket_id']      = $product_id;
					$data['post_id']        = $post_id;
					$data['order_id']       = $order_id;
					$data['optout']         = $optout;
					$data['full_name']      = $individual_attendee_name;
					$data['email']          = $individual_attendee_email;
					$data['price_paid']     = get_post_meta( $product_id, '_price', true );
					$data['price_currency'] = $currency_symbol;

					if ( 0 < $attendee_user_id ) {
						$data['user_id'] = $attendee_user_id;
					}

					$attendee_object = tribe( Module::class )->create_attendee( $ticket_type, $data );
					$attendee_id     = $attendee_object->ID;

				} else {
					// Update attendee.
					tribe( Module::class )->update_attendee( $attendee_id, $data );
				}

				$order->add_attendee( $attendee_id );

				$order_attendee_id ++;

				if ( ! empty( $existing_attendee ) ) {
					$existing_attendees = wp_list_filter( $existing_attendees, array( 'attendee_id' => $existing_attendee['attendee_id'] ), 'NOT' );
				}
			}

			if ( ! ( empty( $existing_attendees ) || empty( $oversell_policy ) ) ) {
				// an oversell policy applied: what to do with existing oversold attendees?
				$oversell_policy->handle_oversold_attendees( $existing_attendees );
			}

			if ( $has_generated_new_tickets ) {
				/**
				 * Action fired when a PayPal has had attendee tickets generated for it.
				 *
				 * @since 4.7
				 *
				 * @param int    $product_id PayPal ticket post ID
				 * @param string $order_id   ID of the PayPal order
				 * @param int    $qty        Quantity ordered
				 */
				do_action( 'event_tickets_tpp_tickets_generated_for_product', $product_id, $order_id, $qty );
			}

			/**
			 * Action fired when a PayPal has had attendee tickets updated for it.
			 *
			 * This will fire even when tickets are initially created; if you need to hook on the
			 * creation process only use the 'event_tickets_tpp_tickets_generated_for_product' action.
			 *
			 * @since 4.7
			 *
			 * @param int    $product_id PayPal ticket post ID
			 * @param string $order_id   ID of the PayPal order
			 * @param int    $qty        Quantity ordered
			 */
			do_action( 'event_tickets_tpp_tickets_generated_for_product', $product_id, $order_id, $qty );

			// After Adding the Values we Update the Transient
			\Tribe__Post_Transient::instance()->delete( $post_id, \Tribe__Tickets__Tickets::ATTENDEES_CACHE );
		}

		$order->update();

		/**
		 * Fires when an PayPal attendee tickets have been generated.
		 *
		 * @since 4.7
		 *
		 * @param string $order_id ID of the PayPal order
		 * @param int    $post_id  ID of the post the order was placed for
		 */
		do_action( 'event_tickets_tpp_tickets_generated', $order_id, $post_id );

		/**
		 * Filters whether a confirmation email should be sent or not for PayPal tickets.
		 *
		 * This applies to attendance and non attendance emails.
		 *
		 * @since 4.7
		 *
		 * @param bool $send_mail Defaults to `true`.
		 */
		$send_mail = apply_filters( 'tribe_tickets_tpp_send_mail', true );

		if (
			$send_mail
			&& $has_tickets
			&& $attendee_order_status === Order_Statuses::$completed
		) {
			$this->send_tickets_email( $order_id, $post_id );
		}

		// Redirect to the same page to prevent double purchase on refresh
		if ( ! empty( $post_id ) ) {
			/** @var \Tribe__Tickets__Commerce__PayPal__Endpoints $endpoints */
			$endpoints = tribe( 'tickets.commerce.paypal.endpoints' );
			$url       = $endpoints->success_url( $order_id, $post_id );
			if ( $redirect ) {
				wp_redirect( esc_url_raw( $url ) );
			}
			tribe_exit();
		}
	}
}