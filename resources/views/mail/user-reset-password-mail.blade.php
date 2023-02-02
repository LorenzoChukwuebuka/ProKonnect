@component('mail::message')
# {{ $subject }}

{{ $message }}



@component('mail::button', ['url' => $url])
 Reset Password 
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
