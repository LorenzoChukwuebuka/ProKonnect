@component('mail::message')
    # {{ $subject }}

    {{ $msg }}

    ## {{ $otp }}

    Thanks,<br>
    {{ config('app.name') }}
@endcomponent
