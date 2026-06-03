<h1>Chào {{ $name }},</h1>
<p>Bạn đã yêu cầu khôi phục mật khẩu. Vui lòng click vào link bên dưới để đặt lại mật khẩu:</p>
<a href="{{ route('reset.pass', ['email' => $email, 'key' => $hash]) }}">Đặt lại mật khẩu</a>
