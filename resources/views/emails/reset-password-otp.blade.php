@extends('emails.layout')

@section('content')
    <div style="font-size: 16px; font-weight: 600; color: #09090b; margin-bottom: 12px;">
        Xin chào {{ $userName }},
    </div>

    <p style="margin: 0 0 16px; color: #3f3f46; font-size: 14.5px; line-height: 1.6;">
        Chúng tôi đã nhận được yêu cầu đặt lại mật khẩu cho tài khoản <strong>CloudBlog</strong> ({{ $userEmail }}) của bạn. Vui lòng sử dụng mã xác thực OTP 6 chữ số dưới đây để tiếp tục quá trình đặt lại mật khẩu:
    </p>

    <!-- Centered Highlighted OTP Box -->
    <div style="margin: 28px 0; text-align: center;">
        <div style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.1em; color: #71717a; margin-bottom: 10px; font-weight: 600;">
            MÃ OTP ĐẶT LẠI MẬT KHẨU
        </div>
        <div class="otp-code" style="display: inline-block; background-color: #f8fafc; border: 1.5px dashed #cbd5e1; border-radius: 12px; padding: 16px 32px; font-family: 'SF Mono', Consolas, Monaco, 'Courier New', monospace; font-size: 32px; font-weight: 700; letter-spacing: 8px; color: #09090b; user-select: all;">
            {{ $otp }}
        </div>
        <div style="font-size: 12.5px; color: #64748b; margin-top: 10px;">
            Mã có hiệu lực trong vòng <strong>5 phút</strong> (tối đa 5 lần nhập).
        </div>
    </div>

    <!-- Security Warning Card -->
    <div style="background-color: #fef2f2; border-left: 3px solid #ef4444; padding: 12px 16px; border-radius: 0 8px 8px 0; font-size: 12.5px; color: #991b1b; line-height: 1.5; margin: 24px 0 16px;">
        <strong>Lưu ý bảo mật:</strong> Tuyệt đối không chia sẻ mã này cho bất kỳ ai. Đội ngũ CloudBlog không bao giờ yêu cầu bạn cung cấp mã OTP.
    </div>

    <p style="margin: 0; color: #71717a; font-size: 13px; line-height: 1.5;">
        Nếu bạn không yêu cầu đặt lại mật khẩu, vui lòng bỏ qua email này. Tài khoản của bạn vẫn được bảo vệ an toàn và mật khẩu cũ không bị thay đổi.
    </p>
@endsection

