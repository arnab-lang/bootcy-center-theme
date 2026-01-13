<?php
/**
 * Single Product Image – Swiper Version
 *
 * Override of WooCommerce's product-image.php to use Swiper.js
 */

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'wc_get_gallery_image_html' ) ) {
	return;
}

global $product;

// Get all gallery image IDs (including featured image)
$gallery_image_ids = $product->get_gallery_image_ids();
$featured_id       = $product->get_image_id();

// Ensure featured image is first
$all_image_ids = array_filter( array_merge( array( $featured_id ), $gallery_image_ids ) );

// If no images, show placeholder
if ( empty( $all_image_ids ) || ! $featured_id ) {
	$wrapper_classname = 'woocommerce-product-gallery__image--placeholder';
	echo '<div class="' . esc_attr( $wrapper_classname ) . '">';
	echo '<img src="' . esc_url( wc_placeholder_img_src( 'woocommerce_single' ) ) . '" alt="' . esc_attr__( 'Awaiting product image', 'woocommerce' ) . '" class="wp-post-image" />';
	echo '</div>';
	return;
}
?>

<div class="swiper-product-gallery">
	<!-- Main Swiper -->
	<div style="--swiper-navigation-color: #000; --swiper-pagination-color: #000" class="swiper mySwiper2">
		<div class="swiper-wrapper">
			<?php foreach ( $all_image_ids as $image_id ) : ?>
				<div class="swiper-slide">
					<?php echo wp_get_attachment_image( $image_id, 'full', false, array( 'loading' => 'lazy' ) ); ?>
				</div>
			<?php endforeach; ?>
		</div>
		<div class="swiper-button-next"></div>
		<div class="swiper-button-prev"></div>
	</div>

	<!-- Thumbnail Swiper -->
	<div thumbsSlider class="swiper mySwiper">
		<div class="swiper-wrapper">
			<?php foreach ( $all_image_ids as $image_id ) : ?>
				<div class="swiper-slide">
					<?php echo wp_get_attachment_image( $image_id, 'shop_thumbnail', false, array( 'loading' => 'lazy' ) ); ?>
				</div>
			<?php endforeach; ?>
		</div>
		<div class="swiper-button-prev swiper-button-prev-thumbs"></div>
  		<div class="swiper-button-next swiper-button-next-thumbs"></div>
	</div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
	if (typeof Swiper !== 'undefined') {
		var swiperThumbs = new Swiper(".mySwiper", {
			loop: true,
			spaceBetween: 10,
			slidesPerView: 4,
			freeMode: true,
			watchSlidesProgress: true,
			// Enable navigation for thumbs
			navigation: {
				nextEl: ".swiper-button-next-thumbs",
				prevEl: ".swiper-button-prev-thumbs",
			},
		});

		var swiperMain = new Swiper(".mySwiper2", {
			loop: true,
			spaceBetween: 10,
			navigation: {
				nextEl: ".swiper-button-next",
				prevEl: ".swiper-button-prev",
			},
			thumbs: {
				swiper: swiperThumbs,
			},
		});
	}
});
</script>