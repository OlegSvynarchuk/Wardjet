(function () {
	const params = new URLSearchParams(window.location.search);
	const utm_params = [];
	const carry_keys = ['gclid', 'fbclid', '_gl'];
	params.forEach(function (value, key) {
	   if (key.startsWith('utm_') || carry_keys.indexOf(key) !== -1) {
		  utm_params.push(key + '=' + value)
	   }
	});

	const utm_search = utm_params.join('&');
	if (utm_search.length === 0) return;

	function shouldSkip(href) {
		return !href
			|| href.indexOf('#') !== -1
			|| href.indexOf('mailto:') === 0
			|| href.indexOf('tel:') === 0
			|| href.indexOf('javascript:') === 0;
	}

	function alreadyHasAll(href) {
		// Quick check: skip if every tracked key=value is already present
		for (let i = 0; i < utm_params.length; i++) {
			if (href.indexOf(utm_params[i]) === -1) return false;
		}
		return true;
	}

	function appendTo(item) {
		const href = item.getAttribute('href');
		if (shouldSkip(href)) return;
		if (alreadyHasAll(href)) return;
		const sep = href.indexOf('?') === -1 ? '?' : '&';
		item.setAttribute('href', href + sep + utm_search);
	}

	function applyUTMS() {
		Array.from(document.querySelectorAll('a[href]')).forEach(appendTo);
	}

	// 1) Apply as soon as DOM is parsed — don't wait for slow third-party
	//    iframes (Zapier, JotForm, etc.) which can delay window.load by 3-4s.
	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', applyUTMS);
	} else {
		applyUTMS();
	}

	// 2) Re-apply once everything (including iframes) finishes, in case
	//    components like Max Mega Menu re-render menu items late.
	window.addEventListener('load', applyUTMS);

	// 3) Capture-phase click guard — last line of defense. If any link
	//    still lacks UTMs at click time (e.g. dynamically inserted or
	//    re-rendered by a plugin), rewrite its href before the browser
	//    navigates. Fires before bubble-phase handlers (Max Mega Menu).
	document.addEventListener('click', function (e) {
		const link = e.target && e.target.closest ? e.target.closest('a[href]') : null;
		if (!link) return;
		appendTo(link);
	}, true);
})();
