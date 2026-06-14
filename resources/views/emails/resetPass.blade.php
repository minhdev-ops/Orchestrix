<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Mật khẩu mới</title>
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
                        <p style="color:#666;line-height:1.6;">Mật khẩu của bạn đã được đặt lại thành công.</p>
                        <div style="background:#f8f8f8;border-radius:8px;padding:20px;margin:20px 0;text-align:center;">
                            <p style="color:#999;font-size:12px;margin:0 0 8px;">Mật khẩu mới của bạn:</p>
                            <p style="color:#333;font-size:20px;font-weight:bold;margin:0;font-family:monospace;letter-spacing:2px;">{{ $pass }}</p>
                        </div>
                        <p style="color:#666;line-height:1.6;">Vui lòng đăng nhập và đổi mật khẩu ngay để bảo mật tài khoản.</p>
                        <div style="text-align:center;margin:30px 0;">
                            <a href="{{ url('/login') }}"
                               style="display:inline-block;padding:14px 32px;background:#486730;color:#fff;text-decoration:none;border-radius:8px;font-weight:bold;">
                                Đăng nhập ngay
                            </a>
                        </div>
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
