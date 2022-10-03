@component('mail::message')
# {{ $subject }}

{{ $message }}

## {{ $otp }}

Thanks, <br>
{{ config('app.name') }}
@endcomponent
