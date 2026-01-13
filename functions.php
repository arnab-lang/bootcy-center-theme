<?php
/**
 * Theme functions and definitions
 */

function hello_elementor_child_enqueue_scripts() {
    // 1. Parent & Child Styles
    wp_enqueue_style( 'hello-elementor-child-style', get_stylesheet_directory_uri() . '/style.css', array('hello-elementor-theme-style'), time());

    // 2. External Libs
    wp_enqueue_style('google-fonts', 'https://fonts.googleapis.com/css2?family=Lato:wght@300;400;700;900&family=Dancing+Script:wght@400;700&display=swap', array(), null);
    wp_enqueue_style('font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css', array(), '6.4.0');
    wp_enqueue_style('swiper-css', 'https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.css', array(), '10.0');

    // 3. Scripts
    wp_enqueue_script('swiper-js', 'https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.js', array(), '10.0', true);
    wp_enqueue_script('custom-js', get_stylesheet_directory_uri() . '/assets/js/main.js', array('jquery', 'swiper-js'), '1.0', true);

    // 4. LOCALIZE SCRIPT (Crucial: Sends data from PHP to JS)
    wp_localize_script('custom-js', 'flatsome_params', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce'    => wp_create_nonce('flatsome_nonce')
    ));
}
add_action( 'wp_enqueue_scripts', 'hello_elementor_child_enqueue_scripts', 20 );



/* --- NEW: AJAX QUICK VIEW HANDLER --- */
add_action('wp_ajax_load_quick_view', 'flatsome_load_quick_view');
add_action('wp_ajax_nopriv_load_quick_view', 'flatsome_load_quick_view');

function flatsome_load_quick_view() {
    // Security check
    if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'flatsome_nonce' ) ) {
        die( 'Permission denied' );
    }

    $product_id = intval($_POST['product_id']);

    // Ensure global $post and $product are set so WooCommerce templates (add-to-cart) render correctly
    global $post, $product;
    $post = get_post( $product_id );
    setup_postdata( $post );
    $product = wc_get_product( $product_id );

    if ( ! $product ) {
        wp_reset_postdata();
        die('Product not found');
    }

    // Prepare Gallery Images
    $attachment_ids = $product->get_gallery_image_ids();
    $main_image_id  = $product->get_image_id();
    
    // Add main image to the start of the array
    if($main_image_id) {
        array_unshift($attachment_ids, $main_image_id);
    }
    
    // Remove duplicates
    $attachment_ids = array_unique($attachment_ids);

    ob_start();
    ?>
    <div class="qv-wrapper">
        <div class="qv-gallery">
            <div class="swiper qv-swiper">
                <div class="swiper-wrapper">
                    <?php if ( ! empty($attachment_ids) ) : ?>
                        <?php foreach ( $attachment_ids as $attachment_id ) : ?>
                            <div class="swiper-slide">
                                <?php echo wp_get_attachment_image( $attachment_id, 'large' ); ?>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="swiper-slide">
                            <img src="<?php echo wc_placeholder_img_src(); ?>" alt="Placeholder">
                        </div>
                    <?php endif; ?>
                </div>
                <div class="swiper-button-next"></div>
                <div class="swiper-button-prev"></div>
                <div class="swiper-pagination"></div>
            </div>
        </div>

        <div class="qv-details">
            <h2 class="qv-title"><?php echo $product->get_name(); ?></h2>
            <div class="qv-price"><?php echo $product->get_price_html(); ?></div>
            
            <div class="qv-short-desc">
                <?php echo apply_filters( 'woocommerce_short_description', $product->get_short_description() ); ?>
            </div>

            <div class="qv-add-to-cart">
                <?php 
                // This renders the FULL functional form (Variable dropdowns, Quantity, etc.)
                woocommerce_template_single_add_to_cart(); 
                ?>
            </div>
            
            <div class="qv-meta">
                <span class="sku_wrapper">SKU: <span class="sku"><?php echo ( $product->get_sku() ) ? $product->get_sku() : 'N/A'; ?></span></span>
                <span class="posted_in">Category: <?php echo wc_get_product_category_list( $product->get_id() ); ?></span>
            </div>
        </div>
    </div>
    <?php
    echo ob_get_clean();
    wp_reset_postdata();
    wp_die();
}

