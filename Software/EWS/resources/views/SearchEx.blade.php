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
            $(".section-controls").hide();
            $(".section-results").hide();

            var numIteration = 1;
            var low = -20;
            var high = 0;
            var direction = 0;
            var diff = 0;
            var alpha = 0;
            var New = 0;
            var Now = 0;

            $("#input_min").val(low);
            $("#input_max").val(high);

            reset = () => {
                $.ajax({
                    method: 'POST',
                    url: '/api/params',
                    data: JSON.stringify({
                    user_id: -1,
                    method: 'set',
                    request_action: 1,
                    data:{
                        left:{
                            audio_filename:'/audio/AB/MPEG_es01_s.wav',
                            audio_play:0,
                            alpha: 0
                        },
                        right:{
                            audio_filename:'/audio/AB/MPEG_es01_s.wav',
                            audio_play:0,
                            alpha: 0
                        }
                    }
                    }),
                    success: function(response){
                        console.log(response);
                    },
                    error: function(jqXHR, textStatus,errorThrown){
                        console.log("Parameters were not sent to the MHA.")
                    }
                });
            }

            reset();

            onStartPlaying = () => {
                var param;
                if (alpha == 1) {
                    param = JSON.stringify(
                    {
                        user_id: -1,
                        method: 'set',
                        request_action: 1,
                        data: {
                            left: {
                                audio_filename:'/audio/AB/MPEG_es01_s.wav',
                                audio_play:1,
                                audio_repeat:1,
                                alpha:1,
                                afc:0
                            },
                            right: {
                                audio_filename:'/audio/AB/MPEG_es01_s.wav',
                                audio_play:1,
                                audio_repeat:1,
                                alpha:1,
                                afc:0
                            }
                        }
                    });
                }
                else {
                    param = JSON.stringify(
                    {
                        user_id: -1,
                        method: 'set',
                        request_action: 1,
                        data:{
                            left:{
                                alpha:0
                            },
                            right:{
                                alpha:0,
                            }
                        }
                    });
                }
                
                var mid = (Number(low) + Number(high))*0.5;
                diff = Math.abs(high - low)*0.25;
                New = Number(mid) + Number(diff);
                Now = Number(mid) - Number(diff);

                $.ajax({
                    method: 'POST',
                    headers: {'X-CSRF-TOKEN': "{{ csrf_token() }}" },
                    url: '/api/params',
                    data: param,
                    success: function(response) {
                        console.log("Parameters were sent to the MHA");
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        alert("Parameters were not sent to the MHA");
                        console.log("Parameters were not sent to the MHA");
                    }     
                })
            }    

            $("input[type=radio]").on("click", () => {
                alpha = $("input:radio:checked").val();
            });

            $('#new').on("click", () => {
                    $('#new').addClass('bg-info');
                    $('#current').removeClass('bg-info');
                    var param = getParam(New);
                    transmitParams(param);
            });
                
            $('#current').on("click", () => {
                    $('#current').addClass('bg-info');
                    $('#new').removeClass('bg-info');
                    var param = getParam(Now)
                    transmitParams(param);
            });

            $("#start").on("click", () => {
                $(".section-intro").hide();
                $(".section-controls").show();
                onStartPlaying();
            });

            $("exit").on("click", () => {
                reset();
            })

            $("#input_min").on("input", () => {
                low = $("#input_min").val();
            })

            $("#input_max").on("input", () => {
                high = $("#input_max").val();
            })

            calculateNew = (choice) => {
                if (Math.abs(choice - high) < Math.abs(choice - low)) {
                    low = Number(low) + Number(diff);
                    diff = Math.abs(high - low)*0.25;
                    New = Number(high) - Number(diff);
                }
                else {
                    high = Number(high) - Number(diff);
                    diff = Math.abs(high - low)*0.25;
                    New = Number(low) + Number(diff);
                } 
            }

            $("#new_better").on("click", () => {
                Now = New;
                var param = getParam(Now);
                transmitParams(param);
                calculateNew(New);
                numIteration++;
                console.log("Current: " + Now)
                console.log("New: "+ New);
            });

            $("#new_worse").on("click", () => {
                calculateNew(Now);
                var param = getParam(Now);
                transmitParams(param);     
                numIteration++;
                console.log("Current: " + Now)
                console.log("New: " + New);
            })

            $("#equal").on("click", () => {
                $(".section-controls").hide();
                $("#curr_value").text("The overall gain is: " + Now);
                $("#value_diff").text("The difference between two stimulus: " + Math.abs(New - Now));
                $("#iteration").text("The number of iterations is: "+numIteration);
                $('.section-results').show();
                reset();
            });

            transmitParams = (param) => {
                $.ajax({
                    method: 'POST',
                    headers: {'X-CSRF-TOKEN': "{{ csrf_token() }}" },
                    url: '/api/params',
                    data: JSON.stringify({
                        user_id: -1,
                        method: 'set',
                        request_action: 1,
                        data: param
                    }),
                    success: function(response) {
                        console.log("Parameters were sent to the MHA");
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        alert("Parameters were not sent to the MHA");
                        console.log("Parameters were not sent to the MHA");
                    }     
                })
            }

            getParam = (value) => {
                return {
                    'left':{
                        'g50': [value, value, value, value, value, value],
                        'g80': [value, value, value, value, value, value]
                    },
                    'right':{
                        'g50': [value, value, value, value, value, value],
                        'g80': [value, value, value, value, value, value]
                    }
                } 
            }

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
            width: 60px;
            height: 60px;
            text-align: center;
            font-size: 40px;
        }

    </style>

