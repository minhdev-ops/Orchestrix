<h1>Yêu cầu đặt lịch tư vấn mới</h1>
<p>Một khách hàng vừa đặt lịch tư vấn trên AgriVerse:</p>

<table style="border-collapse: collapse; width: 100%; max-width: 560px; font-family: Arial, sans-serif; font-size: 14px; color: #2c2c2c;">
    <tr>
        <td style="padding: 8px 12px; background: #f4f1ea;"><strong>Họ tên</strong></td>
        <td style="padding: 8px 12px;">{{ $booking['name'] }}</td>
    </tr>
    <tr>
        <td style="padding: 8px 12px; background: #f4f1ea;"><strong>Email</strong></td>
        <td style="padding: 8px 12px;">{{ $booking['email'] }}</td>
    </tr>
    @if (!empty($booking['phone']))
    <tr>
        <td style="padding: 8px 12px; background: #f4f1ea;"><strong>Số điện thoại</strong></td>
        <td style="padding: 8px 12px;">{{ $booking['phone'] }}</td>
    </tr>
    @endif
    @if (!empty($booking['topic']))
    <tr>
        <td style="padding: 8px 12px; background: #f4f1ea;"><strong>Chủ đề</strong></td>
        <td style="padding: 8px 12px;">{{ $booking['topic'] }}</td>
    </tr>
    @endif
    @if (!empty($booking['preferred_date']))
    <tr>
        <td style="padding: 8px 12px; background: #f4f1ea;"><strong>Ngày mong muốn</strong></td>
        <td style="padding: 8px 12px;">{{ \Carbon\Carbon::parse($booking['preferred_date'])->format('d/m/Y') }}</td>
    </tr>
    @endif
    @if (!empty($booking['preferred_time']))
    <tr>
        <td style="padding: 8px 12px; background: #f4f1ea;"><strong>Giờ mong muốn</strong></td>
        <td style="padding: 8px 12px;">{{ $booking['preferred_time'] }}</td>
    </tr>
    @endif
    @if (!empty($booking['message']))
    <tr>
        <td style="padding: 8px 12px; background: #f4f1ea; vertical-align: top;"><strong>Tin nhắn</strong></td>
        <td style="padding: 8px 12px;">{{ $booking['message'] }}</td>
    </tr>
    @endif
</table>

<p style="font-size: 13px; color: #74796c;">Mã tham chiếu: <strong>{{ $booking['reference'] ?? '—' }}</strong></p>
<p style="font-size: 13px; color: #74796c;">Vui lòng liên hệ khách hàng để xác nhận lịch tư vấn.</p>
