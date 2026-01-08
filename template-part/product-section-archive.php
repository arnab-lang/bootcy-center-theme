<?php 

global $product;
if ( function_exists('wc_get_product') ) {
    $product = wc_get_product( get_the_ID() );
}
$image = wp_get_attachment_image_src( get_post_thumbnail_id( get_the_ID() ), 'woocommerce_thumbnail' );
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

