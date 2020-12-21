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


    <title>Researcher Login</title>
</head>

<body>

    <div class="container">
        <div class="row">
            <div class="col">
                <p class="text-center h10">GOLDILOCKS</p>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <br>

                <form class="form-horizontal" method="POST">
                    {{ Form::token()  }}
                    <div class="form-inline">
                        <label for="researcher" class="h7">Researcher</label>
                        <div class="col-sm-8">
                            @if(!$curResearcher)
                                <input type="text" class="text_1" id="researcher" name="researcher" placeholder="Researcher">
                            @else
                                <input type="text" class="text_1" id="researcher" name="researcher" value="{{ $curResearcher->researcher }}">
                            @endif
                         </div>
                    </div>

                    <br>

                    @if($errors->any())
                        <p style="color: red">{{ $errors->first() }}</p>
                    @endif

                    <p class="text-center h8">Listeners</p>
                    @foreach($listeners as $listener)
                        <br>
                        <input type="submit" class="btn1 btn-info btn-block1" name="listener" value="{{ $listener->listener }}">
                    @endforeach

                </form>
                @if(count($listeners) < 1)
                    <br>
                    <a href="{{ url('/goldilocks/admin/listeners') }}" class="btn1 btn-secondary btn-block1">Click Here To Create Listener</a>
                @endif
            </div>

            <a class="btn btn-info btn-sm" style = "margin-top:20px;margin-left:20px;"
            href="{{url('/goldilocks')}}" role="button" >Home</a>
        </div>

    </div>
</body>

</html>

