
    <section class="products-four-col-section">
        <div class="container">
            <div class="four-cols">
                <div class="col">
                    <div class="col-header"><h3>Latest</h3><span class="underline"></span></div>
                    <ul class="product-list">
                        <?php
                        $latest_products = wc_get_products( array( 'limit' => 4, 'orderby' => 'date', 'return' => 'objects' ) );
                        foreach ( $latest_products as $prod ) : ?>
                            <li>
                                <a class="thumb" href="<?php echo esc_url( $prod->get_permalink() ); ?>"><?php echo $prod->get_image( 'thumbnail' ); ?></a>
                                <div class="meta">
                                    <a class="title" href="<?php echo esc_url( $prod->get_permalink() ); ?>"><?php echo esc_html( $prod->get_name() ); ?></a>
                                    <?php echo wc_get_rating_html( $prod->get_average_rating() ); ?>
                                    <div class="price"><?php echo $prod->get_price_html(); ?></div>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <div class="col">
                    <div class="col-header"><h3>Best Selling</h3><span class="underline"></span></div>
                    <ul class="product-list">
                        <?php
                        $best_sellers = wc_get_products( array( 'limit' => 4, 'orderby' => 'total_sales', 'return' => 'objects' ) );
                        foreach ( $best_sellers as $prod ) : ?>
                            <li>
                                <a class="thumb" href="<?php echo esc_url( $prod->get_permalink() ); ?>"><?php echo $prod->get_image( 'thumbnail' ); ?></a>
                                <div class="meta">
                                    <a class="title" href="<?php echo esc_url( $prod->get_permalink() ); ?>"><?php echo esc_html( $prod->get_name() ); ?></a>
                                    <?php echo wc_get_rating_html( $prod->get_average_rating() ); ?>
                                    <div class="price"><?php echo $prod->get_price_html(); ?></div>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <div class="col">
                    <div class="col-header"><h3>Featured</h3><span class="underline"></span></div>
                    <ul class="product-list">
                        <?php
                        $featured = wc_get_products( array( 'limit' => 4, 'featured' => true, 'return' => 'objects' ) );
                        foreach ( $featured as $prod ) : ?>
                            <li>
                                <a class="thumb" href="<?php echo esc_url( $prod->get_permalink() ); ?>"><?php echo $prod->get_image( 'thumbnail' ); ?></a>
                                <div class="meta">
                                    <a class="title" href="<?php echo esc_url( $prod->get_permalink() ); ?>"><?php echo esc_html( $prod->get_name() ); ?></a>
                                    <?php echo wc_get_rating_html( $prod->get_average_rating() ); ?>
                                    <div class="price"><?php echo $prod->get_price_html(); ?></div>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <div class="col">
                    <div class="col-header"><h3>Top Rated Products</h3><span class="underline"></span></div>
                    <ul class="product-list">
                        <?php
                        $top_rated = wc_get_products( array( 'limit' => 4, 'orderby' => 'rating', 'return' => 'objects' ) );
                        foreach ( $top_rated as $prod ) : ?>
                            <li>
                                <a class="thumb" href="<?php echo esc_url( $prod->get_permalink() ); ?>"><?php echo $prod->get_image( 'thumbnail' ); ?></a>
                                <div class="meta">
                                    <a class="title" href="<?php echo esc_url( $prod->get_permalink() ); ?>"><?php echo esc_html( $prod->get_name() ); ?></a>
                                    <?php echo wc_get_rating_html( $prod->get_average_rating() ); ?>
                                    <div class="price"><?php echo $prod->get_price_html(); ?></div>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>
    </section>
