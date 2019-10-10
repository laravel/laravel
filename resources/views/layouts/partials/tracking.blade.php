@if (App::environment(['production', 'staging']) && config('services.gtm.tracking_id'))
	@if ($body ?? false)
		<noscript><iframe src="https://www.googletagmanager.com/ns.html?id={{ config('services.gtm.tracking_id') }}" height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
	@else
		<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start': new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0], j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src= 'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f); })(window,document,'script','dataLayer','{{ config('services.gtm.tracking_id') }}');</script>
	@endif
@endif
