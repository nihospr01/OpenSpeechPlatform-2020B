<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
    <script type="text/javascript" src="{{ asset('js/jquery-3.3.1.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/popper.min.js')}}"></script>
    <script type="text/javascript" src="{{ asset('js/bootstrap.min.js')}}"></script>


    <style>
        .styled{
        margin-left: 20%;
    }



  
    </style>

    <title>Listener Login</title>

</head>

<body>


<div class="container">
        <div class="row">
            <div class="col">
                <p class="text-center h10">GOLDILOCKS</p>
            </div>
        </div>

        <div class="row">
            <dive class="col-12">
                <p></p>
                <form class="form-horizontal" method="POST">
                    {{ Form::token()  }}


                    @if($errors->any())
                      <p style="color: red">{{ $errors->first() }}</p>
                    @endif

                    @foreach($listeners as $listener)
                        <br>
                        <input type="submit" class="btn1 btn-info btn-block1" name="listener" value="{{ $listener->listener }}">
                    @endforeach

                </form>
                @if(count($listeners) < 1)
                    <br>
                    <h3>No listeners to choose from.</h3>
                @endif

            </div>
        </div>
    </div>





</body>

</html>
