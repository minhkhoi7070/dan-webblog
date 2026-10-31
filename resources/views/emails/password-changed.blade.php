@extends('emails.layout')

@section('content')
    <div style="font-size: 16px; font-weight: 600; color: #09090b; margin-bottom: 12px;">
        Xin chào {{ $userName }},
    </div>

    <p style="margin: 0 0 16px; color: #3f3f46; font-size: 14.5px; line-height: 1.6;">
        Mật khẩu cho tài khoản <strong>CloudBlog</strong> ({{ $userEmail }}) của bạn vừa được thay đổi thành công.
    </p>

    <!-- Security Event Information Card -->
    <div style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 18px 20px; margin: 20px 0;">
        <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
            <tr>
                <td style="font-size: 13px; color: #64748b; padding-bottom: 8px; width: 120px;">
                    Sự kiện:
                </td>
                <td style="font-size: 13.5px; font-weight: 600; color: #0f172a; padding-bottom: 8px;">
                    Đổi mật khẩu tài khoản
                </td>
            </tr>
            <tr>
                <td style="font-size: 13px; color: #64748b; padding-bottom: 8px;">
                    Tài khoản:
                </td>
                <td style="font-size: 13.5px; font-weight: 500; color: #0f172a; padding-bottom: 8px;">
                    {{ $userEmail }}
                </td>
            </tr>
            <tr>
                <td style="font-size: 13px; color: #64748b;">
                    Thời gian:
                </td>
                <td style="font-size: 13.5px; font-weight: 500; color: #0f172a;">
                    {{ $changedAt }} (GMT+7)
                </td>
            </tr>
        </table>
    </div>

    <p style="margin: 0 0 16px; color: #3f3f46; font-size: 14px; line-height: 1.6;">
        Nếu chính bạn đã thực hiện thao tác này, bạn có thể hoàn toàn an tâm bỏ qua thông báo này.
    </p>

    <!-- Urgent Warning if not authorized -->
    <div style="background-color: #fef2f2; border-left: 3px solid #ef4444; padding: 12px 16px; border-radius: 0 8px 8px 0; font-size: 12.5px; color: #991b1b; line-height: 1.5; margin: 20px 0 8px;">
        <strong>Cảnh báo an toàn:</strong> Nếu bạn <strong>KHÔNG</strong> thực hiện thay đổi này, tài khoản của bạn có thể đã bị truy cập trái phép. Vui lòng sử dụng chức năng khôi phục mật khẩu hoặc liên hệ với ban quản trị ngay lập tức để bảo vệ tài khoản của mình.
    </div>
@endsection
