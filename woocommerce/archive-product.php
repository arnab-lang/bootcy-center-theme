<?php
/*
Template Name: Custom WooCommerce Shop Archive

*/


get_header(); ?>

    <div class="swiper hero-slider">
        <div class="swiper-wrapper">
            <?php for ( $i = 1; $i <= 3; $i++ ) :
                $img = get_theme_mod( "hero_slide_{$i}_image" );
                $subtitle = get_theme_mod( "hero_slide_{$i}_subtitle" );
                $title = get_theme_mod( "hero_slide_{$i}_title" );
                $link = get_theme_mod( "hero_slide_{$i}_link" );
                if ( ! $img ) continue; ?>
                <div class="swiper-slide" style="background-image: url('<?php echo esc_url( $img ); ?>');">
                    <div class="slide-overlay"></div>
                    <div class="container" style="width: 100%;">
                        <div class="slide-content">
                            <?php if ( $subtitle ) : ?><div class="slide-subtitle"><?php echo esc_html( $subtitle ); ?></div><?php endif; ?>
                            <?php if ( $title ) : ?><div class="slide-title"><?php echo wp_kses_post( nl2br( $title ) ); ?></div><?php endif; ?>
                            <?php if ( $link ) : ?><a href="<?php echo esc_url( $link ); ?>" class="btn btn-white-outline">View</a><?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endfor; ?>
        </div>
        <div class="swiper-button-next"></div>
        <div class="swiper-button-prev"></div>
        <div class="swiper-pagination"></div>
    </div>

    <section class="banners-section">
        <div class="container">
            <div class="banner-grid">
                <?php for ( $i = 1; $i <= 3; $i++ ) :
                    $img = get_theme_mod( "banner_{$i}_image" );
                    $title = get_theme_mod( "banner_{$i}_title" );
                    $sub = get_theme_mod( "banner_{$i}_sub" );
                    $link = get_theme_mod( "banner_{$i}_link" ); // New: Get banner link
                    if ( ! $img ) continue; ?>
                    <div class="banner-item">
                        <a href="<?php echo esc_url( $link ); ?>">
                            <img src="<?php echo esc_url( $img ); ?>" class="banner-img" alt="<?php echo esc_attr( $title ); ?>">
                            <div class="banner-content">
                                <?php if ( $title ) : ?><h3 class="banner-title"><?php echo esc_html( $title ); ?></h3><?php endif; ?>
                                <?php if ( $sub ) : ?><div class="banner-sub"><?php echo esc_html( $sub ); ?></div><?php endif; ?>
                            </div>
                        </a>
                    </div>
                <?php endfor; ?>
            </div>
        </div>
    </section>

    <section class="products-section">
        <div class="section-header">
            <h2>Best Sellers</h2>
        </div>
        <div class="container">

            
            <div class="product-grid" id="product-container">
                <?php
                // Support both 'paged' and 'page' query vars and use the main query so WP handles 404/pagination properly
                $paged = max( 1, get_query_var('paged') ? get_query_var('paged') : ( get_query_var('page') ? get_query_var('page') : 1 ) );

                // Use the main loop; WooCommerce/WordPress will provide the products on the shop archive
                if ( have_posts() ) {
                    while ( have_posts() ) : the_post();
                        get_template_part('template-part/product-section-archive');
                    endwhile;
                } else {
                    echo '<p>No products found. Please add products in WooCommerce.</p>';
                }

                ?>
            </div>

            <?php
            // Pagination (use main query) - placed below the grid
            global $wp_query;
            if ( $wp_query->max_num_pages > 1 ) {
                $big = 999999999; // need an unlikely integer
                echo '<div class="pagination-wrap">';
                echo paginate_links( array(
                    'base' => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
                    'format' => '/page/%#%/',
                    'current' => max( 1, $paged ),
                    'total' => $wp_query->max_num_pages,
                    'prev_text' => '&laquo; Prev',
                    'next_text' => 'Next &raquo;',
                    'type' => 'list',
                ) );
                echo '</div>';
            }
            ?>

        </div>
    </section>


    <section class="products-section slider">
        <div class="section-header">
            <h2>All Products</h2>
        </div>
        <div class="container">
            <div class="swiper product-slider">
                <div class="swiper-wrapper">
                    <?php
                    $args = array(
                        'post_type' => 'product',
                        'posts_per_page' => -1, // Display all products
                        'post_status' => 'publish',
                    );
                    $all_products_query = new WP_Query( $args );

                    if ( $all_products_query->have_posts() ) {
                        while ( $all_products_query->have_posts() ) : $all_products_query->the_post();
                            global $product;
                            $image = wp_get_attachment_image_src( get_post_thumbnail_id( $product->get_id() ), 'woocommerce_thumbnail' );
                            // 2. Get Secondary Image (First image from gallery)
                            $attachment_ids = $product->get_gallery_image_ids();
                            $secondary_img_url = '';
                            if ( $attachment_ids && ! empty($attachment_ids) ) {
                                // Get the first gallery image
                                $secondary_img_data = wp_get_attachment_image_src( $attachment_ids[0], 'woocommerce_thumbnail' );
                                if ( $secondary_img_data ) {
                                    $secondary_img_url = $secondary_img_data[0];
                                }
                            }
                            ?>
                            <div class="swiper-slide">
                                <div class="product-card">
                                    <div class="product-img-wrap">
                                        <img src="<?php echo $image ? $image[0] : wc_placeholder_img_src(); ?>" alt="<?php the_title(); ?>">
                                        <?php if ( $secondary_img_url ) : ?>
                                            <img src="<?php echo esc_url( $secondary_img_url ); ?>" alt="<?php the_title(); ?>" class="hover-img">
                                        <?php endif; ?>
                                        <button class="wishlist-btn" onclick="toggleWishlist(this)" aria-label="Add to wishlist">
                                            <i class="fa fa-regular fa-heart" aria-hidden="true"></i>
                                        </button>

                                        <div class="product-actions">
                                            <button class="action-btn" 
                                                    onclick="openQuickView(this)" 
                                                    data-id="<?php echo $product->get_id(); ?>" aria-label="Quick view">
                                                <i class="fa fa-regular fa-eye" aria-hidden="true"></i>
                                            </button>
                                        </div>
                                        
                                        <a href="?add-to-cart=<?php echo $product->get_id(); ?>" class="add-to-cart-btn">Add to Cart</a>
                                    </div>
                                    <div class="product-details">
                                        <div class="cat-name"><?php echo wc_get_product_category_list($product->get_id()); ?></div>
                                        <a href="<?php the_permalink(); ?>"><div class="prod-title"><?php the_title(); ?></div></a>
                                        <div class="prod-price"><?php echo $product->get_price_html(); ?></div>
                                    </div>
                                </div>
                            </div>
                            <?php
                        endwhile;
                        wp_reset_postdata();
                    } else {
                        echo '<p>No products found.</p>';
                    }
                    ?>
                </div>
                <div class="swiper-button-next"></div>
                <div class="swiper-button-prev"></div>
            </div>
        </div>
    </section>

    <section class="categories-slider-section">
        <div class="section-header">
            <h2>Shop by Category</h2>
        </div>
        <div class="container">
            <div class="swiper category-slider">
                <div class="swiper-wrapper">
                    <?php
                    $product_cats = get_terms( 'product_cat', array(
                        'hide_empty' => true,
                        'orderby' => 'count',
                        'order' => 'DESC',
                    ) );

                    if ( ! empty( $product_cats ) && ! is_wp_error( $product_cats ) ) {
                        foreach ( $product_cats as $cat ) {
                            $thumb_id = get_term_meta( $cat->term_id, 'thumbnail_id', true );
                            $img = $thumb_id ? wp_get_attachment_image_url( $thumb_id, 'medium' ) : wc_placeholder_img_src();
                            $cat_link = get_term_link( $cat );
                            ?>
                            <div class="swiper-slide">
                                <a href="<?php echo esc_url( $cat_link ); ?>" class="category-card">
                                    <div class="category-img-wrap">
                                        <img src="<?php echo esc_url( $img ); ?>" alt="<?php echo esc_attr( $cat->name ); ?>">
                                    </div>
                                    <div class="category-info">
                                        <div class="cat-name"><?php echo esc_html( $cat->name ); ?></div>
                                        <div class="cat-count"><?php echo intval( $cat->count ); ?> products</div>
                                    </div>
                                </a>
                            </div>
                            <?php
                        }
                    }
                    ?>
                </div>
                <div class="swiper-button-next"></div>
                <div class="swiper-button-prev"></div>
            </div>
        </div>
    </section>

    <section class="three-grid-section">
        <div class="container">
            <div class="three-grid">
                <div class="feature-item">
                    <div class="feature-icon"><i class="fa fa-check" aria-hidden="true"></i></div>
                    <h3 class="feature-title">Free Shipping On All Orders</h3>
                    <p class="feature-desc">Get Free Shipping on all orders over $75 and free returns to our UK returns centre! Items are dispatched from the US and will arrive in 5-8 days.</p>
                </div>
                <div class="feature-item">
                    <div class="feature-icon"><i class="fa fa-heart" aria-hidden="true"></i></div>
                    <h3 class="feature-title">Amazing Customer Service</h3>
                    <p class="feature-desc">Get Free Shipping on all orders over $75 and free returns to our UK returns centre! Items are dispatched from the US and will arrive in 5-8 days.</p>
                </div>
                <div class="feature-item">
                    <div class="feature-icon"><i class="fa fa-star" aria-hidden="true"></i></div>
                    <h3 class="feature-title">No Customs or Duty Fees!</h3>
                    <p class="feature-desc">We pay these fees so you don't have to! The total billed at checkout is the final amount you pay, inclusive of VAT, with no additional charges at the time of delivery!</p>
                </div>
            </div>
        </div>
    </section>

    <section class="latest-news-section">
        <div class="section-header">
            <h2>Latest News</h2>
        </div>
        <div class="container">
            <div class="news-grid">
                <?php
                $news_args = array(
                    'post_type'      => 'post',
                    'posts_per_page' => 4,
                    'post_status'    => 'publish',
                );
                $news_query = new WP_Query( $news_args );
                if ( $news_query->have_posts() ) :
                    while ( $news_query->have_posts() ) : $news_query->the_post(); ?>
                        <div class="news-card">
                            <div class="news-img-wrap">
                                <a href="<?php the_permalink(); ?>">
                                    <?php if ( has_post_thumbnail() ) : ?>
                                        <?php the_post_thumbnail( 'medium' ); ?>
                                    <?php else : ?>
                                        <img src="<?php echo get_template_directory_uri() . '/assets/images/placeholder.png'; ?>" alt="<?php the_title_attribute(); ?>">
                                    <?php endif; ?>
                                    <div class="news-date">
                                        <span class="day"><?php echo get_the_date('d'); ?></span>
                                        <span class="month"><?php echo get_the_date('M'); ?></span>
                                    </div>
                                </a>
                            </div>
                            <div class="news-details">
                                <a href="<?php the_permalink(); ?>"><h3 class="news-title"><?php the_title(); ?></h3></a>
                                <div class="news-excerpt"><?php echo wp_trim_words( get_the_excerpt(), 20, '...' ); ?></div>
                            </div>
                        </div>
                    <?php endwhile;
                    wp_reset_postdata();
                endif;
                ?>
            </div>
        </div>
    </section>


    <?php get_template_part( 'template-part/four-col-products' ); ?>

<?php get_footer(); ?>