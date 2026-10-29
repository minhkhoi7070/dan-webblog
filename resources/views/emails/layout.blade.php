<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="vi">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="format-detection" content="telephone=no" />
    <meta name="color-scheme" content="light dark" />
    <meta name="supported-color-schemes" content="light dark" />
    <title>{{ $emailTitle ?? config('app.name', 'CloudBlog') }}</title>
    <style type="text/css">
        /* Client-specific Resets */
        body, table, td, a { -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; }
        table, td { mso-table-lspace: 0pt; mso-table-rspace: 0pt; }
        img { -ms-interpolation-mode: bicubic; border: 0; outline: none; text-decoration: none; }
        body { margin: 0; padding: 0; width: 100% !important; min-width: 100%; background-color: #f4f4f5; }

        /* Responsive Styles */
        @media only screen and (max-width: 600px) {
            .email-container {
                width: 100% !important;
                max-width: 100% !important;
                border-radius: 0 !important;
                border-left: none !important;
                border-right: none !important;
            }
            .email-body {
                padding: 24px 20px !important;
            }
            .email-header {
                padding: 24px 20px !important;
            }
            .otp-code {
                font-size: 28px !important;
                letter-spacing: 6px !important;
                padding: 14px 20px !important;
            }
        }
    </style>
</head>
<body style="margin: 0; padding: 0; background-color: #f4f4f5; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif; -webkit-font-smoothing: antialiased; color: #09090b;">
    <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #f4f4f5; padding: 32px 12px;">
        <tr>
            <td align="center" style="padding: 0;">
                <!-- Main Card Container -->
                <table role="presentation" class="email-container" border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 560px; background-color: #ffffff; border: 1px solid #e4e4e7; border-radius: 16px; overflow: hidden; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);">
                    <!-- Brand Header -->
                    <tr>
                        <td align="center" class="email-header" style="padding: 32px 32px 20px; border-bottom: 1px solid #f4f4f5; background-color: #ffffff;">
                            <table role="presentation" border="0" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td align="center" style="vertical-align: middle;">
                                        <!-- CloudBlog Brand Logo Icon -->
                                        <div style="display: inline-block; width: 36px; height: 36px; background-color: #09090b; border-radius: 10px; line-height: 36px; text-align: center; color: #ffffff; font-weight: 800; font-size: 19px; vertical-align: middle; margin-right: 10px;">
                                            C
                                        </div>
                                        <span style="font-size: 20px; font-weight: 700; color: #09090b; letter-spacing: -0.02em; vertical-align: middle;">
                                            CloudBlog
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Email Content Body -->
                    <tr>
                        <td class="email-body" style="padding: 32px; font-size: 14.5px; line-height: 1.65; color: #27272a;">
                            @yield('content')
                        </td>
                    </tr>

                    <!-- Email Footer -->
                    <tr>
                        <td style="padding: 20px 32px 28px; background-color: #fafafa; border-top: 1px solid #f4f4f5; text-align: center; font-size: 12px; color: #71717a; line-height: 1.6;">
                            <div style="font-weight: 600; color: #52525b; margin-bottom: 4px;">
                                Trân trọng,<br>Đội ngũ CloudBlog
                            </div>
                            <div style="font-size: 11px; color: #a1a1aa; margin-top: 12px;">
                                Email này được gửi tự động từ hệ thống CloudBlog. Vui lòng không trả lời trực tiếp email này.
                            </div>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>

