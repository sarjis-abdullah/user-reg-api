<!DOCTYPE html>
<html>
    @include('layouts.head')
    <body>
        <table class="email-wrapper" width="100%" cellpadding="0" cellspacing="0" role="presentation">
        <tbody>
        <tr>
            <td align="center">
                <table class="email-content" width="100%" cellpadding="0" cellspacing="0" role="presentation">
                    <tbody>
                    <!-- Logo and Title -->
                    @yield('logo')
                    @yield('title')

                    <!-- Email Body -->
                    @yield('content')

                    <!-- footer -->
                    @include('layouts.footer')

                    </tbody>
                </table>
            </td>
        </tr>
        </tbody>
    </table>
    </body>
</html>

