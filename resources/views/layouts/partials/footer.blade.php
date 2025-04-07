<footer class="footer mt-auto py-3 bg-light">
    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <p class="mb-0 text-muted">
                    &copy; {{ date('Y') }} {{ config('app.name') }}. جميع الحقوق محفوظة.
                </p>
            </div>
            <div class="col-md-6 text-md-end">
                <p class="mb-0 text-muted">
                    الإصدار: {{ config('app_version.version') }}
                    @if(auth()->check() && auth()->user()->isAgency())
                        <a href="{{ route('agency.settings.index') }}" class="ms-2 text-muted small">
                            <i class="fas fa-info-circle"></i> معلومات النظام
                        </a>
                    @endif
                </p>
            </div>
        </div>
    </div>
</footer>
