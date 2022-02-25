function isVisible(element) {
	const rect = element.getBoundingClientRect();
	return (rect.x + rect.width) < 0 || (rect.y - (document.body.classList.contains('admin-bar') ? 32 : 0) - 20 + rect.height) < 0 || (rect.x > window.innerWidth || rect.y > window.innerHeight);
}

if (document.body.classList.contains('woocommerce-shop')) {
	const observer = new IntersectionObserver(([ e ]) => e.target.toggleAttribute('stuck', e.intersectionRatio < 1), { threshold: [ 1 ] });
	observer.observe(document.querySelector('.menu-categories-container'));

	document.addEventListener('scroll', () => {
		const header = document.querySelector('header');
		header.querySelector('.menu-korzina-container').toggleAttribute('offscreen', isVisible(header));
	}, { passive: true });
}

const scrollToTop = document.querySelector('.scroll-to-top');

document.addEventListener('scroll', () => {
	const header = document.querySelector('header');
	scrollToTop.style.display = isVisible(header) ? 'block' : 'none';
}, { passive: true });

document.querySelector('.scroll-to-top').addEventListener('click', () => {
	document.body.scrollTop = 0;
	document.documentElement.scrollTop = 0;
}, { passive: true });

jQuery(($) => {
	// Обновляем корзину при изменении количества
	$(document).on('input', '#add_payment_method table.cart input, .woocommerce-cart table.cart input, .woocommerce-checkout table.cart input', () => {
		$('.woocommerce #content table.cart td.actions button[name="update_cart"], .woocommerce table.cart td.actions button[name="update_cart"], .woocommerce-page #content table.cart td.actions button[name="update_cart"], .woocommerce-page table.cart td.actions button[name="update_cart"]').click();
	});

	// Маска телефона
	$.mask.definitions[ 'h' ] = '[0-6-9]'; // Исключаем 7 и 8
	$('input[type="tel"]').mask('+7 (h99) 999-99-99');

	$('#coupon_code_fake ~ button').click(() => {
		$('#coupon_code').val($('#coupon_code_fake').val());
		$('#coupon_code').closest('form').submit();
		return false;
	});

	$(document.body).on('applied_coupon_in_checkout', () => {
		setTimeout(() => $.scroll_to_notices($('.woocommerce-error, .woocommerce-message')));
	})
});