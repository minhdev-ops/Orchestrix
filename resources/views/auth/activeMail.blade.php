<!DOCTYPE html>
<html>
<head>
    <title>Kích hoạt tài khoản</title>
</head>
<body>
    @if($ok == 1)
        <h1>Kích hoạt thành công!</h1>
        <p>Tài khoản của bạn đã được kích hoạt. Bây giờ bạn có thể đăng nhập.</p>
    @else
        <h1>Kích hoạt thất bại!</h1>
        <p>Mã kích hoạt không đúng hoặc đã hết hạn.</p>
    @endif
</body>
</html>
