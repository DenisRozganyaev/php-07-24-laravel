@extends('layouts.admin')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col">
                <h3>Admin Dashboard</h3>
                @hasrole('admin')
                    @unless(auth()->user()->telegram_id)
                        <h4>Telegram auth</h4>
                        <script async
                                src="https://telegram.org/js/telegram-widget.js?22"
                                data-telegram-login="{{config('services.telegram-bot-api.bot')}}"
                                data-size="large"
                                data-auth-url="{{ route('callback.telegram') }}"
                                data-request-access="write"
                        ></script>
                    @endunless
                @endhasrole
            </div>
        </div>
    </div>
@endsection
