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
}