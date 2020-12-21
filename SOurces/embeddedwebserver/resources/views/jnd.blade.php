<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/mobile.css') }}">
    <link rel="stylesheet" href="{{ asset('css/open-iconic-bootstrap.min.css')}}">
    <link rel="stylesheet" href="{{ asset('css/all.css') }}">
    <script type="text/javascript" src="{{ asset('js/jquery-3.3.1.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/popper.min.js')}}"></script>
    <script type="text/javascript" src="{{ asset('js/bootstrap.min.js')}}"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            $('#new').on("click", () => {
                    console.log("test");
                    $('#new').addClass('bg-info');
                    $('#current').removeClass('bg-info');
            });
                
            $('#current').on("click", () => {
                    $('#current').addClass('bg-info');
                    $('#new').removeClass('bg-info');
            });
        });
    </script>
    <style>
        .container {
            width: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding-top: 100px;
        }
        
        #description {
            margin-top: 20px;
            max-width: 600px;
        }
        
        #input_control {
            max-width: 300px;
        }

        .btn {
            margin-bottom: 20px;
        }

        .btn-play {
            font-size: 60px;
            padding: 15px;
        }
        
        .btn-play-wrapper {
            border-style: solid;
            border-width: 1px;
            width:180px; 
            height: 130px;
            display: flex;
            flex-direction: column;
            cursor: pointer;
            align-items: center;
        }

        .btn-choice-wrapper {
            display: flex;
            cursor: pointer;
            align-items: center;
            justify-content: center;
            width: 90%;
            max-width: 350px;
        }


    </style>

</head>

<body>
    <section class="section-controls">
        <div class="container"  style="text-align:center; margin-bottom: 20px">
            <h3 class="text-secondary" style="margin-bottom: 30px">
                Compare A and B
            </h3>
            <div class="row" style="margin-bottom: 50px; text-align:center;">
                <div type="button" id="new" class="col btn-play-wrapper" >
                    <span class="oi oi-media-play btn-play"></span>
                    <h3 >A</h3>
                </div>
                <div type="button" id="current" class="col btn-play-wrapper">
                    <span class="oi oi-media-play btn-play"></span>
                    <h3 >B</h3>
                </div>
            </div>
            <div style="width:90%; max-width:300px">
                <button class="btn btn-outline-info btn-lg btn-block">
                    Same
                </button>
                <button class="btn btn-outline-info btn-lg btn-block">
                    Different
                </button>
            </div>
        </div>
    </section>
</body>
</html>