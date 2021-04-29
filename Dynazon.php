<?php
/*
Plugin Name: Dynazon - Dynamic Amazon Link (on Product page)
Plugin URI: http://www.visiaro.com
Description: Updates Amazon Link from custom variation_amazon_asin field
Version: 1.0
Author URI: http://www.visiaro.com
License: GPLv2
*/
// add to product template
/*
STEP 1
add following snippet to product template
makr sure id is id="variation_amazon_asin_custom_link"
<a id="variation_amazon_asin_custom_link" href="https://www.amazon.in/stores/VISIARO/page/FCD29134-CC6B-45A3-91C7-4CEC7329400D?ref_=ast_bln" target="_blank" rel="noopener noreferrer">
    <img class="alignnone wp-image-94" src="http://localhost:8000/wp-content/uploads/WhatsApp-Image-2021-04-22-at-11.28.11-PM.jpeg" alt="" width="104" height="50" />
</a>

STEP 2
Replace http://localhost:8000/wp-content/uploads/WhatsApp-Image-2021-04-22-at-11.28.11-PM.jpeg in above code with your amazon image location

STEP 3
in each product,
in each product Variation,
add Amazon ASIN 
*/
function custom_menu() { 
 
    add_menu_page( 
        'Dynazon Settings', 
        'Dynazon Settings', 
        'edit_posts', 
        'dynazon-settings', 
        'dynazon_render_plugin_settings_page', 
        'dashicons-media-spreadsheet' 
       );
  }
  add_action('admin_menu', 'custom_menu');
  function dynazon_render_plugin_settings_page() {
    ?>
    <h2>Dynazon settings</h2>
    <form action="options.php" method="post">
        <?php 
        settings_fields( 'dynazon_plugin_options' );
        do_settings_sections( 'dynazon_plugin' ); ?>
        <input name="submit" class="button button-primary" type="submit" value="<?php esc_attr_e( 'Save' ); ?>" />
    </form>
    <?php
}
function dynazon_register_settings() {
    register_setting( 'dynazon_plugin_options', 'dynazon_plugin_options', 'dynazon_plugin_options_validate' );
    add_settings_section( 'dynazon_plugin_section_1', 'Settings', 'dynazon_plugin_section_text', 'dynazon_plugin' );

    add_settings_field( 'dynazon_plugin_setting_default_url', 'Default URL(when no variation is selected)', 'dynazon_plugin_setting_default_url', 'dynazon_plugin', 'dynazon_plugin_section_1' );
    add_settings_field( 'dynazon_plugin_setting_template', 'URL Template (use {#var#} for placeholder)', 'dynazon_plugin_setting_template', 'dynazon_plugin', 'dynazon_plugin_section_1' );
}
add_action( 'admin_init', 'dynazon_register_settings' );
function dynazon_plugin_options_validate( $input ) {
    return $input;
}
function dynazon_plugin_section_text() {
    echo '<p>Dyanmic Amazon Link settings</p>';
}

function dynazon_plugin_setting_default_url() {
    $options = get_option( 'dynazon_plugin_options' );
    echo "<input id='dynazon_plugin_setting_default_url' name='dynazon_plugin_options[default_url]' type='text' value='" . esc_attr( $options['default_url'] ) . "' />";
}

function dynazon_plugin_setting_template() {
    $options = get_option( 'dynazon_plugin_options' );
    echo "<input id='dynazon_plugin_setting_template' name='dynazon_plugin_options[template]' type='text' value='" . esc_attr( $options['template'] ) . "' />";
}

// 
add_action( 'woocommerce_variation_options_pricing', 'add_variation_amazon_asin_field_to_variations', 10, 3 );
function add_variation_amazon_asin_field_to_variations( $loop, $variation_data, $variation ) {
woocommerce_wp_text_input( array(
'id' => 'variation_amazon_asin[' . $loop . ']',
'class' => 'short',
'label' => __( 'Amazon URL (use {#ASIN#} for asin placeholder)', 'woocommerce' ),
'value' => get_post_meta( $variation->ID, 'variation_amazon_asin', true )
)
);
}

add_filter('woocommerce_product_data_tabs', function($tabs) {
    $tabs['dynazon'] = [
        'label' => __('Dynazon', 'txtdomain'),
        'target' => 'additional_product_data',
        'class' => ['hide_if_external'],
        'priority' => 25
    ];
    return $tabs;
});
add_action('woocommerce_product_data_panels', function() {
    ?><div id="additional_product_data" class="panel woocommerce_options_panel hidden"><?php
    woocommerce_wp_text_input([
        'id' => 'dynazon_default_link',
        'label' => __('Product Specific Default link if no variation is selected and no global default link specific in plugin settings (product meta field: dynazon_default_link)', 'txtdomain'),
        'type' => 'text',
    ]);
 
    ?></div><?php
});
add_action('woocommerce_process_product_meta', function($post_id) {
    $product = wc_get_product($post_id);
    
    $product->update_meta_data('dynazon_default_link', sanitize_text_field($_POST['dynazon_default_link']));
 
    $product->save();
});
add_action( 'woocommerce_save_product_variation', 'save_variation_amazon_asin_field_variations', 10, 2 );
function save_variation_amazon_asin_field_variations( $variation_id, $i ) {
$variation_amazon_asin = $_POST['variation_amazon_asin'][$i];
if ( isset( $variation_amazon_asin ) ) update_post_meta( $variation_id, 'variation_amazon_asin', esc_attr( $variation_amazon_asin ) );
}
add_filter( 'woocommerce_available_variation', 'add_variation_amazon_asin_field_variation_data' );
function add_variation_amazon_asin_field_variation_data( $variations ) {
$variations['variation_amazon_asin'] =  get_post_meta( $variations[ 'variation_id' ], 'variation_amazon_asin', true );
return $variations;
}
add_action( 'woocommerce_after_variations_form', 'add_variation_amazon_asin_attribute_on_amazon_link' );
function add_variation_amazon_asin_attribute_on_amazon_link(){
    ?>
    <script type="text/javascript">
    (function($){
        var default_store_link="https://www.amazon.in/stores/VISIARO/HardwareFittings/page/D7AB8AF5-2636-48A3-9D02-64BDF987DF68";

        var og_link = '';
        // var og_link = $('#variation_amazon_asin_custom_link').attr("href");
        // product specific og_link
        og_link = '<?php global $post;echo get_post_meta( $post->ID,'dynazon_default_link')[0]; ?>';
        var template = '<?php $options=get_option( 'dynazon_plugin_options' ); echo $options['template']; ?>';
        if(typeof og_link =="undefined" || og_link == null || og_link==""){
            og_link = '<?php $options=get_option( 'dynazon_plugin_options' ); echo $options['default_url']; ?>';
            if(typeof og_link =="undefined" || og_link == null || og_link==""){
                og_link = default_store_link;
            }
        }

        $('form.cart').on('show_variation', function(event, data) {
            var text = '';
                if(data.variation_amazon_asin!=''){
                    text = template.replace("{#var#}",data.variation_amazon_asin)
                    // alert(text)
                }else{
                    text=og_link;
                }
            $('#variation_amazon_asin_custom_link').attr("href", text);
        }).on('hide_variation', function(event, data) {
            // alert("hid")
            // alert(og_link)
            $('#variation_amazon_asin_custom_link').attr("href", og_link);
        });
       
    })(jQuery);
    </script>
    <?php
}
?>