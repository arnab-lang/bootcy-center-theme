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
        <!-- Hamburger Menu Button - Visible only on mobile -->
        <button class="hamburger-menu" aria-expanded="false" aria-controls="mobile-sidebar">
            <span class="hamburger-icon"></span>
            <span class="screen-reader-text"><?php esc_html_e( 'Toggle filters menu', 'bootcy-center-theme' ); ?></span>
        </button>

        <!-- Mobile Sidebar Overlay -->
        <div class="sidebar-overlay"></div>

        <!-- Desktop Sidebar (Visible on 768px and above) -->
        <aside class="shop-sidebar col col-3 desktop-sidebar">
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
                    $children = get_terms( array( 
                        'taxonomy' => 'product_cat', 
                        'hide_empty' => true, 
                        'parent' => $cat->term_id 
                    ) );
                    $count = intval( $cat->count );
                    $is_current = is_tax( 'product_cat', $cat->slug );
                    $li_classes = 'cat-item cat-parent' . ( $is_current ? ' current-cat' : '' );

                    echo '<li class="' . esc_attr( $li_classes ) . '" data-term-id="' . esc_attr( $cat->term_id ) . '">';
                    echo '<div class="cat-left">';
                    echo '<div class="cat-left-group">';
                    echo '<a href="' . esc_url( get_term_link( $cat ) ) . '" class="cat-link">' . esc_html( $cat->name ) . '</a>';
                    if ( ! empty( $children ) ) {
                        echo '<button class="cat-toggle" aria-expanded="false" aria-label="' . esc_attr__( 'Expand categories', 'bootcy-center-theme' ) . '">';
                        echo '<i class="fa fa-regular fa-chevron-right" aria-hidden="true"></i>';
                        echo '</button>';
                    }
                    echo '</div>';
                    echo '<span class="cat-count">' . esc_html( $count ) . '</span>';
                    echo '</div>';

                    if ( ! empty( $children ) ) {
                        echo '<ul class="children" style="max-height: 0px;">';
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

                // Keep price filter widget as fallback
                the_widget( 'WC_Widget_Price_Filter' );
            }
            ?>
        </aside>

        <!-- Mobile Sidebar (Hidden on desktop, visible on mobile) -->
        <aside id="mobile-sidebar" class="mobile-sidebar">
            <div class="sidebar-header">
                <h2><?php esc_html_e( 'Filters', 'bootcy-center-theme' ); ?></h2>
                <button class="close-sidebar" aria-label="<?php esc_attr_e( 'Close filters', 'bootcy-center-theme' ); ?>">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            
            <?php
            // Reuse the same sidebar content generation for mobile
            if ( is_active_sidebar( 'sidebar-shop' ) ) {
                dynamic_sidebar( 'sidebar-shop' );
            } else {
                // Same category code as desktop but optimized for mobile
                echo '<div class="widget widget_product_categories">';
                echo '<h3 class="widget-title">' . esc_html__( 'Categories', 'bootcy-center-theme' ) . '</h3>';
                echo '<ul class="product-cat-list">';

                foreach ( $cats as $cat ) {
                    $children = get_terms( array( 
                        'taxonomy' => 'product_cat', 
                        'hide_empty' => true, 
                        'parent' => $cat->term_id 
                    ) );
                    $count = intval( $cat->count );
                    $is_current = is_tax( 'product_cat', $cat->slug );
                    $li_classes = 'cat-item cat-parent' . ( $is_current ? ' current-cat' : '' );

                    echo '<li class="' . esc_attr( $li_classes ) . '" data-term-id="' . esc_attr( $cat->term_id ) . '">';
                    echo '<div class="cat-left">';
                    echo '<div class="cat-left-group">';
                    echo '<a href="' . esc_url( get_term_link( $cat ) ) . '" class="cat-link">' . esc_html( $cat->name ) . '</a>';
                    if ( ! empty( $children ) ) {
                        echo '<button class="cat-toggle" aria-expanded="false" aria-label="' . esc_attr__( 'Expand categories', 'bootcy-center-theme' ) . '">';
                        echo '<i class="fa fa-regular fa-chevron-right" aria-hidden="true"></i>';
                        echo '</button>';
                    }
                    echo '</div>';
                    echo '<span class="cat-count">' . esc_html( $count ) . '</span>';
                    echo '</div>';

                    if ( ! empty( $children ) ) {
                        echo '<ul class="children" style="max-height: 0px;">';
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

                // Mobile-optimized price filter
                echo '<div class="widget woocommerce widget_price_filter">';
                echo '<h2 class="widgettitle">' . esc_html__( 'Filter by price', 'bootcy-center-theme' ) . '</h2>';
                the_widget( 'WC_Widget_Price_Filter' );
                echo '</div>';
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
            echo '<div class="shop-controls">';
            do_action( 'woocommerce_before_shop_loop' );
            echo '</div>';
            
            echo '<section class="products-section">';
            echo '<div class="container">';
            echo '<div class="product-grid" id="product-container">';
            
            if ( woocommerce_product_loop() ) {
                while ( have_posts() ) {
                    the_post();
                    get_template_part('template-part/product-section-archive');
                }

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
            <h2 id="modalTitle"></h2>
            <h3 id="modalPrice"></h3>
            <p id="modalDesc"></p>
            <div>
                <strong><?php esc_html_e( 'Category:', 'bootcy-center-theme' ); ?></strong> 
                <span id="modalCat"></span>
            </div>
            <button class="btn"><?php esc_html_e( 'View Product', 'bootcy-center-theme' ); ?></button>
        </div>
    </div>
</div>

<?php
// Enqueue responsive sidebar styles
add_action('wp_footer', function() {
    ?>
    <style>
        /* Hamburger Menu Button */
        .hamburger-menu {
            display: none;
            background: var(--primary, #333);
            border: none;
            width: 40px;
            height: 40px;
            border-radius: 4px;
            cursor: pointer;
            position: relative;
            z-index: 980;
            margin: 10px 0 10px 10px;
            align-self: flex-start;
        }
        
        .hamburger-icon,
        .hamburger-icon::before,
        .hamburger-icon::after {
            position: absolute;
            top: 50%;
            left: 50%;
            width: 20px;
            height: 2px;
            background: white;
            transform: translate(-50%, -50%);
            transition: all 0.3s ease;
        }
        
        .hamburger-icon::before {
            content: '';
            transform: translate(-50%, -8px);
        }
        
        .hamburger-icon::after {
            content: '';
            transform: translate(-50%, 8px);
        }
        
        /* Mobile Sidebar Styles */
        .mobile-sidebar {
            position: fixed;
            top: 0;
            left: -100%;
            width: 300px;
            height: 100vh;
            background: white;
            box-shadow: 3px 0 10px rgba(0,0,0,0.1);
            z-index: 999;
            overflow-y: auto;
            transition: left 0.3s ease;
            padding: 1rem;
            display: none;
        }
        
        .sidebar-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-bottom: 1rem;
            border-bottom: 1px solid #eee;
            margin-bottom: 1rem;
            margin-top: 40px;
        }
        
        .close-sidebar {
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            color: #333;
            padding: 0;
            line-height: 1;
        }
        
        .sidebar-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
            z-index: 998;
        }
        
        /* Category toggle styling */
        .cat-toggle {
            background: none;
            border: none;
            cursor: pointer;
            margin-left: 8px;
            color: #666;
        }
        
        .cat-toggle i {
            transition: transform 0.3s ease;
        }
        
        .cat-toggle[aria-expanded="true"] i {
            transform: rotate(90deg);
        }
        
        .children {
            overflow: hidden;
            transition: max-height 0.3s ease;
        }
        
        /* Responsive Adjustments */
        @media (max-width: 767px) {
            .hamburger-menu {
                display: block;
            }
            
            .desktop-sidebar {
                display: none;
            }
            
            .mobile-sidebar {
                display: block;
            }
            
            .mobile-sidebar.active {
                left: 0;
            }
            
            .sidebar-overlay.active {
                opacity: 1;
                visibility: visible;
            }
            
            /* Improve mobile form elements */
            .price_slider_wrapper {
                margin: 1rem 0;
            }
            
            .filter-actions {
                display: flex;
                flex-direction: column;
                gap: 0.5rem;
            }
            
            .button, .clear-price {
                width: 100%;
                padding: 10px;
                margin: 5px 0;
            }
            
            .price_label {
                margin-top: 10px;
                display: block;
            }
            
            /* Main content adjustments */
            .shop-main {
                width: 100%;
                padding-left: 0 !important;
            }
            
            .shop-wrap {
                flex-direction: column;
            }
        }
        
        /* Desktop Styles */
        @media (min-width: 768px) {
            .shop-wrap {
                display: flex;
            }
            
            .desktop-sidebar {
                display: block;
                width: 25%;
                padding-right: 20px;
            }
            
            .shop-main {
                width: 75%;
            }
            
            .hamburger-menu,
            .mobile-sidebar,
            .sidebar-overlay {
                display: none;
            }
        }
    </style>
    <?php
});

// Enqueue sidebar toggle script
add_action('wp_footer', function() {
    ?>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const hamburger = document.querySelector('.hamburger-menu');
        const sidebar = document.getElementById('mobile-sidebar');
        const closeBtn = document.querySelector('.close-sidebar');
        const overlay = document.querySelector('.sidebar-overlay');
        
        if (!hamburger || !sidebar || !closeBtn || !overlay) return;
        
        function toggleSidebar() {
            const isExpanded = hamburger.getAttribute('aria-expanded') === 'true';
            hamburger.setAttribute('aria-expanded', !isExpanded);
            sidebar.classList.toggle('active');
            overlay.classList.toggle('active');
            document.body.style.overflow = sidebar.classList.contains('active') ? 'hidden' : '';
        }
        
        hamburger.addEventListener('click', toggleSidebar);
        closeBtn.addEventListener('click', toggleSidebar);
        overlay.addEventListener('click', toggleSidebar);
        
        // Handle category toggles
        document.querySelectorAll('.cat-toggle').forEach(button => {
            button.addEventListener('click', function() {
                const isExpanded = this.getAttribute('aria-expanded') === 'true';
                this.setAttribute('aria-expanded', !isExpanded);
                
                const icon = this.querySelector('i');
                if (icon) {
                    icon.style.transform = isExpanded ? 'rotate(0deg)' : 'rotate(90deg)';
                }
                
                const children = this.closest('.cat-item').querySelector('.children');
                if (children) {
                    children.style.maxHeight = isExpanded ? '0px' : children.scrollHeight + 'px';
                }
            });
        });
        
        // Handle price filter form submission in mobile sidebar
        const priceForms = document.querySelectorAll('.widget_price_filter form');
        priceForms.forEach(form => {
            form.addEventListener('submit', function(e) {
                if (window.innerWidth < 768 && sidebar.classList.contains('active')) {
                    // Close sidebar after filter submission on mobile
                    setTimeout(() => {
                        sidebar.classList.remove('active');
                        overlay.classList.remove('active');
                        document.body.style.overflow = '';
                    }, 300);
                }
            });
        });
    });
    </script>
    <?php
});

get_footer();