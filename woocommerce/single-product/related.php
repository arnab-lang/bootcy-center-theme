<?php
/**
 * Related Products – Swiper Slider Version
 *
 * Override of woocommerce/single-product/related.php
 * Uses Swiper.js structure for sliding related products.
 *
 * @see https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 10.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( $related_products ) :
	// Ensure lazy loading compatibility (as in original)
	if ( function_exists( 'wp_increase_content_media_count' ) ) {
		$content_media_count = wp_increase_content_media_count( 0 );
		if ( $content_media_count < wp_omit_loading_attr_threshold() ) {
			wp_increase_content_media_count( wp_omit_loading_attr_threshold() - $content_media_count );
		}
	}
	?>

	<section class="products-section slider">
		<div class="section-header">
			<h2><?php echo esc_html( apply_filters( 'woocommerce_product_related_products_heading', __( 'Related products', 'woocommerce' ) ) ); ?></h2>
		</div>
		<div class="container">
			<div class="swiper product-slider">
				<div class="swiper-wrapper">

					<?php foreach ( $related_products as $related_product ) : ?>
						<?php
						$post_object = get_post( $related_product->get_id() );
						setup_postdata( $GLOBALS['post'] = $post_object );
						global $product;

						// Primary image
						$image = wp_get_attachment_image_src( get_post_thumbnail_id( $product->get_id() ), 'woocommerce_thumbnail' );

						// Secondary image (first gallery image)
						$attachment_ids = $product->get_gallery_image_ids();
						$secondary_img_url = '';
						if ( $attachment_ids && ! empty( $attachment_ids ) ) {
							$secondary_img_data = wp_get_attachment_image_src( $attachment_ids[0], 'woocommerce_thumbnail' );
							if ( $secondary_img_data ) {
								$secondary_img_url = $secondary_img_data[0];
							}
						}
						?>

						<div class="swiper-slide">
							<div class="product-card">
								<div class="product-img-wrap">
									<img src="<?php echo esc_url( $image ? $image[0] : wc_placeholder_img_src() ); ?>" alt="<?php echo esc_attr( $product->get_name() ); ?>">
									<?php if ( $secondary_img_url ) : ?>
										<img src="<?php echo esc_url( $secondary_img_url ); ?>" alt="<?php echo esc_attr( $product->get_name() ); ?>" class="hover-img">
									<?php endif; ?>

									<button class="wishlist-btn" onclick="toggleWishlist(this)" aria-label="Add to wishlist">
										<i class="fa fa-regular fa-heart" aria-hidden="true"></i>
									</button>

									<div class="product-actions">
										<button class="action-btn" 
												onclick="openQuickView(this)" 
												data-id="<?php echo esc_attr( $product->get_id() ); ?>" 
												aria-label="Quick view">
											<i class="fa fa-regular fa-eye" aria-hidden="true"></i>
										</button>
									</div>

									<a href="?add-to-cart=<?php echo esc_attr( $product->get_id() ); ?>" class="add-to-cart-btn"><?php esc_html_e( 'Add to Cart', 'woocommerce' ); ?></a>
								</div>
								<div class="product-details">
									<div class="cat-name">
										<?php echo wc_get_product_category_list( $product->get_id(), ', ', '<span>', '</span>' ); ?>
									</div>
									<a href="<?php echo esc_url( get_permalink() ); ?>">
										<div class="prod-title"><?php echo esc_html( $product->get_name() ); ?></div>
									</a>
									<div class="prod-price"><?php echo $product->get_price_html(); ?></div>
								</div>
							</div>
						</div>

					<?php endforeach; ?>

				</div> <!-- .swiper-wrapper -->

				<div class="swiper-button-next"></div>
				<div class="swiper-button-prev"></div>
			</div> <!-- .swiper -->
		</div> <!-- .container -->
	</section>

	<?php
endif;

wp_reset_postdata();

// hr line

echo '<hr class="section-divider" style="margin-top: 20px; margin-bottom: -25px; border-top: 1px solid rgba(0,0,0,.05)" >';

get_template_part( 'template-part/four-col-products' );