/**
 * Ensure shop / product archive shows 8 products per page (main query)
 */
function child_set_products_per_page( $query ) {
    if ( ! is_admin() && $query->is_main_query() && ( is_post_type_archive('product') || ( function_exists('is_shop') && is_shop() ) ) ) {
        $query->set( 'posts_per_page', 8 );
    }
}
add_action( 'pre_get_posts', 'child_set_products_per_page' );

/**
 * Add Customizer settings to manage the Hero Slider and Banners section
 */
function flatsome_customize_register( $wp_customize ) {
    // Hero Slider (3 slides)
    $wp_customize->add_section( 'hero_slider', array(
        'title' => __( 'Hero Slider', 'hello-elementor-child' ),
        'priority' => 30,
        'description' => __( 'Manage hero slides (image, subtitle, title, link)', 'hello-elementor-child' ),
    ) );

    for ( $i = 1; $i <= 3; $i++ ) {
        $wp_customize->add_setting( "hero_slide_{$i}_image", array( 'default' => '', 'sanitize_callback' => 'esc_url_raw' ) );
        $wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, "hero_slide_{$i}_image", array(
            'label' => sprintf( __( 'Slide %d Image', 'hello-elementor-child' ), $i ),
            'section' => 'hero_slider',
            'settings' => "hero_slide_{$i}_image",
        ) ) );

        $wp_customize->add_setting( "hero_slide_{$i}_subtitle", array( 'default' => '', 'sanitize_callback' => 'sanitize_text_field' ) );
        $wp_customize->add_control( "hero_slide_{$i}_subtitle", array(
            'label' => sprintf( __( 'Slide %d Subtitle', 'hello-elementor-child' ), $i ),
            'section' => 'hero_slider',
            'type' => 'text',
        ) );

        $wp_customize->add_setting( "hero_slide_{$i}_title", array( 'default' => '', 'sanitize_callback' => 'sanitize_text_field' ) );
        $wp_customize->add_control( "hero_slide_{$i}_title", array(
            'label' => sprintf( __( 'Slide %d Title', 'hello-elementor-child' ), $i ),
            'section' => 'hero_slider',
            'type' => 'text',
        ) );

        $wp_customize->add_setting( "hero_slide_{$i}_link", array( 'default' => '', 'sanitize_callback' => 'esc_url_raw' ) );
        $wp_customize->add_control( "hero_slide_{$i}_link", array(
            'label' => sprintf( __( 'Slide %d Link', 'hello-elementor-child' ), $i ),
            'section' => 'hero_slider',
            'type' => 'url',
        ) );
    }

    // Banners (3 banners)
    $wp_customize->add_section( 'banners_section', array(
        'title' => __( 'Banners Section', 'hello-elementor-child' ),
        'priority' => 31,
        'description' => __( 'Manage banners shown in the feature banners area', 'hello-elementor-child' ),
    ) );

    for ( $i = 1; $i <= 3; $i++ ) {
        $wp_customize->add_setting( "banner_{$i}_image", array( 'default' => '', 'sanitize_callback' => 'esc_url_raw' ) );
        $wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, "banner_{$i}_image", array(
            'label' => sprintf( __( 'Banner %d Image', 'hello-elementor-child' ), $i ),
            'section' => 'banners_section',
            'settings' => "banner_{$i}_image",
        ) ) );

        $wp_customize->add_setting( "banner_{$i}_title", array( 'default' => '', 'sanitize_callback' => 'sanitize_text_field' ) );
        $wp_customize->add_control( "banner_{$i}_title", array(
            'label' => sprintf( __( 'Banner %d Title', 'hello-elementor-child' ), $i ),
            'section' => 'banners_section',
            'type' => 'text',
        ) );

        $wp_customize->add_setting( "banner_{$i}_sub", array( 'default' => '', 'sanitize_callback' => 'sanitize_text_field' ) );
        $wp_customize->add_control( "banner_{$i}_sub", array(
            'label' => sprintf( __( 'Banner %d Subtitle', 'hello-elementor-child' ), $i ),
            'section' => 'banners_section',
            'type' => 'text',
        ) );

        $wp_customize->add_setting( "banner_{$i}_link", array( 'default' => '', 'sanitize_callback' => 'esc_url_raw' ) );
        $wp_customize->add_control( "banner_{$i}_link", array(
            'label' => sprintf( __( 'Banner %d Link', 'hello-elementor-child' ), $i ),
            'section' => 'banners_section',
            'type' => 'url',
        ) );
    }
}
add_action( 'customize_register', 'flatsome_customize_register' );

