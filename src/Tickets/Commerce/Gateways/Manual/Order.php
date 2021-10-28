<?php

namespace TEC\Tickets\Commerce\Gateways\Manual;

use TEC\Tickets\Commerce\Utils\Price;
use TEC\Tickets\Commerce\Order as Commerce_Order;
use Tribe__Utils__Array as Arr;

/**
 * Class Order
 *
 * @since   TBD
 *
 * @package TEC\Tickets\Commerce\Gateways\Manual
 */
class Order {
	public function create( $items, $purchaser = [] ) {
		$order   = tribe( Commerce_Order::class );
		$gateway = tribe( Gateway::class );

		$items      = array_map(
			static function ( $item ) {
				$ticket = \Tribe__Tickets__Tickets::load_ticket_object( $item['ticket_id'] );
				if ( null === $ticket ) {
					return null;
				}

				$item['sub_total'] = Price::sub_total( $ticket->price, $item['quantity'] );
				$item['price']     = $ticket->price;

				return $item;
			},
			$items
		);
		$items      = array_filter( $items );
		$sub_totals = array_filter( wp_list_pluck( $items, 'sub_total' ) );
		$total      = Price::total( $sub_totals );
		$hash       = wp_generate_password( 12, false );

		$order_args = [
			'title'       => $order->generate_order_title( $items, [ 'M', $hash ] ),
			'total_value' => $total,
			'items'       => $items,
			'gateway'     => $gateway::get_key(),
		];

		// When purchaser data-set is not passed we pull from the current user.
		if ( empty( $purchaser ) && is_user_logged_in() && $user = wp_get_current_user() ) {
			$order_args['purchaser_user_id']    = $user->ID;
			$order_args['purchaser_full_name']  = $user->first_name . ' ' . $user->last_name;
			$order_args['purchaser_first_name'] = $user->first_name;
			$order_args['purchaser_last_name']  = $user->last_name;
			$order_args['purchaser_email']      = $user->user_email;
		} elseif ( empty( $purchaser ) ) {
			$order_args['purchaser_user_id']    = 0;
			$order_args['purchaser_full_name']  = Commerce_Order::$placeholder_name;
			$order_args['purchaser_first_name'] = Commerce_Order::$placeholder_name;
			$order_args['purchaser_last_name'] = Commerce_Order::$placeholder_name;
			$order_args['purchaser_email']      = '';
		} else {
			$order_args['purchaser_user_id'] = Arr::get( $purchaser, 'user_id', 0 );
			if ( ! empty( $purchaser['full_name'] ) ) {
				$order_args['purchaser_full_name'] = Arr::get( $purchaser, 'full_name' );
			}
			if ( ! empty( $purchaser['first_name'] ) ) {
				$order_args['purchaser_first_name'] = Arr::get( $purchaser, 'first_name' );
			}
			if ( ! empty( $purchaser['last_name'] ) ) {
				$order_args['purchaser_last_name'] = Arr::get( $purchaser, 'last_name' );
			}
			if ( ! empty( $purchaser['email'] ) ) {
				$order_args['purchaser_email'] = Arr::get( $purchaser, 'email' );
			}
		}

		return $order->create( $gateway, $order_args );
	}
}