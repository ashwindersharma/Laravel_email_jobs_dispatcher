<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $subject }}</title>
</head>

<body style="margin:0;padding:0;background:#f4f4f4;font-family:Arial,sans-serif;">

    <table width="100%" cellpadding="0" cellspacing="0" border="0"
        style="background:#f4f4f4;padding:40px 0;">
        <tr>
            <td align="center">

                <table width="600" cellpadding="0" cellspacing="0" border="0"
                    style="background:#ffffff;border-radius:8px;overflow:hidden;">

                    <!-- Header -->
                    <tr>
                        <td style="background:#111827;padding:30px;text-align:center;">
                            <h1 style="color:#ffffff;margin:0;font-size:24px;">
                                {{ config('app.name') }}
                            </h1>
                        </td>
                    </tr>

                    <!-- Content -->
                    <tr>
                        <td style="padding:40px;color:#333333;font-size:16px;line-height:1.7;">
                            {!! nl2br(e($body)) !!}
                            {{-- {!! $body !!} --}}

                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td
                            style="padding:20px;text-align:center;background:#f9fafb;
                            color:#6b7280;font-size:13px;">

                            <p style="margin:0;">
                                © {{ date('Y') }} {{ config('app.name') }}.
                                All rights reserved.
                            </p>

                        </td>
                    </tr>

                </table>

            </td>
        </tr>
    </table>

</body>

</html>