</head>

<body>

    <section class="section-intro">
        <div id="setInputMode" class="container">
            <div style="text-align:center; margin-bottom:20px" >
                <h1 class="display-5">
                        Search Example
                </h1>
                <h3 class="text-secondary" id="description">
                        Choose between two set of changing parameters to find your optimal setting.
                </h3>
            </div>

            <div class="card" style="width: 18rem;">
                    <div class="card-body">
                        <h5 class="card-title">Input Controls</h5>
                            <div class="form-check" >
                                    <input class="form-check-input" type="radio" name="modeSelection" id="useBackground" value="0" checked>
                                <label class="form-check-label" for="modeSelection">
                                    Use Live Audio
                                </label>
                            </div>
                            <div class="form-check" style="margin-bottom:10px">
                                <input class="form-check-input" type="radio" name="modeSelection" id="useAudiofile" value="1">
                                <label class="form-check-label" for="modeSelection">
                                    Use File Stimulus
                                </label>
                            </div>
                            <h6 class="text-secondary">
                                Enter your search range:
                            </h6>
                            <div class="input-group input-group-sm mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" >Min</span>
                                </div>
                                <input type="number" id="input_min" class="form-control">
                            </div>
                            <div class="input-group input-group-sm mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" >Max</span>
                                </div>
                                <input type="number" id="input_max" class="form-control">
                            </div>
                    </div>
            </div>
          
            <div style="width:300px;margin-top:15px">
                <button class="btn btn-info btn-block" id="start">
                        Start
                </button>
                <a href="{{url('/')}}" id="exit" class="btn btn-block btn-outline-danger">
                    Exit
                </a>
            </div>
        </div>
    </section>

    <section class="section-controls">
        <div class="container"  style="text-align:center">
            <h3 class="text-secondary">
                Compare New and Current Setting and Choose Which One is Better
            </h3>
            <div class="row justify-content-around">
                <div class="col" >
                    <button type="button" id = "new" class="btn btn-default btn-play">
                        <span class="oi oi-play-circle oi-play"></span>
                    </button>
                    <h3>New</h3>
                </div>
                <div class="col">
                    <button type="button" id = "current" class="btn btn-default btn-play">
                        <span class="oi oi-play-circle oi-play"></span>
                    </button>
                    <h3>Current</h3>
                </div>
            </div>
            
            <div class="row">
                <div class="col-2">
                    <span style="font-size: 36px">
                        <i class="far fa-smile"></i>
                    </span>
                </div>
                <div class="col-10" style="justify-content: center;">
                    <span style="width: 80%">
                        <button 
                            type="button"
                            class="btn btn-outline-info btn-lg btn-block"
                            id = "new_better"
                        >
                            New is better than Current
                        </button>
                    </span>
                </div>
            </div>
            <div class="row">
                <div class="col-2">
                    <span style="font-size: 36px">
                        <i class="far fa-meh"></i>
                    </span>
                </div>
                <div class="col-10">
                    <button 
                        type="button" 
                        class="btn btn-outline-info btn-lg btn-block"
                        id = "equal"
                        >
                        New is the same as Current
                    </button>
                </div>
            </div>
            <div class="row">
                <div class="col-2">
                    <span style="font-size: 36px">
                        <i class="far fa-frown"></i>
                    </span>
                </div>
                <div class="col-10">
                    <button 
                        type="button" 
                        class="btn btn-outline-info btn-lg btn-block"
                        id = "new_worse"
                    >
                    New is worse than Current
                    </button>
                </div>
            </div>
        </div>
    </section>

    <section class="section-results">
        <div class="container"
             id = "status" >
          <h1  id = "result">Here is your result</h2>  
          <div class="container" style="margin-top:20px">
            <h4 class = "text-secondary" id="curr_value">
           
            </h4>

            <h4 class="text-secondary" id="value_diff">
        
            </h4>
            <h4 class="text-secondary" id="iteration">
        
            </h4>
          </div>
          <div style="max-width:150px;margin-top:30px;margin-left:auto;margin-right:auto">
            <a  href = "{{url('/searchex')}}" class="btn btn-info btn-block" id="exit">
                Start over
            </a>
            <a  href = "{{url('/')}}" class="btn btn-outline-danger btn-block" id="exit">
                  Exit
           </a>
          </div>
        </div>

</body>
</html>