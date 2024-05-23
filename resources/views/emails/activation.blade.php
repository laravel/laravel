<x-mail::message>
# GREETINGS FROM IIM

Hello {{ $user->name }},

Thank you for registering with us. Your account has been successfully created.

**User Email:** {{ $user->email }}
**Password:** {{ $user->password }}

To Login your account, please click the button below

@component('mail::button', ['url' => route('login')])
    Login
@endcomponent




Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
