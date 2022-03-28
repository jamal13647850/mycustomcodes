<?php 

declare (strict_types = 1);

namespace jamal\mycustomcodes;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WC {
	public function modifyStatus( $status ) {
		return str_ireplace( array( 'wc-', 'wc_' ), '', $status );
	}

    public function statusName( $status, $pending = false ) {

		$status = wc_get_order_status_name( $status );
		if ( $status == 'created' ) {
			$pending_label = _x( 'Pending payment', 'Order status', 'woocommerce' );
			$status        = $pending ? $pending_label : $pending_label . ' (بلافاصله بعد از ثبت سفارش)';
		}

		return $status;
	}

    public function getAllStatuses( $pending = false ) {

		if ( ! function_exists( 'wc_get_order_statuses' ) ) {
			return array();
		}

		$statuses = wc_get_order_statuses();

		$pending_label = _x( 'Pending payment', 'Order status', 'woocommerce' );
		if ( ! empty( $statuses['wc-pending'] ) ) {
			$statuses['wc-pending'] = $pending ? $pending_label : $pending_label . ' (بعد از تغییر وضعیت سفارش)';
		}
		if ( empty( $statuses['wc-created'] ) ) {
			$statuses = array_merge( array( 'wc-created' => $pending ? 'بعد از ثبت سفارش' : $pending_label . ' (بلافاصله بعد از ثبت سفارش)' ), $statuses );
		}

		$opt_statuses = array();
		foreach ( (array) $statuses as $status_val => $status_name ) {
			$opt_statuses[ $this->modifyStatus( $status_val ) ] = $status_name;
		}

		return $opt_statuses;
	}

    public function productSalePriceTime( $product, $type = '' ) {

		if ( is_numeric( $product ) ) {
			$product_id = $product;
			$product    = wc_get_product( $product_id );
		} else {
			$product_id = $this->ProductId( $product );
		}

		$timestamp = '';
		$method    = 'get_date_on_sale_' . $type;
		if ( method_exists( $product, $method ) ) {
			$timestamp = $product->$method();
			if ( method_exists( $timestamp, 'getOffsetTimestamp' ) ) {
				$timestamp = $timestamp->getOffsetTimestamp();
			} else {
				$timestamp = '';
			}
		}
		if ( empty( $timestamp ) ) {
			$timestamp = get_post_meta( $product_id, '_sale_price_dates_' . $type, true );
		}

		return $timestamp;
	}

    public function productId( $product = '' ) {

		if ( empty( $product ) ) {
			$product_id = get_the_ID();
		} else if ( is_numeric( $product ) ) {
			$product_id = $product;
		} else if ( is_object( $product ) ) {
			$product_id = $this->ProductProp( $product, 'id' );
		} else {
			$product_id = false;
		}

		return $product_id;
	}

	public function productProp( $product, $prop ) {
		$method = 'get_' . $prop;

		return method_exists( $product, $method ) ? $product->$method() : ( ! empty( $product->{$prop} ) ? $product->{$prop} : '' );
	}

    public function productStockQty( $product ) {

		if ( ! is_object( $product ) ) {
			$product = wc_get_product( $product );
		}

		if ( method_exists( $product, 'get_stock_quantity' ) ) {
			$quantity = $product->get_stock_quantity();
		} else {
			$quantity = $this->ProductProp( $product, 'total_stock' );
		}

		if ( empty( $quantity ) ) {
			$quantity = ( (int) get_post_meta( $this->ProductId( $product ), '_stock', true ) );
		}

		return ! empty( $quantity ) ? $quantity : 0;
	}

    public function getProdcutLists( $order, $field = '' ) {

		$products = array();
		$fields   = array();

		foreach ( (array) $this->OrderProp( $order, 'items' ) as $product ) {

			$parent_product_id = ! empty( $product['product_id'] ) ? $product['product_id'] : $this->ProductId( $product );
			$product_id        = $this->ProductProp( $product, 'variation_id' );
			$product_id        = ! empty( $product_id ) ? $product_id : $parent_product_id;

			$item = array(
				'id'         => $product_id,
				'product_id' => $parent_product_id,
				'qty'        => ! empty( $product['qty'] ) ? $product['qty'] : 0,
				'total'      => ! empty( $product['total'] ) ? $product['total'] : 0,
			);

			if ( ! empty( $field ) && isset( $item[ $field ] ) ) {
				$fields[] = $item[ $field ];
			}

			$products[ $parent_product_id ][] = $item;
		}

		if ( ! empty( $field ) ) {
			$products[ $field ] = $fields;
		}

		return $products;
	}

    public function allItems( $order ) {

		$order_products = $this->GetProdcutLists( $order );

		$items = array();
		foreach ( (array) $order_products as $item_datas ) {
			foreach ( (array) $item_datas as $item_data ) {
				$this->prepareItems( $items, $item_data );
			}
		}

		$items['product_ids'] = array_keys( $order_products );

		return $items;
	}

    public function prepareItems( &$items, $item_data ) {

		if ( ! empty( $item_data['id'] ) ) {
			$title                = $this->MaybeVariableProductTitle( $item_data['id'] );
			$items['items'][]     = $title;
			$items['items_qty'][] = $title . ' (' . $item_data['qty'] . ')';
			$items['price'][]     = $item_data['total'];
		}
	}

	public function MayBeVariable( $product ):array|int {

		$product_id = $this->ProductId( $product );
		$product    = wc_get_product( $product_id );
		if ( $product->is_type( 'variable' ) ) {

			unset( $product_id );

			$product_ids = array();
			foreach ( (array) $this->ProductProp( $product, 'children' ) as $product_id ) {
				//$product_ids[] = wc_get_product( $product_id );
				$product_ids[] = $product_id;
			}

			return $product_ids;//array
		} else {
			return $product_id;//int
		}
	}

    public function MaybeVariableProductTitle( $product ) {

		$product_id = $this->ProductId( $product );

		if ( ! is_object( $product ) ) {
			$product = wc_get_product( $product );
		}

		$attributes = $this->ProductProp( $product, 'variation_attributes' );
		$parent_id  = $this->ProductProp( $product, 'parent_id' );

		if ( ! empty( $attributes ) && ! empty( $parent_id ) ) {

			$parent = wc_get_product( $parent_id );

			$variation_attributes = $this->ProductProp( $parent, 'variation_attributes' );

			$variable_title = array();
			foreach ( (array) $attributes as $attribute_name => $options ) {

				$attribute_name = str_ireplace( 'attribute_', '', $attribute_name );

				foreach ( (array) $variation_attributes as $key => $value ) {
					$key = str_ireplace( 'attribute_', '', $key );

					if ( sanitize_title( $key ) == sanitize_title( $attribute_name ) ) {
						$attribute_name = $key;
						break;
					}
				}

				if ( ! empty( $options ) && substr( strtolower( $attribute_name ), 0, 3 ) !== 'pa_' ) {
					$variable_title[] = $attribute_name . ':' . $options;
				}
			}

			$product_title = get_the_title( $parent_id );

			if ( ! empty( $variable_title ) ) {
				$product_title .= ' (' . implode( ' - ', $variable_title ) . ')';
			}
		} else {
			$product_title = get_the_title( $product_id );
		}

		return html_entity_decode( urldecode( $product_title ) );
	}

    public function orderProp( $order, $prop, $args = array() ) {
		$method = 'get_' . $prop;

		if ( method_exists( $order, $method ) ) {
			if ( empty( $args ) || ! is_array( $args ) ) {
				return $order->$method();
			} else {
				return call_user_func_array( array( $order, $method ), $args );
			}
		}

		return ! empty( $order->{$prop} ) ? $order->{$prop} : '';
	}

    public function orderId( $order ) {
		return $this->OrderProp( $order, 'id' );
	}

    public function OrderDate( $order ) {

		$order_date = $this->OrderProp( $order, 'date_paid' );
		if ( empty( $order_date ) ) {
			$order_date = $this->OrderProp( $order, 'date_created' );
		}
		if ( empty( $order_date ) ) {
			$order_date = $this->OrderProp( $order, 'date_modified' );
		}
		if ( ! empty( $order_date ) ) {
			if ( method_exists( $order_date, 'getOffsetTimestamp' ) ) {
				$order_date = gmdate( 'Y-m-d H:i:s', $order_date->getOffsetTimestamp() );
			}
		} else {
			$order_date = date_i18n( 'Y-m-d H:i:s' );
		}

		return $this->mayBeJalaliDate( $order_date );
	}