// 1. Hide default price & short description — SAFE VERSION
add_action('wp_head', 'hide_default_variable_product_info');
function hide_default_variable_product_info() {
    // Only run on single product pages
    if (!is_product()) {
        return;
    }

    // Get the product ID safely
    $product_id = get_the_ID();
    if (!$product_id) {
        return;
    }

    // Get product object
    $product = wc_get_product($product_id);
    if (!$product || !is_a($product, 'WC_Product')) {
        return;
    }

    // Only apply to variable products
    if (!$product->is_type('variable')) {
        return;
    }

    // Hide default price and description
    echo '<style>
        
        .woocommerce-product-details__short-description {
            display: none !important;
        }
        .woocommerce-variation-description {
            display: none !important;
        }
        .summary.entry-summary .price {
            display: none !important;
        }

        .summary.entry-summary .custom-variation-price .price {
            display: inline !important;
        }
    </style>';
}
// 2. Inject JS only if valid variable product
add_action('wp_footer', 'custom_variation_preselect_js');
function custom_variation_preselect_js() {
    if (!is_product()) return;

    $product_id = get_the_ID();
    if (!$product_id) return;

    $product = wc_get_product($product_id);
    if (!$product || !is_a($product, 'WC_Product') || !$product->is_type('variable')) {
        return;
    }

    // Now it's safe to output JS
    ?>
    <script type="text/javascript">
    jQuery(document).ready(function($) {
        var form = $('form.variations_form');
        if (!form.length) return;

        var variations = form.data('product_variations');
        if (!variations || variations.length === 0) return;

        setTimeout(function() {
            var firstVar = variations[0];
            $.each(firstVar.attributes, function(attrName, attrValue) {
                var select = $('select[data-attribute_name="' + attrName + '"]');
                if (select.length && attrValue) {
                    select.val(attrValue).trigger('change');
                }
            });
            form.trigger('woocommerce_variation_select_change');
        }, 300);

        form.on('show_variation', function(event, variation) {
            $('.custom-variation-price, .custom-variation-desc').remove();

            if (variation.price_html) {
                $('.product_title').first().after('<div class="custom-variation-price">' + variation.price_html + '</div>');
            }

            // Note: WooCommerce uses 'description' for variation description in newer versions
            var desc = variation.variation_description || variation.description || '';
            if (desc) {
                $('.custom-variation-price').after('<div class="custom-variation-desc">' + desc + '</div>');
            } else {
                $('.custom-variation-price').after('<div class="custom-variation-desc">Select an option to see details.</div>');
            }
        });

        form.on('hide_variation', function() {
            $('.custom-variation-price, .custom-variation-desc').fadeOut(function() { $(this).remove(); });
        });
    });
    </script>
    <?php
}

add_action('wp_footer', 'auto_select_first_variation_js');
function auto_select_first_variation_js() {
    if (!is_product()) return;
    ?>
    <script>
    jQuery(document).ready(function($) {
        setTimeout(function() {
            var $realForm = $('form.variations_form').has('select[name^="attribute_"]');
            if ($realForm.length === 0) return;

            var variations = $realForm.data('product_variations');
            if (!variations || !Array.isArray(variations)) {
                try {
                    var raw = $realForm.attr('data-product_variations');
                    if (raw) variations = JSON.parse(raw.replace(/&quot;/g, '"'));
                } catch (e) {
                    return;
                }
            }
            if (!variations || variations.length === 0) return;

            var first = variations.find(v => v.variation_is_active && v.variation_is_visible);
            if (!first) return;

            var changed = false;
            $.each(first.attributes, function(attrName, attrValue) {
                var $select = $realForm.find('select[name="' + attrName + '"]');
                if ($select.length && !$select.val()) {
                    $select.val(attrValue).trigger('change');
                    changed = true;
                }
            });

            if (changed) {
                $realForm.trigger('woocommerce_variation_select_change');
            }
        }, 800);
    });
    </script>
    <?php
}