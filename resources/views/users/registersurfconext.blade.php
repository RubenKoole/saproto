@extends('website.layouts.panel')

@section('page-title')
    New Account Registration
@endsection

@section('panel-title')
    Registering a new account
@endsection

@section('panel-body')

    <form method="POST" action="{{ route('login::register::surfconext') }}">

        <p>
            You tried to login using a university account we don't recognize. If you wish to create an account on the
            Proto website, you can use this here.
        </p>

        <p style="font-weight: bold;">
            Creating and having an account on the website does not make you a member of S.A. Proto and is free of
            charge.
        </p>

        <hr>

        {!! csrf_field() !!}

        <p>
            Create an account using the following details:
        </p>

        <p>
            Name: <strong>{{ $remote_data['givenname'] }} {{ $remote_data['surname'] }}</strong>
        </p>
        <p>
            E-mail address: <strong>{{ $remote_data['mail'] }}</strong><br>
            <i>You can change this later!</i>
        </p>
        <p>
            University account: <strong>{{ $remote_data['uid'] }}</strong> at <strong>{{ $remote_data['org'] }}</strong><br>
            <i>You can unlink your account later!</i>
        </p>

        <hr>

        <p>
            Although you can log-in to your account using your university account, you will also receive an e-mail
            that'll allow you to set a password. You should do this, because you can also use that password in
            combination with your e-mail address to log-in, and you can't use your university account everywhere where
            you log-in to your account.
        </p>

        <hr>

        <div class="checkbox">
            <label>
                <input type="checkbox" name="privacy_policy_acceptance" required>
                I have read and agree with the <a href="https://wiki.proto.utwente.nl/ict/privacy/start?do=export_pdf"
                                                  target="_blank">privacy policy</a> of S.A. Proto.
            </label>
        </div>

        @endsection

        @section('panel-footer')
            <button type="submit" class="btn btn-success pull-right">Create my account</button>

    </form>
@endsection