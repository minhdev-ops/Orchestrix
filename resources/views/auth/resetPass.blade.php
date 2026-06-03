<!DOCTYPE html>
<html>
<head>
    <title>Đặt lại mật khẩu</title>
</head>
<body>
    @if($ok == 1)
        <h1>Đặt lại mật khẩu thành công!</h1>
        <p>Mật khẩu mới đã được gửi vào email của bạn. Vui lòng kiểm tra email để lấy mật khẩu mới.</p>
    @else
        <h1>Đặt lại mật khẩu thất bại!</h1>
        <p>Yêu cầu không hợp lệ hoặc đã hết hạn.</p>
    @endif
</body>
</html>
