@component('mail::message')
# Security Alert: IP Address Blocked

An IP address has been blocked due to multiple failed authentication attempts.

**IP Address:** {{ $ipAddress }}  
**Action:** {{ ucfirst(str_replace('_', ' ', $action)) }}  
**Failed Attempts:** {{ $attempts }}

This IP address has been temporarily blocked to protect the system from potential security threats.

@component('mail::button', ['url' => config('app.url')])
View Dashboard
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
