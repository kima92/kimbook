@php
    /** @var \App\Models\Book $book */
@endphp
<div dir="rtl">
    <table cellpadding="0" cellspacing="0" border="0" width="100%" bgcolor="#FFFFFF">
<tbody><tr>
<td valign="top" bgcolor="#FFFFFF" width="100%">
    <table width="100%" role="content-container" align="center" cellpadding="0" cellspacing="0" border="0">
        <tbody><tr>
            <td width="100%">
                <table width="100%" cellpadding="0" cellspacing="0" border="0">
                    <tbody><tr>
                        <td>

<table width="100%" cellpadding="0" cellspacing="0" border="0" style="width:100%;max-width:600px" align="center">
    <tbody><tr>
        <td role="modules-container" style="padding:0 0 0 0;color:#000000;text-align:left" bgcolor="#FAFAF7" width="100%" align="left"><table class="preheader" role="module" border="0" cellpadding="0" cellspacing="0" width="100%" style="display:none!important;opacity:0;color:transparent;height:0;width:0">
                <tbody><tr>
                    <td role="module-content">
                        <p></p>
                    </td>
                </tr>
                </tbody></table>
            <table class="wrapper" role="module" border="0" cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed">
                <tbody>
                <tr>
                    <td style="line-height:10px;padding:30px 0 10px 0" valign="top" align="center" bgcolor="#f0efea"><br><br>
                        <a href="{{ url("") }}" target="_blank" style="text-decoration: none; color: black; font-weight: bolder"><h1 style="font-size:36px">{{ config("app.name", "סיפורון") }}</h1></a>
                    </td>
                </tr>
                </tbody>
            </table>
            <table role="module" border="0" cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed">
                <tbody>
                <tr>
                    <td style="padding:40px 0px 0px 0px;line-height:50px;text-align:inherit" height="100%" valign="top" bgcolor="" role="module-content"><div>
                            <h1 style="text-align:center"><span style="font-size:48px;font-family:helvetica,sans-serif">{{ $book->title }}</span></h1>
                            <h1 style="text-align:center"><span style="font-size:48px;font-family:helvetica,sans-serif">הסיפור נוצר בהצלחה!</span></h1>
                    <div></div></div></td>
                </tr>
                </tbody>
            </table><table border="0" cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed">
                <tbody>
                <tr>
                    <td style="font-size:6px;line-height:10px;padding:40px 0px 40px 0px" valign="top" align="center">
                        <a href="{{ url("/books/{$book->uuid}") }}" target="_blank">
                            <img border="0" style="display:block;color:#000000;text-decoration:none;font-family:Helvetica,arial,sans-serif;font-size:16px;max-width:100%!important;width:100%;height:auto!important" width="600" alt="" src="{{ url($book->chapters()->first()->images()->first()->image_url) }}">
                        </a>
                    </td>
                </tr>
                </tbody>
            </table>
            <table border="0" cellpadding="0" cellspacing="0" role="module" style="table-layout:fixed" width="100%">
                <tbody>
                <tr>
                    <td align="center" bgcolor="" style="padding:0px 0px 0px 0px">
                        <table border="0" cellpadding="0" cellspacing="0" style="text-align:center">
                            <tbody>
                            <tr>
                                <td align="center" bgcolor="#333333" style="border-radius:6px;font-size:16px;text-align:center;background-color:inherit">
                                    <a href="{{ url("/books/{$book->uuid}") }}" style="background-color:#333333;border:1px solid #333333;border-color:#333333;border-radius:15px;border-width:1px;color:#ffffff;display:inline-block;font-size:14px;font-weight:normal;letter-spacing:0px;line-height:normal;padding:12px 18px 12px 18px;text-align:center;text-decoration:none;border-style:solid" target="_blank">לקריאת הספר</a>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
                </tbody>
            </table>
            <table role="module" border="0" cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed">
                <tbody>
                <tr>
                    <td style="padding:18px 40px 40px 40px;line-height:23px;text-align:inherit" height="100%" valign="top" bgcolor="" role="module-content">
                        <div><div style="font-family:inherit;text-align:right">
                                <span style="font-family:helvetica,sans-serif;font-size:18px">
                                    שלום, {{ $book->user->name }}<br><br>
                        עבודת היצירה הסתיימה - הספר החדש שלכם הושלם!<br><br>
                        {{ $book->description }}<br><br>
                        שלכם,<br>
                        אתר סיפורון
                                </span></div><div></div></div></td>
                </tr>
                </tbody>
            </table>
            <table role="module" align="center" border="0" cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed">
                <tbody>
                <tr>
                    <td valign="top" style="padding:0px 0px 20px 0px;font-size:6px;line-height:10px" align="center">
                        <table align="center">
                            <tbody>
                            <tr align="center">
                                <td style="padding:0px 5px">
                                    <a role="social-icon-link" href="{{ url('') }}" alt="Twitter" title="Twitter" style="display:inline-block;background-color:#7ac4f7;height:30px;width:30px" target="_blank">
                                        <img role="social-icon" alt="Twitter" title="Twitter" src="https://mc.sendgrid.com/assets/social/white/twitter.png" style="height:30px;width:30px" height="30" width="30" class="CToWUd" data-bit="iit">
                                    </a>
                                </td>
                                <td style="padding:0px 5px">
                                    <a role="social-icon-link" href="{{ url('') }}" alt="LinkedIn" title="LinkedIn" style="display:inline-block;background-color:#0077b5;height:30px;width:30px" target="_blank">
                                        <img role="social-icon" alt="LinkedIn" title="LinkedIn" src="https://mc.sendgrid.com/assets/social/white/linkedin.png" style="height:30px;width:30px" height="30" width="30" class="CToWUd" data-bit="iit">
                                    </a>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
                </tbody>
            </table>
        </td>
    </tr>
    </tbody></table>

                                    </td>
                                </tr>
                                </tbody></table>
                        </td>
                    </tr>
                    </tbody></table>
            </td>
        </tr>
        </tbody></table>
</div>
