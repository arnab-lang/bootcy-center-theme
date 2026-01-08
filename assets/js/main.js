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
function ensureQuickViewModal() {
    let modal = document.getElementById('quickViewModal');
    if ( ! modal ) {
        modal = document.createElement('div');
        modal.id = 'quickViewModal';
        modal.className = 'modal-overlay';
        modal.innerHTML = '<div class="modal-content"></div>';
        document.body.appendChild(modal);
    }

    let modalBody = modal.querySelector('.modal-content');
    if ( ! modalBody ) {
        modalBody = document.createElement('div');
        modalBody.className = 'modal-content';
        modal.appendChild(modalBody);
    }

    return { modal, modalBody };
}

window.openQuickView = function(btn) {
    if ( ! btn ) return;

    const productId = btn.getAttribute('data-id');
    if ( ! productId ) {
        console.warn('openQuickView: button missing data-id');
        return;
    }

    const els = ensureQuickViewModal();
    const modal = els.modal;
    const modalBody = els.modalBody;

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
            
            // Initialize the Gallery Swiper inside the modal (if present)
            if ( document.querySelector('.qv-swiper') ) {
                new Swiper('.qv-swiper', {
                    loop: true,
                    navigation: { nextEl: '.swiper-button-next', prevEl: '.swiper-button-prev' },
                    pagination: { el: '.swiper-pagination', clickable: true },
                });
            }

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
    const modal = document.getElementById('quickViewModal');
    const modalBody = modal ? modal.querySelector('.modal-content') : null;
    if ( modal ) modal.style.display = 'none';
    if ( modalBody ) modalBody.innerHTML = ''; // Clear content
}

// Close on outside click
window.onclick = function(event) {
    const modal = document.getElementById('quickViewModal');
    if ( modal && event.target == modal ) {
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

// 4. Product categories toggle (improved UI)
document.addEventListener('DOMContentLoaded', function() {
    const toggles = document.querySelectorAll('.product-cat-list .cat-toggle');

    toggles.forEach(function(btn) {
        const li = btn.closest('.cat-item');
        const children = li ? li.querySelector('.children') : null;

        // Initialize open state on load
        if (li && li.classList.contains('open') && children) {
            children.style.maxHeight = children.scrollHeight + 'px';
            btn.setAttribute('aria-expanded', 'true');
            btn.classList.add('open');
        }

        btn.addEventListener('click', function(e) {
            e.preventDefault();
            if (!li) return;

            const isOpen = li.classList.contains('open');

            // If opening, close siblings (accordion behavior)
            if (!isOpen) {
                const siblings = li.parentElement ? li.parentElement.querySelectorAll(':scope > .cat-item.open') : [];
                siblings.forEach(function(sib) {
                    if (sib === li) return;
                    sib.classList.remove('open');
                    const sibChildren = sib.querySelector('.children');
                    const sibBtn = sib.querySelector('.cat-toggle');
                    if (sibChildren) sibChildren.style.maxHeight = '0';
                    if (sibBtn) { sibBtn.setAttribute('aria-expanded', 'false'); sibBtn.classList.remove('open'); }
                });
            }

            if (children) {
                if (isOpen) {
                    // close
                    children.style.maxHeight = children.scrollHeight + 'px';
                    requestAnimationFrame(function() {
                        children.style.maxHeight = '0';
                    });
                    li.classList.remove('open');
                    btn.setAttribute('aria-expanded', 'false');
                    btn.classList.remove('open');
                } else {
                    // open
                    children.style.maxHeight = children.scrollHeight + 'px';
                    li.classList.add('open');
                    btn.setAttribute('aria-expanded', 'true');
                    btn.classList.add('open');
                }
            }
        });
    });

    // Auto-open parents if current child is active
    const current = document.querySelectorAll('.product-cat-list .current-cat');
    current.forEach(function(node) {
        const parentLi = node.closest('li.cat-item');
        if (parentLi) {
            parentLi.classList.add('open');
            const btn = parentLi.querySelector('.cat-toggle');
            const children = parentLi.querySelector('.children');
            if (btn) { btn.setAttribute('aria-expanded', 'true'); btn.classList.add('open'); }
            if (children) children.style.maxHeight = children.scrollHeight + 'px';
        }
    });

    /* Price filter enhancement: add Clear button, live values and reset */
    jQuery('.widget_price_filter').each(function() {
        var $w = jQuery(this);
        var $slider = $w.find('.price_slider');
        var $from = $w.find('.price_label .from');
        var $to = $w.find('.price_label .to');
        var $minInput = $w.find('input.min_price, input[name="min_price"]');
        var $maxInput = $w.find('input.max_price, input[name="max_price"]');
        var $filterBtn = $w.find('.price_slider_amount .button');

        // Append Clear button if not present
        if (!$w.find('.clear-price').length) {
            var $clear = jQuery('<button type="button" class="clear-price">Clear</button>');

            // Group filter and clear into .filter-actions for layout
            var $actions = $w.find('.filter-actions');
            if (!$actions.length) {
                $actions = jQuery('<div class="filter-actions"></div>');
                $w.find('.price_slider_amount .button').first().before($actions);
            }

            $actions.append($filterBtn);
            $actions.append($clear);

            $clear.on('click', function(e) {
                e.preventDefault();
                if ($slider.length && typeof $slider.slider === 'function') {
                    var min = $slider.slider('option', 'min');
                    var max = $slider.slider('option', 'max');
                    $slider.slider('values', [min, max]);
                    // Clear inputs
                    $minInput.val('');
                    $maxInput.val('');
                    // Update labels
                    if ($from.length) $from.text(min);
                    if ($to.length) $to.text(max);
                }
            });
        }

        // Helper to update label from slider
        function updateLabels(ui) {
            var minV = ui ? ui.values[0] : ( $slider.length && typeof $slider.slider === 'function' ? $slider.slider('values',0) : null );
            var maxV = ui ? ui.values[1] : ( $slider.length && typeof $slider.slider === 'function' ? $slider.slider('values',1) : null );
            if ($from.length && minV !== null) $from.text(minV);
            if ($to.length && maxV !== null) $to.text(maxV);
            if ($minInput.length && minV !== null) $minInput.val(minV);
            if ($maxInput.length && maxV !== null) $maxInput.val(maxV);
        }

        // Wait until slider is ready and bind events
        if ($slider.length) {
            var tries = 0;
            var intv = setInterval(function() {
                if ($slider.hasClass('ui-slider')) {
                    // initialize labels
                    updateLabels();
                    // bind slide event
                    $slider.on('slide change', function(event, ui) {
                        updateLabels(ui);
                    });
                    clearInterval(intv);
                }
                tries++;
                if (tries > 30) clearInterval(intv);
            }, 100);
        }
    });
});