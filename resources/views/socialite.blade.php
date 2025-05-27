<strong>Socialite</strong>

@auth
    <p>You are logged in.</p>
    <p>Welcome, {{ auth()->user()->name }}!</p>

    <form action="{{ route('socialite.logout') }}" method="POST">
        @csrf
        <button type="submit">Logout</button>
    </form>
@else
    <p>You are not logged in.</p>
    @if(empty($providers))
        <p>No socialite providers are configured yet.</p>
    @else
        <p>Login via:</p>
        <ul>
            @foreach ($providers as $provider)
                <li>
                    <a href="{{ route('socialite.redirect', ['provider' => $provider]) }}">{{ $provider }}</a>
                </li>
            @endforeach
        </ul>
    @endif
@endauth

@if ($errors->any())
    <p>Errors:</p>
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif