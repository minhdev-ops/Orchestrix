<h1>Chào {{ $name }},</h1>
<p>Vui lòng click vào link bên dưới để kích hoạt tài khoản của bạn:</p>
<a href="{{ route('active.mail', ['email' => $email, 'key' => $hash]) }}">Kích hoạt tài khoản</a>
