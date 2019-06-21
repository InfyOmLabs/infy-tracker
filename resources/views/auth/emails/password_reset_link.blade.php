<html>
<head>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
    <!-- Styles -->
    <style>
        .verification-btn {
            font-size: 13px;
            text-align: center;
            white-space: nowrap;
            cursor: pointer;
            padding: 10px;
            background-color: #66CC33;
            color: #FFFFFF;
            text-decoration: none;
        }

        .account-verification {
            width: 512px;
            padding: 10px;
            margin: auto;
        }

        .account-verification__table {
            width: 100%;
        }

        .text-center {
            text-align: center;
        }

        .divider {
            color: #e0e0e0;
            border: none;
            background-color: #e0e0e0;
            height: 3px;
            margin-top: 20px;
        }

        td {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
        }

        .logo {
            width: 80px;
            height: 35px;
            margin-top: 10px
        }

        td p {
            margin: 17px;
            font-size: 13px;
            color: #6D6C6C
        }
        .regards-mb-4 {
            margin-bottom: 4px;
        }
        .regards-mt-4 {
            margin-top: 4px;
        }
        .bottom-text{
            font-size: 11px;
        }
    </style>
</head>
<body>

<div class="account-verification">
    <table class="account-verification__table">
        <tr>
            <td class="text-center">
                <img class="logo" src="{{asset('assets/img/logo-red-black.png')}}" alt="InfyOm Logo">
            </td>
        </tr>
        <tr>
            <td>
                <hr class="divider"/>
            </td>
        </tr>
        <tr>
            <td>
                <p>Dear {{ucfirst($username)}},</p>
                <p>You are receiving this email because we received a password reset request for your account.</p>
            </td>
        </tr>
        <tr>
            <td class="text-center">
                <p>
                    <a href="{{$link}}" class="verification-btn">
                        <strong>Reset Password</strong>
                    </a>
                </p>
            </td>
        </tr>
        <tr>
            <td>
                <p>This password reset link will expire in 60 minutes.</p>
                <p>If you did not request a password reset, no further action is required.</p>
            </td>
        </tr>
        <tr>
            <td>
                <p class="regards-mb-4">Regards,</p>
                <p class="regards-mt-4">InfyTracker</p>
            </td>
        </tr>
        <tr>
            <td>
                <p class="bottom-text">
                    If you’re having trouble clicking the "Reset Password" button, copy and paste the URL below into your web browser: <a href="{{$link}}">{{$link}}</a>
                </p>
            </td>
        </tr>
        <tr>
            <td>
                <hr class="divider"/>
            </td>
        </tr>
    </table>
</div>
</body>
</html>