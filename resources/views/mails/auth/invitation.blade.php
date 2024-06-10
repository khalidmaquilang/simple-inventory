<x-mail::message>
    Hello,

    {{ __('You have been invited to join ') }}{{ $company_name }}

    {{ __('To accept the invitation, click on the button below and create an account.') }}

    <x-mail::button :url='$acceptUrl'>
        {{ __('Join Company') }}
    </x-mail::button>

    {{ __('If you did not expect to receive an invitation to this company, you may disregard this email.') }}

    Thanks,
    {{ config('app.name') }}
</x-mail::message>
