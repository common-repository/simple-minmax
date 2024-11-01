<?php

/**
 *
 * @package  Simple MinMax
 * @category Integration
 * @author   NicNet
 */

defined( 'ABSPATH' ) || exit;



$all_plugins = apply_filters('active_plugins', get_option('active_plugins'));
if (stripos(implode($all_plugins), 'woocommerce.php')) {

if ( ! class_exists( 'WC_simple_minmax_plugin_Integration' ) ) :
class WC_simple_minmax_plugin_Integration extends WC_Integration {
  /**
   * Init and hook in the integration.
   */
public function __construct() {
    global $woocommerce;
    $this->id                 = 'simple-minmax-plugin-integration';
    $this->method_title       = __( 'Simple MinMax');
    $this->method_description = __( 'Set the Minimum and/or Maximum Quantity Of A Product That May Be Ordered On A Product By Product Basis');
    // Load the settings.
    $this->init_form_fields();
    $this->init_settings();
    // Define user set variables.
    $this->custom_display_loc          = $this->get_option( 'custom_display_loc' );
    // Actions.
    //
    // Category Options
    add_action( 'product_cat_add_form_fields', array( $this, 'wc_smm_category_add'), 10, 2 );
    add_action( 'product_cat_edit_form_fields', array( $this, 'wc_smm_category_edit'), 10, 2 );
    add_action( 'edited_product_cat', array( $this, 'wc_smm_category_save'), 10, 2 );
    add_action( 'create_product_cat', array( $this, 'wc_smm_category_save'), 10, 2 );
    add_action( 'woocommerce_check_cart_items', array( $this, 'wc_smm_cart_check'), 10, 2 );
    add_action( 'woocommerce_update_cart_action_cart_updated', array( $this, 'wc_smm_cart_check'), 21, 1 );
    add_filter( 'get_terms', array( $this, 'smm_display_category_function'), 10, 3 );

    // Invidual item min/max
    add_action( 'woocommerce_product_options_inventory_product_data', array( $this, 'wc_smm_qty_add_product_field' ));
    add_action( 'woocommerce_process_product_meta', array( $this, 'wc_smm_qty_save_product_field' ));
    add_filter( 'woocommerce_quantity_input_args',  array( $this, 'wc_smm_qty_input_args') , 10, 2 );
    add_filter( 'woocommerce_add_to_cart_validation', array( $this, 'wc_smm_qty_add_to_cart_validation' ), 1, 5 );

    // Display info to user if requested to

    if ( $this->custom_display_loc == "up" ) {
       add_action( 'woocommerce_before_add_to_cart_button', array( $this, 'wc_smm_content_addtocart_button_func' ));
   }
    if ( $this->custom_display_loc == "down" ) {
       add_action( 'woocommerce_after_add_to_cart_button', array( $this, 'wc_smm_content_addtocart_button_func' ));
   }
 }


  /**
   * Initialize integration settings form fields.
   */
  public function init_form_fields() {
    $this->form_fields = array(
      'custom_display_loc' => array(
        'title'             => __( 'Display Options'),
        'type'              => 'select',
        'description'       => __( 'Select Wether To Display Minimum and Maximum Order Quantities And Where, If They Are Configured'),
        'desc_tip'          => true,
        'default'           => 'down',
	'css'      => 'min-width:200px;background-color: #e9e9e9;box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);padding: 1px 0px 1px 4px;',
	'options' => array(
          'no' => 'Do Not Display Min/Max',
          'up' => 'Above The Add To Cart Button',
          'down' => 'Under The Add To Cart Button'
     )
      ),
    );
}


// Category 
// Add to  new category 
public function wc_smm_category_add() {
	   // Retrive woocommerce_currency_symbol safely and store it to be reused
           $wcSym = esc_attr(filter_var(get_woocommerce_currency_symbol(get_option('woocommerce_currency')),FILTER_SANITIZE_STRING));
?>
    <div class="smm_options_group">
        <HR style="width:50%;size:2;">
        <label for="smm_cat_min_value"><?php _e('Minimum Category Order Value', 'woocommerece'); ?></label>
        <input type="number" min="0" name="smm_cat_min_value" id="smm_cat_min_value" maxlength="24" value="" />
        <p class="description"><?php _e('Minimum Category Order Value', 'woocommerece'); printf (esc_attr__( ' In Whole %s', 'woocommerece'), esc_attr($wcSym) ); ?></p>
        <BR>
        <label for="smm_cat_max_value"><?php _e('Maximum Category Order Value', 'woocommerece'); ?></label>
        <input type="number" min="0" name="smm_cat_max_value" id="smm_cat_max_value" maxlength="24" value="" />
        <p class="description"><?php _e('Maximum Category Order Value', 'woocommerece'); printf (esc_attr__( ' In Whole %s', 'woocommerce' ), esc_attr($wcSym) ); ?></p>
        <BR>
        <label for="smm_cat_min_quantity"><?php _e('Minimum Category Order Quantity', 'woocommerece'); ?></label>
        <input type="number" min="0" name="smm_cat_min_quantity" id="smm_cat_min_quantity" maxlength="24" value="" />
        <p class="description"><?php _e('Minimum Category Order Quantity', 'woocommerece'); ?></p>
        <BR>
        <label for="smm_cat_max_quantity"><?php _e('Maximum Category Order Quantity', 'woocommerece'); ?></label>
        <input type="number" min="0" name="smm_cat_max_quantity" id="smm_cat_max_quantity" maxlength="24" value="" />
        <p class="description"><?php _e('Maximum Category Order Quantity', 'woocommerece'); ?></p>
	<HR style="width:40%;size:2;">
        <label for="smm_cat_display"><?php _e('DO NOT Display On Shop Page', 'woocommerece'); ?></label>
        <input type="checkbox" name="smm_cat_display" id="smm_cat_display" value="1" />
        <p class="description"><?php _e('If checked, this category WILL NOT be on the Shop Page', 'woocommerece'); ?></p>
        <HR style="width:50%;size:2;">
    </div>
    <?php
}


// Edit existing category
public function wc_smm_category_edit( $term ) {
	// var_dump(get_term_meta( $term->term_id ));
	$wcSym = esc_attr(filter_var(get_woocommerce_currency_symbol(get_option('woocommerce_currency')),FILTER_SANITIZE_STRING));

        $smm_cat_min_value = filter_var(get_term_meta($term->term_id, 'smm_cat_min_value', true), FILTER_SANITIZE_NUMBER_INT);
        $smm_cat_max_value = filter_var(get_term_meta($term->term_id, 'smm_cat_max_value', true), FILTER_SANITIZE_NUMBER_INT);
        $smm_cat_min_quantity = filter_var(get_term_meta($term->term_id, 'smm_cat_min_quantity', true), FILTER_SANITIZE_NUMBER_INT);
        $smm_cat_max_quantity = filter_var(get_term_meta($term->term_id, 'smm_cat_max_quantity', true), FILTER_SANITIZE_NUMBER_INT);
	$smm_cat_display = filter_var(get_term_meta($term->term_id, 'smm_cat_display', true), FILTER_SANITIZE_NUMBER_INT);

    if( isset( $smm_cat_display ) && ($smm_cat_display == 1) ) {
        $chk_txt = 'value="1" checked="checked"';
    }
    else {
        $chk_txt = 'value="1" ';
    }




?>
    <div class="smm_options_group">
        <tr class="form-field term-display-type-wrap">
        <th scope="row" valign="top">
        <label for="smm_cat_min_value"><?php _e('Min Cat Value', 'woocommerece'); ?></label>
       </th><td>
        <input type="number" min="0" name="smm_cat_min_value" id="smm_cat_min_value" maxlength="24" value="<?php echo esc_attr($smm_cat_min_value); ?>" />
        <p class="description"><?php _e('Minimum Category Order Value', 'woocommerece'); printf (esc_attr__( ' In Whole %s', 'woocommerce' ), esc_attr($wcSym) ); ?></p>
        </td></tr>
        <tr class="form-field term-display-type-wrap">
        <th scope="row" valign="top">
        <label for="smm_cat_max_value"><?php _e('Max Cat Value', 'woocommerece'); ?></label>
        </th><td>
        <input type="number" min="0" name="smm_cat_max_value" id="smm_cat_max_value" maxlength="24" value="<?php echo esc_attr($smm_cat_max_value); ?>" />
	<p class="description"><?php _e('Maximum Category Order Value', 'woocommerece'); printf (esc_attr__( ' In Whole %s', 'woocommerce' ), esc_attr($wcSym) ); ?></p>
	</td></tr>
        <tr class="form-field term-display-type-wrap">
        <th scope="row" valign="top">
        <label for="smm_cat_min_quantity"><?php _e('Min Cat Quantity', 'woocommerece'); ?></label>
        </th><td>
        <input type="number" name="smm_cat_min_quantity" id="smm_cat_min_quantity" maxlength="24" value="<?php echo esc_attr($smm_cat_min_quantity); ?>" />
        <p class="description"><?php _e('Minimum Category Quantity', 'woocommerece'); ?></p>
	</td></tr>
        <tr class="form-field term-display-type-wrap">
        <th scope="row" valign="top">
        <label for="smm_cat_max_quantity"><?php _e('Max Cat Quantity', 'woocommerece'); ?></label>
        </th><td>
        <input type="number" name="smm_cat_max_quantity" id="smm_cat_max_quantity" maxlength="24" value="<?php echo esc_attr($smm_cat_max_quantity); ?>" />
        <p class="description"><?php _e('Maximum Category Quantity', 'woocommerece'); ?></p>
	</td></tr>

        <tr class="form-field term-display-type-wrap">
        <th scope="row" valign="top">
        <label for="smm_cat_display"><?php _e('No Display On Shop Page', 'woocommerece'); ?></label>
	</th><td>

	<input type="checkbox" name="smm_cat_display" id="smm_cat_display" 
        <?php echo esc_attr($chk_txt)  ?> 
        /> 
        <p class="description"><?php _e('If checked, this category WILL NOT be on the Shop Page', 'woocommerece') ?></p>
	</td></tr>
    </div>
    <?php
}



public function wc_smm_category_save( $term_id ) {
    $smm_cat_min_value = intval( filter_input(INPUT_POST, 'smm_cat_min_value', FILTER_SANITIZE_NUMBER_INT));
    $smm_cat_max_value = intval( filter_input(INPUT_POST, 'smm_cat_max_value', FILTER_SANITIZE_NUMBER_INT));
    $smm_cat_min_quantity = intval( filter_input(INPUT_POST, 'smm_cat_min_quantity', FILTER_SANITIZE_NUMBER_INT));
    $smm_cat_max_quantity = intval( filter_input(INPUT_POST, 'smm_cat_max_quantity',FILTER_SANITIZE_NUMBER_INT));
    $smm_cat_display = intval( filter_input(INPUT_POST, 'smm_cat_display', FILTER_SANITIZE_NUMBER_INT));


    update_term_meta($term_id, 'smm_cat_min_value', $smm_cat_min_value);
    update_term_meta($term_id, 'smm_cat_max_value', $smm_cat_max_value);
    update_term_meta($term_id, 'smm_cat_min_quantity', $smm_cat_min_quantity);
    update_term_meta($term_id, 'smm_cat_max_quantity', $smm_cat_max_quantity);


    if( isset( $smm_cat_display ) && ( $smm_cat_display == 1) ) {
          update_term_meta($term_id, 'smm_cat_display', 1);
    }
    else {
          update_term_meta($term_id, 'smm_cat_display', 0);
    }

}


// Cart check for Category Min/Max values
public function wc_smm_cart_check() {
  if( is_cart() || is_checkout() ) {
     global $woocommerce, $product;

     // Claer any notices
     wc_clear_notices();
     // Create array from every item in cart
     $catcart = array (); // Multidimensional array of categories in cart
     foreach ( WC()->cart->get_cart() as $cart_item ) {
	// Get product categories in cart
	$cata = array ();
	$cat=strip_tags(wc_get_product_category_list($cart_item['product_id']));
	$cat_ids = explode (",", $cat);

	/// Loop through category array to build array of properties
	foreach ($cat_ids as $cat) {
	  // $price =  filter_var($cart_item['line_subtotal'],FILTER_SANITIZE_NUMBER_FLOAT);
	  $unit_price = filter_var($cart_item['data']->get_price(), FILTER_SANITIZE_NUMBER_FLOAT);;
	  $quant = filter_var($cart_item['quantity'], FILTER_SANITIZE_NUMBER_INT);
	  $price = $unit_price * $quant;
	  $category = get_term_by('name', $cat, 'product_cat');
	  $term_id = $category->term_id;

	if ( isset($catcart[$term_id]) ) {
	  // add to array
		  $v_value = $catcart[$term_id]['value'];
                  $catcart[$term_id]['value'] = $v_value + $price;
		  $q_value = $catcart[$term_id]['quantity'];
                  $catcart[$term_id]['quantity'] = $q_value + $quant;
          } else {
                             // Not there so build it up buttercup
              $ctemp = array();
              $ctemp['id'] = filter_var($term_id, FILTER_SANITIZE_NUMBER_INT);
              $ctemp['name'] = esc_html($cat);
              $ctemp['quantity'] = filter_var($quant, FILTER_SANITIZE_NUMBER_INT);
              $ctemp['value'] = filter_var($price, FILTER_SANITIZE_NUMBER_FLOAT);
              $ctemp['smm_cat_min_value'] = filter_var(get_term_meta($term_id, 'smm_cat_min_value', true), FILTER_SANITIZE_NUMBER_INT) ?: NULL;
              $ctemp['smm_cat_max_value'] = filter_var(get_term_meta($term_id, 'smm_cat_max_value', true), FILTER_SANITIZE_NUMBER_INT) ?: NULL;
              $ctemp['smm_cat_min_quantity'] = filter_var(get_term_meta($term_id, 'smm_cat_min_quantity', true), FILTER_SANITIZE_NUMBER_INT) ?: NULL;
              $ctemp['smm_cat_max_quantity'] = filter_var(get_term_meta($term_id, 'smm_cat_max_quantity', true), FILTER_SANITIZE_NUMBER_INT) ?: NULL;
              // array_push($catcart[$cat], $ctemp);
              $catcart[$term_id] = $ctemp;
          }

	}

    } // End of storing values 

    // Evaluate limits
       foreach ($catcart as $obj_key =>$line) {
         $check_cat_id = $obj_key;
	 foreach ($line as $key=>$value){
           $cat_id = filter_var($catcart[$obj_key]['id'],FILTER_SANITIZE_NUMBER_INT) ?: NULL;
           $cat_name = filter_var(esc_html($catcart[$obj_key]['name']), FILTER_SANITIZE_STRING) ?: NULL;
           $cat_quant = filter_var($catcart[$obj_key]['quantity'],FILTER_SANITIZE_NUMBER_INT) ?: NULL;
	   $cat_value = filter_var($catcart[$obj_key]['value'],FILTER_SANITIZE_NUMBER_FLOAT) ?: NULL;
	   $v_smm_cat_min_value = filter_var($catcart[$obj_key]['smm_cat_min_value'],FILTER_SANITIZE_NUMBER_INT) ?: NULL;
	   $v_smm_cat_max_value = filter_var($catcart[$obj_key]['smm_cat_max_value'],FILTER_SANITIZE_NUMBER_INT) ?: NULL;
	   $v_smm_cat_min_quantity = filter_var($catcart[$obj_key]['smm_cat_min_quantity'],FILTER_SANITIZE_NUMBER_INT) ?: NULL;
	   $v_smm_cat_max_quantity = filter_var($catcart[$obj_key]['smm_cat_max_quantity'],FILTER_SANITIZE_NUMBER_INT) ?: NULL;

	   // Retrive woocommerce_currency_symbol and other info safely and store it to be reused
	   $wcSym = esc_attr(filter_var(get_woocommerce_currency_symbol(get_option('woocommerce_currency')),FILTER_SANITIZE_STRING));
	   $wcSep = esc_attr(filter_var(wc_get_price_decimal_separator(),FILTER_SANITIZE_STRING));
	   $wcPre = esc_attr(filter_var(wc_get_price_decimals(),FILTER_SANITIZE_STRING));


           if ($key == "smm_cat_min_value") {
              $value = $v_smm_cat_min_value;
              if ( $cat_value < $value ) {
                $diff = $value - $cat_value;
		wc_print_notice ('A minimum order value of <b>'.esc_attr($wcSym.number_format($value,$wcPre,$wcSep,'')).'</b> is required for Category <b>'. esc_attr($cat_name) . '</b>. Current category value in the cart is only <b> '.esc_attr($wcSym.number_format($cat_value,$wcPre,$wcSep,'')).'</b>.<BR>Please add at least another <b>  '.esc_attr($wcSym.number_format($diff,$wcPre,$wcSep,'')).'</b> of product from category <b>'. esc_attr($cat_name) . '</b>.<BR><HR style="size:2;noshade;">' ,'error');
                remove_action( 'woocommerce_proceed_to_checkout', 'woocommerce_button_proceed_to_checkout', 20 );
                   } // end if
	   } // end main if
	   if ($key == "smm_cat_max_value") {
              $value = $v_smm_cat_max_value;
              if ( ( ($value != NULL) ) && ( $cat_value > $value ) ) {
                $diff = $cat_value - $value;
                wc_print_notice ('A maximum order value of <b>'.esc_attr($wcSym.number_format($value,$wcPre,$wcSep,'')).'</b> is permitted for Category <b>'. esc_attr($cat_name) . '</b>. Current category value in the cart is <b> '.esc_attr($wcSym.number_format($cat_value,$wcPre,$wcSep,'')).'</b>.<BR>Please remove at least <b>  '.esc_attr($wcSym .number_format($diff,$wcPre,$wcSep,'')).'</b> of product from category <b>'. esc_attr($cat_name) . '</b>.<BR><HR style="size:2;noshade;">' ,'error');
                remove_action( 'woocommerce_proceed_to_checkout', 'woocommerce_button_proceed_to_checkout', 20 );
                   } // end if
	   } // end main if
	   if ($key == "smm_cat_min_quantity") {
              $value = $v_smm_cat_min_quantity;
		   if ( $cat_quant < $value ) {
                $diff = $value - $cat_quant;
		wc_print_notice ('A minimum order quantity of <b>'.esc_attr($value).'</b> is required for Category <b>'. esc_attr($cat_name) . '</b>. Current category quantity in the cart is only <b> '.esc_attr($cat_quant).'</b>.<BR>Please add at least another <b>  '.esc_attr($diff).'</b> units of product from category <b>'. esc_attr($cat_name) . '</b>.<BR><HR style="size:2;noshade;">' ,'error');
                remove_action( 'woocommerce_proceed_to_checkout', 'woocommerce_button_proceed_to_checkout', 20 );
		   } // end if
	   } // end main if
	   if ($key == "smm_cat_max_quantity") {
              $value = $v_smm_cat_max_quantity;
              if ( ( ($value != NULL) ) && ( $cat_quant > $value ) ) {
                $diff = $cat_quant - $value;
                wc_print_notice ('A maximum order quantity of <b>'.esc_attr($value).'</b> is permitted for Category <b>'. esc_attr($cat_name) . '</b>. Current category quantity in the cart is <b> '.esc_attr($cat_quant).'</b>.<BR>Please remove at least <b>  '.esc_attr($diff).'</b> units of product from category <b>'. esc_attr($cat_name) . '</b>.<BR><HR style="size:2;noshade;">' ,'error');
                remove_action( 'woocommerce_proceed_to_checkout', 'woocommerce_button_proceed_to_checkout', 20 );
                   } // end if
	   } // end main if

	 } // inside foreach end
        } // outside foreach end
  }
}



function smm_display_category_function( $terms, $taxonomies, $args ) {
$new_terms = array();

if ( in_array( 'product_cat', $taxonomies ) && !is_admin() && is_shop() ) {
	foreach ( $terms as $key => $term ) {
		if ( isset( $term->term_id) ) {
		    $display = get_term_meta($term->term_id, 'smm_cat_display', true) ?: NULL; 
		    if ( !isset( $display ) ) {
			$new_terms[] = $term; }
		} // if term
      } // foreach
      $terms = $new_terms;
} // if shop page


return $terms;
}
 


/// End Category

public function wc_get_product_max_limit( $product_id ) {
        $qty = filter_var(get_post_meta( $product_id, '_wc_max_qty_product', true ), FILTER_SANITIZE_NUMBER_INT);
        if ( empty( $qty ) ) {
                $limit = false;
        } else {
                $limit = (int) $qty;
        }
        return $limit;
}

public function wc_get_product_min_limit( $product_id ) {
        $qty = filter_var(get_post_meta( $product_id, '_wc_min_qty_product', true ), FILTER_SANITIZE_NUMBER_INT);
        if ( empty( $qty ) ) {
                $limit = false;
        } else {
                $limit = (int) $qty;
        }
        return $limit;
}



public function wc_smm_qty_add_to_cart_validation( $passed, $product_id, $quantity, $variation_id = '', $variations = '' ) {
        $product_min = filter_var( $this->wc_get_product_min_limit( $product_id ), FILTER_SANITIZE_NUMBER_INT);
        $product_max = filter_var( $this->wc_get_product_max_limit( $product_id ), FILTER_SANITIZE_NUMBER_INT );
        $new_min = NULL;
        if ( ! empty( $product_min ) ) {
                // min is empty
                if ( false !== $product_min ) {
                        $new_min = $product_min;
                } else {
                        // neither max is set, so get out
                        return $passed;
                }
        }

	$new_max = NULL;
        if ( ! empty( $product_max ) ) {
                // max is empty
                if ( false !== $product_max ) {
                        $new_max = $product_max;
                } else {
                        // neither max is set, so get out
                        return $passed;
                }
	}

	$already_in_cart        = filter_var($this->wc_qty_get_cart_qty( $product_id ), FILTER_SANITIZE_NUMBER_INT);
        $product                = wc_get_product( $product_id );
        $product_title        = filter_var($product->get_title(), FILTER_SANITIZE_STRING);
        if ( !is_null( $new_max ) && !empty( $already_in_cart ) ) {

                if ( ( $already_in_cart + $quantity ) > $new_max ) {
                        // oops. too much.
                        $passed = false;

	// Clear any notices
	wc_clear_notices();
                        wc_print_notice( apply_filters( 'isa_wc_max_qty_error_message_already_had', sprintf( __( 'You can add a maximum of %1$s %2$s\'s to %3$s. You already have %4$s.', 'woocommerce-max-quantity' ), esc_attr($new_max), esc_attr($product_title), '<a href="' . esc_url( wc_get_cart_url() ) . '">' . __( 'your cart', 'woocommerce-max-quantity' ) . '</a>', esc_attr($already_in_cart) ), esc_attr($new_max), esc_attr($already_in_cart) ), 'error' );

                }
        }

        return $passed;
}


public  function wc_smm_content_addtocart_button_func( ) {

	global $post;
	 $product = wc_get_product($post->ID);
         $product_id = filter_var($product->get_parent_id(), FILTER_SANITIZE_NUMBER_INT) ? filter_var($product->get_parent_id(), FILTER_SANITIZE_NUMBER_INT) : filter_var($product->get_id(), FILTER_SANITIZE_NUMBER_INT);

         $qty = filter_var(get_post_meta( $product_id, '_wc_min_qty_product', true ), FILTER_SANITIZE_NUMBER_INT);
         if ( !empty( $qty ) ) {
                echo '<div class="smm_quantity_content">'. sprintf( esc_html( 'Minimum Order Quantity Is :- ')) . esc_attr($qty) . ' </div><br>';
         }

         $qty = filter_var(get_post_meta( $product_id, '_wc_max_qty_product', true ), FILTER_SANITIZE_NUMBER_INT);
         if ( !empty( $qty ) ) {
                echo '<div class="smm_quantity_content">' . sprintf( esc_html( 'Maximum Order Quantity Is :- ')) . esc_attr($qty) . ' </div><br>';
         }
  }



public function wc_qty_get_cart_qty( $product_id ) {
        global $woocommerce;
        $running_qty = 0; // iniializing quantity to 0

        // search the cart for the product in and calculate quantity.
        foreach($woocommerce->cart->get_cart() as $other_cart_item_keys => $values ) {
                if ( $product_id == $values['product_id'] ) {
                        $running_qty += (int) filter_var($values['quantity'], FILTER_SANITIZE_NUMBER_INT);
                }
        }

        return $running_qty;
  }

public function wc_smm_qty_input_args( $args, $product ) {
        $product_id = $product->get_parent_id() ? $product->get_parent_id() : $product->get_id();

        $product_min = filter_var( $this->wc_get_product_min_limit( $product_id ), FILTER_SANITIZE_NUMBER_INT);
        $product_max = filter_var( $this->wc_get_product_max_limit( $product_id ), FILTER_SANITIZE_NUMBER_INT);

        if ( ! empty( $product_min ) ) {
                // min is empty
                if ( false !== $product_min ) {
                        $args['min_value'] = $product_min;
                }
        }

        if ( ! empty( $product_max ) ) {
                // max is empty
                if ( false !== $product_max ) {
                        $args['max_value'] = $product_max;
                }
        }

        if ( $product->managing_stock() && ! $product->backorders_allowed() ) {
                $stock = filter_var($product->get_stock_quantity(), FILTER_SANITIZE_NUMBER_INT);

                $args['max_value'] = min( $stock, $args['max_value'] );
        }

        return $args;
}



public function wc_smm_qty_save_product_field( $post_id ) {
        $val_min = filter_var(trim( get_post_meta( $post_id, '_wc_min_qty_product', true ) ), FILTER_SANITIZE_NUMBER_INT);
        $new_min = sanitize_text_field( $_POST['_wc_min_qty_product'] );

        $val_max = filter_var(trim( get_post_meta( $post_id, '_wc_max_qty_product', true ) ), FILTER_SANITIZE_NUMBER_INT);
        $new_max = sanitize_text_field( $_POST['_wc_max_qty_product'] );

        if ( $val_min != $new_min ) {
                update_post_meta( $post_id, '_wc_min_qty_product', $new_min );
        }

        if ( $val_max != $new_max ) {
                update_post_meta( $post_id, '_wc_max_qty_product', $new_max );
        }
  }

public  function wc_smm_qty_add_product_field() {

        echo '<div class="smm_options_group">';
        woocommerce_wp_text_input(
                array(
                        'id'          => '_wc_min_qty_product',
                        'label'       => __( 'Minimum Quantity', 'woocommerce-max-quantity' ),
                        'placeholder' => '',
                        'type' => 'number',
			'desc_tip'    => 'true',
			'custom_attributes' => array(
 					'step' 	=> 'any',
 					'min'	=> '0'
 				),
                        'description' => __( 'Optional. Set a minimum quantity  allowed per order. Enter a number, 1 or greater. If nothing is entered no limits are enforced.', 'woocommerce-max-quantity' )
                )
        );
        echo '</div>';

        echo '<div class="smm_options_group">';
        woocommerce_wp_text_input(
                array(
                        'id'          => '_wc_max_qty_product',
                        'label'       => __( 'Maximum Quantity', 'woocommerce-max-quantity' ),
                        'placeholder' => '',
			'type' => 'number',
			'custom_attributes' => array(
 					'step' 	=> 'any',
 					'min'	=> '0'
 				),
                        'desc_tip'    => 'true',
                        'description' => __( 'Optional. Set a maximum quantity allowed per order. Enter a number, 1 or greater. If nothing is entered no limits are enforced.', 'woocommerce-max-quantity' )
                )
        );
        echo '</div>';
}


}
endif; 
}

