<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="color-scheme" content="light">
    <meta name="supported-color-schemes" content="light">
</head>
<body>
    <h3>Hey there! {{ $data['inviter'] }} invited you to join the {{ $data['project']['title'] }} project:</h3>
    <p>
        {{ $data['inviter'] }}: "I'm working on this project and wanted to share it with you! <br>
        {{ $data['message'] }}".
    </p>
    <p>
        <a href="{{ route('members.joinByMail', ['member' => $data['member']['invite_token']]) }}">Join Project</a>
    </p>
    <p>
        <small>© {{ date('Y') }} Powered by <a class="footer-link" href="{{ config('app.url') }}">{{ config('app.name') }}</a>. @lang('All rights reserved.').</small>
    </p>

</body>
</html>
