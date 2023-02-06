@component('mail::message')
# {{ $subject }}

{{ $message }}


 

Thanks,<br>
{{ config('app.name') }}
@endcomponent
