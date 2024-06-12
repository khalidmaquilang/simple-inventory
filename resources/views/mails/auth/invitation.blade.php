<x-mail::message>
Hello,

You have been invited to join {{ $company_name }}

To accept the invitation, click on the button below and create an account.

<x-mail::button :url="$acceptUrl">
    Join Company
</x-mail::button>

If you did not expect to receive an invitation to this company, you may disregard this email.

Thanks,
{{ config('app.name') }}
</x-mail::message>
