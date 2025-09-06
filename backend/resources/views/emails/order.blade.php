<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to Our Platform!</title>
</head>

<body
    style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif, 'Apple Color Emoji', 'Segoe UI Emoji', 'Segoe UI Symbol'; background-color: #f4f4f7; margin: 0; padding: 0; -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%;">

    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color: #f4f4f7;">
        <tr>
            <td align="center" style="padding: 20px;">
                <table width="600" cellpadding="0" cellspacing="0" border="0"
                    style="background-color: #ffffff; border-radius: 8px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);">
                    <tr>
                        <td align="center" style="padding: 20px; border-bottom: 1px solid #e0e0e0;">
                            <a href="#" style="display: inline-block;">
                                <img src="{{ $message->embed(public_path('logo.png')) }}" alt="Company Logo"
                                    width="150" height="50" style="max-width: 150px; height: auto;">
                            </a>
                        </td>
                    </tr>
                    <tr>

                        <td style="padding: 20px; line-height: 1.6; color: #333333;">
                            <h2 style="color: #1a1a1a; font-size: 24px; margin-bottom: 10px;">Hi,
                                {{ $details['order']->fullname }}!</h2>
                            <p style="margin-bottom: 15px;">{!! $details['message'] !!}</p>


                            <p style="margin-bottom: 15px;">If you have any questions or need support, please don't
                                hesitate to <a href="mailto:support@yourcompany.com"
                                    style="color: #007bff; text-decoration: none;">contact us</a>. We're here to help.
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td align="center"
                            style="padding-top: 20px; margin-top: 20px; border-top: 1px solid #e0e0e0; font-size: 12px; color: #888888; text-align: center;">
                            <p style="margin: 0; line-height: 1.5;">Cheers,<br>The Team at Careeshyne</p>
                            <div style="margin-top: 10px;">
                                <a href="#" style="display: inline-block; margin: 0 5px;"><img
                                        src="{{ $message->embed(public_path('facebook.png')) }}" alt="Facebook"
                                        width="24" height="24"></a>
                                <a href="#" style="display: inline-block; margin: 0 5px;"><img
                                        src="{{ $message->embed(public_path('instagram.png')) }}" alt="Instagram"
                                        width="24" height="24"></a>
                                <a href="#" style="display: inline-block; margin: 0 5px;"><img
                                        src="{{ $message->embed(public_path('tiktok.png')) }}" alt="Tiktok"
                                        width="24" height="24"></a>
                            </div>
                            <p style="margin: 10px 0 5px 0;">&copy; {{ date('Y') }} Careeshyne. All
                                rights reserved.</p>
                            <p style="margin: 0;"><a href="#"
                                    style="color: #888888; text-decoration: underline;">Unsubscribe</a></p>
                        </td>
                    </tr>
                </table>

            </td>
        </tr>
    </table>
</body>

</html>
