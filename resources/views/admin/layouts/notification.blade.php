<!-- Toast Notification -->
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>

<script type="text/javascript">
	// Default Configuration

        toastr.options = {
            'closeButton': true,
            'debug': false,
            'newestOnTop': false,
            'progressBar': true,
            'positionClass': 'toast-top-center',
            'preventDuplicates': false,
            'showDuration': '1000',
            'hideDuration': '1000',
            'timeOut': '2000',
            'extendedTimeOut': '10000',
            'tapToDismiss': false,
            'showEasing': 'swing',
            'hideEasing': 'linear',
            'showMethod': 'fadeIn',
            'hideMethod': 'fadeOut',
        }
</script>


    <style>

        .toast-close-button{
            color: rgb(0, 0, 0);
        }
        #toast-container>div {
            opacity: 1;
        }

        #toast-container > .toast:before {
            position: relative;
            font-family: 'Font Awesome 5 Free';
            font-size: 24px;
            line-height: 18px;
            float: left;
            margin-left: -1em;
            color: #333;
            padding-right: 0.5em;
            margin-right: 0em;
        }

        .toast-message {
            color: rgb(0, 0, 0);
            font-weight: bold;
        }


        .toast-success {
            border: 2px solid #61c9a8;
            background-color: #fff;
        }

        .toast-success .toast-progress {
            height: 10vh;
            background-color: rgba(97, 201, 168, 0.493);
        }


        .toast-error {
            border: 2px solid #d64550;
            background-color: #fff;
        }

        .toast-error .toast-progress {
            height: 10vh;
            background-color: rgba(214, 69, 81, 0.445);
        }
        .toast-progress:after{
            content: "";
            border-left: 24px solid rgba(214, 69, 81, 0);
            border-top: 0px solid #fff;
            border-bottom: 10vh solid #fff;
            position: absolute;
            right:0px;
        }



        #toast-container > .toast-warning:before {
            content: "\f003";
        }
        #toast-container > .toast-error:before {
            content: "\f057";
            color: #d64550;
        }
        #toast-container > .toast-info:before {
            content: "\f005";
        }
        #toast-container > .toast-success:before {
            content: "\f058";
            color: #61c9a8;
        }
    </style>

    @if(Session::get('status') == 1 && Session::get('message'))
        <script>
            toastr.success('{{ Session::get('message') }}');
        </script>
    @endif

    @if(Session::get('status') == 0 && Session::get('message'))
        <script>
            toastr.error('{{ Session::get('message') }}');
        </script>
    @endif

@if ($errors->any())
    @foreach ($errors->all() as $error)
        <script>
            toastr.error('{{ $error }}');
        </script>
    @endforeach
@endif