    public function mayBeJalaliDate( $date_time ) {

		if ( empty( $date_time ) ) {
			return '';
		}

		$_date_time = explode( ' ', $date_time );
		$date       = ! empty( $_date_time[0] ) ? explode( '-', $_date_time[0], 3 ) : '';
		$time       = ! empty( $_date_time[1] ) ? $_date_time[1] : '';

		if ( count( $date ) != 3 || $date[0] < 2000 ) {
			return $date_time;
		}

		list( $year, $month, $day ) = $date;

		$date = $this->JalaliDate( $year, $month, $day, '/' ) . ' - ' . $time;

		return trim( trim( $date ), '- ' );
	}

	//از سایت jdf
	public function JalaliDate( $g_y, $g_m, $g_d, $mod = '' ) {
		$d_4   = $g_y % 4;
		$g_a   = array( 0, 0, 31, 59, 90, 120, 151, 181, 212, 243, 273, 304, 334 );
		$doy_g = $g_a[ (int) $g_m ] + $g_d;
		if ( $d_4 == 0 and $g_m > 2 ) {
			$doy_g ++;
		}
		$d_33 = (int) ( ( ( $g_y - 16 ) % 132 ) * .0305 );
		$a    = ( $d_33 == 3 or $d_33 < ( $d_4 - 1 ) or $d_4 == 0 ) ? 286 : 287;
		$b    = ( ( $d_33 == 1 or $d_33 == 2 ) and ( $d_33 == $d_4 or $d_4 == 1 ) ) ? 78 : ( ( $d_33 == 3 and $d_4 == 0 ) ? 80 : 79 );
		if ( (int) ( ( $g_y - 10 ) / 63 ) == 30 ) {
			$a --;
			$b ++;
		}
		if ( $doy_g > $b ) {
			$jy    = $g_y - 621;
			$doy_j = $doy_g - $b;
		} else {
			$jy    = $g_y - 622;
			$doy_j = $doy_g + $a;
		}
		if ( $doy_j < 187 ) {
			$jm = (int) ( ( $doy_j - 1 ) / 31 );
			$jd = $doy_j - ( 31 * $jm ++ );
		} else {
			$jm = (int) ( ( $doy_j - 187 ) / 30 );
			$jd = $doy_j - 186 - ( $jm * 30 );
			$jm += 7;
		}

		$jd = $jd > 9 ? $jd : '0' . $jd;
		$jm = $jm > 9 ? $jm : '0' . $jm;

		return ( $mod == '' ) ? array( $jy, $jm, $jd ) : $jy . $mod . $jm . $mod . $jd;
	}

