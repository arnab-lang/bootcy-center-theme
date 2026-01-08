// 1. Hero Slider (Keep this)
const heroSwiper = new Swiper('.hero-slider', {
    loop: true, effect: 'fade',
    autoplay: { delay: 5000 },
    navigation: { nextEl: '.swiper-button-next', prevEl: '.swiper-button-prev' },
    pagination: { el: '.swiper-pagination', clickable: true },
});

// Category slider
const categorySwiper = new Swiper('.category-slider', {
    slidesPerView: 4,
    spaceBetween: 20,
    navigation: {
        nextEl: '.category-slider .swiper-button-next',
        prevEl: '.category-slider .swiper-button-prev',
    },
    breakpoints: {
        320: { slidesPerView: 1 },
        540: { slidesPerView: 2 },
        900: { slidesPerView: 3 },
        1200: { slidesPerView: 4 },
    },
});

// All Products Slider
const productSwiper = new Swiper('.product-slider', {
    slidesPerView: 4,
    spaceBetween: 20,
    navigation: {
        nextEl: '.product-slider .swiper-button-next',
        prevEl: '.product-slider .swiper-button-prev',
    },
    breakpoints: {
        320: { slidesPerView: 1 },
        540: { slidesPerView: 2 },
        900: { slidesPerView: 3 },
        1200: { slidesPerView: 4 },
    },
});

// 2. Quick View AJAX Logic
const modal = document.getElementById('quickViewModal');
const modalBody = document.querySelector('.modal-content'); // We will inject HTML here

window.openQuickView = function(btn) {
    const productId = btn.getAttribute('data-id');

    // Show Modal & Loading State
    modal.style.display = 'flex';
    modalBody.innerHTML = '<div class="loading-spinner"><i class="fa-solid fa-spinner fa-spin" aria-hidden="true"></i> Loading...</div>';

    // Perform AJAX Call
    jQuery.ajax({
        url: flatsome_params.ajax_url,
        type: 'POST',
        data: {
            action: 'load_quick_view',
            product_id: productId,
            nonce: flatsome_params.nonce
        },
        success: function(response) {
            // Inject content
            modalBody.innerHTML = '<span class="modal-close" onclick="closeModal()">&times;</span>' + response;
            
            // Initialize the Gallery Swiper inside the modal
            new Swiper('.qv-swiper', {
                loop: true,
                navigation: { nextEl: '.swiper-button-next', prevEl: '.swiper-button-prev' },
                pagination: { el: '.swiper-pagination', clickable: true },
            });

            // Trigger WooCommerce Variation Scripts (if variable product)
            const form = jQuery(modalBody).find('.variations_form');
            if ( form.length > 0 ) {
                form.wc_variation_form();
            }
        },
        error: function() {
            modalBody.innerHTML = '<span class="modal-close" onclick="closeModal()">&times;</span><p>Error loading product.</p>';
        }
    });
}

window.closeModal = function() {
    modal.style.display = 'none';
    modalBody.innerHTML = ''; // Clear content
}

// Close on outside click
window.onclick = function(event) {
    if (event.target == modal) {
        window.closeModal();
    }
}

// 3. Wishlist (Visual Only)
window.toggleWishlist = function(btn) {
    btn.classList.toggle('active');
    const icon = btn.querySelector('i');
    icon.classList.toggle('fa-solid');
    icon.classList.toggle('fa-regular');
}