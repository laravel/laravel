<div class="js-cookie-consent cookie-consent">

    <span class="cookie-consent__message">
        {!! trans('cookieConsent::texts.message') !!}
    </span>

    <div class="cookie-consent__buttons">
        <button class="js-cookie-consent-agree cookie-consent__agree btn btn-primary">
            {{ trans('cookieConsent::texts.agree') }}
        </button>
        <a href="{{ route('cookies') }}" class="cookie-consent__more-info btn btn-outline-primary ml-2">
            Más información
        </a>
    </div>

</div>