	public function mergeOrderTags($content, $order_status, $order, $vendor_items_array = array())
	{
		$wc = new WC();
		$order_id = $order->get_id();
		$price    = strip_tags($order->get_formatted_order_total());
		$price    = html_entity_decode($price);

		$all_product_list = $wc->AllItems($order);
		$all_product_ids  = !empty($all_product_list['product_ids']) ? $all_product_list['product_ids'] : array();
		$all_items        = !empty($all_product_list['items']) ? $all_product_list['items'] : array();
		$all_items_qty    = !empty($all_product_list['items_qty']) ? $all_product_list['items_qty'] : array();

		$vendor_product_ids = !empty($vendor_items_array['product_ids']) ? $vendor_items_array['product_ids'] : array();
		$vendor_items       = !empty($vendor_items_array['items']) ? $vendor_items_array['items'] : array();
		$vendor_items_qty   = !empty($vendor_items_array['items_qty']) ? $vendor_items_array['items_qty'] : array();
		$vendor_price       = !empty($vendor_items_array['price']) ? array_sum((array) $vendor_items_array['price']) : 0;
		$vendor_price       = strip_tags(wc_price($vendor_price));

		$payment_gateways = array();
		if (WC()->payment_gateways()) {
			$payment_gateways = WC()->payment_gateways->payment_gateways();
		}

		$payment_method  = $wc->OrderProp($order, 'payment_method');
		$payment_method  = (isset($payment_gateways[$payment_method]) ? esc_html($payment_gateways[$payment_method]->get_title()) : esc_html($payment_method));
		$shipping_method = esc_html($wc->OrderProp($order, 'shipping_method'));

		$country = WC()->countries;

		$bill_country = (isset($country->countries[$wc->OrderProp($order, 'billing_country')])) ? $country->countries[$wc->OrderProp($order, 'billing_country')] : $wc->OrderProp($order, 'billing_country');
		$bill_state   = ($wc->OrderProp($order, 'billing_country') && $wc->OrderProp($order, 'billing_state') && isset($country->states[$wc->OrderProp($order, 'billing_country')][$wc->OrderProp($order, 'billing_state')])) ? $country->states[$wc->OrderProp($order, 'billing_country')][$wc->OrderProp($order, 'billing_state')] : $wc->OrderProp($order, 'billing_state');

		$shipp_country = (isset($country->countries[$wc->OrderProp($order, 'shipping_country')])) ? $country->countries[$wc->OrderProp($order, 'shipping_country')] : $wc->OrderProp($order, 'shipping_country');
		$shipp_state   = ($wc->OrderProp($order, 'shipping_country') && $wc->OrderProp($order, 'shipping_state') && isset($country->states[$wc->OrderProp($order, 'shipping_country')][$wc->OrderProp($order, 'shipping_state')])) ? $country->states[$wc->OrderProp($order, 'shipping_country')][$wc->OrderProp($order, 'shipping_state')] : $wc->OrderProp($order, 'shipping_state');

		$post = get_post($order_id);

		$tags = array(
			'{b_first_name}'  => $wc->OrderProp($order, 'billing_first_name'),
			'{b_last_name}'   => $wc->OrderProp($order, 'billing_last_name'),
			'{b_company}'     => $wc->OrderProp($order, 'billing_company'),
			'{b_address_1}'   => $wc->OrderProp($order, 'billing_address_1'),
			'{b_address_2}'   => $wc->OrderProp($order, 'billing_address_2'),
			'{b_state}'       => $bill_state,
			'{b_city}'        => $wc->OrderProp($order, 'billing_city'),
			'{b_postcode}'    => $wc->OrderProp($order, 'billing_postcode'),
			'{b_country}'     => $bill_country,
			'{sh_first_name}' => $wc->OrderProp($order, 'shipping_first_name'),
			'{sh_last_name}'  => $wc->OrderProp($order, 'shipping_last_name'),
			'{sh_company}'    => $wc->OrderProp($order, 'shipping_company'),
			'{sh_address_1}'  => $wc->OrderProp($order, 'shipping_address_1'),
			'{sh_address_2}'  => $wc->OrderProp($order, 'shipping_address_2'),
			'{sh_state}'      => $shipp_state,
			'{sh_city}'       => $wc->OrderProp($order, 'shipping_city'),
			'{sh_postcode}'   => $wc->OrderProp($order, 'shipping_postcode'),
			'{sh_country}'    => $shipp_country,
			'{phone}'         => get_post_meta($order_id, '_billing_phone', true),
			'{mobile}'        => get_user_meta($order->get_customer_id(), 'billing_mobile_number', true),
			'{email}'         => $wc->OrderProp($order, 'billing_email'),
			'{order_id}'      => $wc->OrderProp($order, 'order_number'),
			'{date}'          => $wc->OrderDate($order),
			'{post_id}'       => $order_id,
			'{status}'        => $wc->statusName($order_status, true),
			'{price}'         => $price,

			'{all_items}'     => implode(' - ', $all_items),
			'{all_items_qty}' => implode(' - ', $all_items_qty),
			'{count_items}'   => count($all_items),

			'{vendor_items}'       => implode(' - ', $vendor_items),
			'{vendor_items_qty}'   => implode(' - ', $vendor_items_qty),
			'{count_vendor_items}' => count($vendor_items),
			'{vendor_price}'       => $vendor_price,

			'{transaction_id}'  => get_post_meta($order_id, '_transaction_id', true),
			'{payment_method}'  => $payment_method,
			'{shipping_method}' => $shipping_method,
			'{description}'     => nl2br(esc_html($post->post_excerpt)),
		);


		$content = str_ireplace(array_keys($tags), array_values($tags), $content);
		$content = str_ireplace(array('<br>', '<br/>', '<br />', '&nbsp;'), array('', '', '', ' '), $content);


		return $content;
	}
}