<?php
/**
 * Theme functions and definitions
 */

function hello_elementor_child_enqueue_scripts() {
    // 1. Parent & Child Styles
    wp_enqueue_style( 'hello-elementor-child-style', get_stylesheet_directory_uri() . '/style.css', array('hello-elementor-theme-style'), '1.0.0' );

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