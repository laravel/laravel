<div id="outdated-browser" class="absolute z-40 antialiased top-0 inset-x-0 p-6 bg-red text-white text-center text-base" aria-hidden="true" style="display: none;">
	You are using an <strong>outdated</strong> browser. Please <a href="http://outdatedbrowser.com/" class="text-inherit">upgrade your browser</a> to improve your viewing experience.
</div>

<script>
	(function() {
		var ua = window.navigator.userAgent;
		if (ua.indexOf('MSIE ') > 0 || !!ua.match(/Trident.*rv\:11\./)) {
			document.getElementById('outdated-browser').style.display = null;
		}
	})();
</script>
