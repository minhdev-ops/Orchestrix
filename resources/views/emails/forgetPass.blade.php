<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Khôi phục mật khẩu</title>
</head>
<body style="margin:0;padding:0;background:#f4f4f4;font-family:Arial,sans-serif;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background:#f4f4f4;padding:40px 0;">
        <tr><td align="center">
            <table width="600" cellpadding="0" cellspacing="0" style="background:#fff;border-radius:12px;overflow:hidden;">
                <tr>
                    <td style="background:#486730;padding:30px;text-align:center;">
                        <h1 style="color:#fff;margin:0;font-size:24px;">🌿 AgriVerse</h1>
                    </td>
                </tr>
                <tr>
                    <td style="padding:40px;">
                        <h2 style="color:#333;margin:0 0 16px;">Xin chào {{ $name }}!</h2>
                        <p style="color:#666;line-height:1.6;">Bạn đã yêu cầu khôi phục mật khẩu. Vui lòng nhấn nút bên dưới để đặt lại mật khẩu:</p>
                        <div style="text-align:center;margin:30px 0;">
                            <a href="{{ route('reset.pass', ['email' => $email, 'key' => $hash]) }}"
                               style="display:inline-block;padding:14px 32px;background:#486730;color:#fff;text-decoration:none;border-radius:8px;font-weight:bold;">
                                Đặt lại mật khẩu
                            </a>
                        </div>
                        <p style="color:#999;font-size:12px;line-height:1.6;">Nếu bạn không yêu cầu đặt lại mật khẩu, vui lòng bỏ qua email này. Link sẽ hết hạn sau 24 giờ.</p>
                    </td>
                </tr>
                <tr>
                    <td style="background:#f8f8f8;padding:20px;text-align:center;">
                        <p style="color:#999;font-size:11px;margin:0;">© 2026 AgriVerse - Cộng đồng cây cảnh Việt Nam</p>
                    </td>
                </tr>
            </table>
        </td></tr>
    </table>
</body>
</html>
