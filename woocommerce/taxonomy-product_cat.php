<?php
/**
 * Product Category Archive - category pages only
 */
defined( 'ABSPATH' ) || exit;
get_header(); ?>

<div class="container product-category-archive">

    <?php if ( function_exists( 'woocommerce_breadcrumb' ) ) : ?>
        <nav class="breadcrumbs"><?php woocommerce_breadcrumb(); ?></nav>
    <?php endif; ?>

    <div class="shop-wrap row">
        <aside class="shop-sidebar col col-3">
            <?php
            if ( is_active_sidebar( 'sidebar-shop' ) ) {
                dynamic_sidebar( 'sidebar-shop' );
            } else {
                // Custom product categories list (improved UI)
                $cats = get_terms( array(
                    'taxonomy' => 'product_cat',
                    'hide_empty' => true,
                    'parent' => 0,
                    'orderby' => 'name',
                    'order' => 'ASC',
                ) );

                echo '<div class="widget widget_product_categories">';
                echo '<h3 class="widget-title">' . esc_html__( 'Categories', 'bootcy-center-theme' ) . '</h3>';
                echo '<ul class="product-cat-list">';

                foreach ( $cats as $cat ) {
                    $children = get_terms( array( 'taxonomy' => 'product_cat', 'hide_empty' => true, 'parent' => $cat->term_id ) );
                    $count = intval( $cat->count );
                    $is_current = is_tax( 'product_cat', $cat->slug );
                    $li_classes = 'cat-item cat-parent' . ( $is_current ? ' current-cat' : '' );

                    echo '<li class="' . esc_attr( $li_classes ) . '" data-term-id="' . esc_attr( $cat->term_id ) . '">';
                    // Build left row with toggle on the left, title in middle and count on the right
                    echo '<div class="cat-left">';
                    echo '<div class="cat-left-group">';
                    echo '<a href="' . esc_url( get_term_link( $cat ) ) . '" class="cat-link">' . esc_html( $cat->name ) . '</a>';
                    if ( ! empty( $children ) ) {
                        // Chevron toggle placed before the link for better alignment
                        echo '<button class="cat-toggle" aria-expanded="false" aria-label="' . esc_attr__( 'Expand categories', 'bootcy-center-theme' ) . '">';
                        echo '<i class="fa fa-regular fa-chevron-right" aria-hidden="true"></i>';
                        echo '</button>';
                    }
                    echo '</div>'; // .cat-left-group
                    echo '<span class="cat-count">' . esc_html( $count ) . '</span>';
                    echo '</div>';

                    if ( ! empty( $children ) ) {
                        echo '<ul class="children">';
                        foreach ( $children as $child ) {
                            $child_active = is_tax( 'product_cat', $child->slug ) ? ' current-cat' : '';
                            echo '<li class="cat-item' . $child_active . '">';
                            echo '<a href="' . esc_url( get_term_link( $child ) ) . '">' . esc_html( $child->name ) . '</a>';
                            echo '<span class="cat-count">' . esc_html( intval( $child->count ) ) . '</span>';
                            echo '</li>';
                        }
                        echo '</ul>';
                    }

                    echo '</li>';
                }

                echo '</ul>';
                echo '</div>';

                // Keep price filter widget as fallback (below categories)
                the_widget( 'WC_Widget_Price_Filter' );
            }
            ?>
        </aside>

        <main class="shop-main col col-9 category">
            <header class="woocommerce-products-header">
                <?php if ( apply_filters( 'woocommerce_show_page_title', true ) ) : ?>
                    <h1 class="woocommerce-products-header__title page-title"><?php woocommerce_page_title(); ?></h1>
                <?php endif; ?>

                <?php do_action( 'woocommerce_archive_description' ); ?>
            </header>

            <?php
            // Shows result count & sorting dropdown (wrapped for styling)
            echo '<div class="shop-controls">';
            do_action( 'woocommerce_before_shop_loop' );
            echo '</div>';
            echo '<section class="products-section">';
            echo '<div class="container">';
            echo '<div class="product-grid" id="product-container">';
                if ( woocommerce_product_loop() ) {
                    // woocommerce_product_loop_start();

                    while ( have_posts() ) {
                        the_post();
                        get_template_part('template-part/product-section-archive');
                    }

                    // woocommerce_product_loop_end();

                    // Pagination
                    do_action( 'woocommerce_after_shop_loop' );
                } else {
                    do_action( 'woocommerce_no_products_found' );
                }
            echo '</div>'; // #product-container
            echo '</div>'; // .container
            echo '</section>';
            ?>
        </main>
    </div> <!-- .shop-wrap -->

    <?php get_template_part( 'template-part/four-col-products' ); ?>


</div>

<div class="modal-overlay" id="quickViewModal">
    <div class="modal-content">
        <span class="modal-close" onclick="closeModal()">&times;</span>
        <img src="" alt="Product" class="modal-img" id="modalImg">
        <div class="modal-details">
            <h2 style="font-size:28px; margin-bottom:10px;" id="modalTitle"></h2>
            <h3 style="color:var(--primary); font-size:24px; margin-bottom:20px;" id="modalPrice"></h3>
            <p style="margin-bottom:20px; color:#777;" id="modalDesc"></p>
            <div style="margin-bottom: 20px;">
                <strong>Category:</strong> <span style="color:#999;" id="modalCat"></span>
            </div>
            <button class="btn" style="background:var(--accent); color:#111; border:none; width:100%;">View Product</button>
        </div>
    </div>
</div>
<?php get_footer(); ?>