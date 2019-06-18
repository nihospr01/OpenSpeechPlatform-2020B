{{-- <!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Laravel React application</title>
        <link href="{{mix('css/4afc/app.css')}}" rel="stylesheet" type="text/css">
    </head>
    <body>
    <h2 style="text-align: center"> Laravel and React application </h2>
        <div id="container"></div>
        <script src="{{mix('js/4afc/app.js')}}" ></script>
    </body>
</html> --}}

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/mobile.css') }}">
    <link rel="stylesheet" href="{{ asset('css/open-iconic-bootstrap.min.css')}}">
    <script type="text/javascript" src="{{ asset('js/jquery-3.3.1.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/popper.min.js')}}"></script>
    <script type="text/javascript" src="{{ asset('js/bootstrap.min.js')}}"></script>

    <script type="text/javascript">

        $(document).ready(function(){
            
            $('.page-login').show();

            $.ajax({
                method: 'POST',
                url: '/api/params',
                data: JSON.stringify({
                    user_id: -1,
                    method: "set",
                    request_action:1,
                    data:{
                        left:{
                            alpha:1
                        },
                        right:{
                            alpha:1
                        }                
                    }
                }),
                success:function(response){
                    console.log(response);
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.log("Parameters were not sent to the MHA. Make sure the software is running and try again.");
                }
            });

            var word_sets = [];

            testerID= "";
            participantID = "";
            var questionIndex = 1;
            numQuestions = 0;
            corrects = [];
            answers = [];
            wordSelected = "";
            const titleRow = ["Question#", "Words","Correct Answer","Participant's Answer"];
            //log in handler
            $('#login').click(function(event){
                event.preventDefault();

                //get the tester and participant ID
                testerID = $('#testerID').val();
                participantID = $('#participantID').val();

                $('.page-login').hide();
                $('.page-question').show();

                //load the word sets from json file
                async function load_word_set(){
                    try{
                        const response = await fetch("word_sets.json");

                        const word_sets = await response.json();

                        return word_sets;
                    }
                    catch(err){
                        alert(err.message);
                    }
                }

                //resolve the promise from async load function
                load_word_set().then(data => {
                    let tempWordSets = data;

                    for(var i =0;i<tempWordSets.length;i++){
                        if(tempWordSets[i]["words"]!==""){
                            word_sets.push(tempWordSets[i]);
                        }
                    }
                    numQuestions = word_sets.length;
                    //get all correct answers
                    for(var i =0;i<numQuestions;i++){
                        corrects.push(word_sets[i]["correct_answer"]);
                    }
                    console.log(word_sets);
                    setWords(questionIndex-1);
                    $('#progressBar').css('width',(100/numQuestions)+'%');
                });
            });

            //set the words of a certain question
            function setWords(questionIndex){
                words = word_sets[questionIndex]['words'];
                for(var i = 0;i<4;i++){
                    id = "#word"+(i+1).toString();
                    $(id).text(words[i]);
                }
            }

            //play corresponding wav file
            $('#playSound').on('click',function(event){
                event.preventDefault();
                var filePath = word_sets[questionIndex-1]['audio']['Actual_answer'];
                console.log(filePath);
                var playMode = 1;
                console.log("Attempting to play sound");
                
                // POST request to play audio
                $.ajax({
                    method: 'POST',
                    url: '/api/params',
                    data: JSON.stringify({
                        user_id: -1,
                        method: "set",
                        request_action:1,
                        data:{
                            left:{
                                audio_filename:filePath,
                                audio_reset:0,
                                audio_play:1,
                                audio_repeat:0
                            },
                            right:{
                                audio_filename:filePath,
                                audio_reset:0,
                                audio_play:1,
                                audio_repeat:0
                            }                
                        }
                    }),
                    success:function(response){
                        console.log(response);
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        console.log("Parameters were not sent to the MHA. Make sure the software is running and try again.");
                    }
                });
                

            }) ;

            //handler when press next question button
            $('#nextQuestion').on('click',function(event){
                event.preventDefault();
                $('#nextQuestion').attr('disabled','disabled');
                answers.push(wordSelected);
                questionIndex += 1;
                if(questionIndex>numQuestions){
                    $('.page-question').hide();
                    //append testerID and participant id
                    $('#info').append("<p>Tester ID: "+testerID+"</p>");
                    $('#info').append("<p>Participant ID: "+participantID+"</p>")
                    $('.page-result').show();
                    result = calculateResult();
                    $('#resultTitle').text("Test Result: "+result.length.toString()+"/"+numQuestions);

                    //add table entries
                    for(var i=0;i<numQuestions;i++){
                       
                        let words = word_sets[i]['words'];
                  
                        let wordString = "";
                        for(var j=0;j<words.length;j++){
                            wordString += words[j]+", ";
                        }
                    
                        id = "row"+(i+1).toString();
                        $('#resultTable tr:last').after(
                            "<tr id="+id+"><th scope='row'>"+(i+1).toString()+"</th><td>"+wordString+"</td><td>"+corrects[i]+"</td><td >"+answers[i]+"</td><tr>"
                        );
                    }

                    //change color of row for correct and wrong answer
                    for(var i=0;i<corrects.length;i++){
                        id = "#row"+(i+1).toString();
                        if(answers[i] === corrects[i]){
                            $(id).addClass('table-success');
                        }
                        else{
                            $(id).addClass('table-danger');
                        }
                    }
                    return;
                }
    
                setWords(questionIndex-1);
                var value = questionIndex*(100/numQuestions);
                $('#progressBar').css('width',value+'%').attr('aria-valuenow',value);
                if(questionIndex === numQuestions){
                    $('#nextQuestion').text("Done");
                }
            });



            $("#download").on("click",function(event){
                event.preventDefault();
                var res = [];
                res.push(titleRow);


                for(var i =0;i<numQuestions;i++){
                    let line = [];
                    let temp = "";
                    let word = word_sets[i]['words']
                    for(var j = 0;j<word.length;j++){
                        temp += word[j]+" ";
                    }
                    line.push(i.toString());
                    line.push(temp);
                    line.push(corrects[i]);
                    line.push(answers[i]);
                    res.push(line);
                }
                console.log(res);
                let csvContent = "data:text/csv;charset=utf-8,";
                res.forEach(function(rowArray){
                    let row = rowArray.join(",");
                    csvContent += row+"\r\n";
                });
                console.log(csvContent);

                var encodeUri = encodeURI(csvContent);
                window.open(encodeUri);
                
            });

            $('#word1').on('click',function(event){
                event.preventDefault();
                wordSelected = $('#word1').text();
                $('#nextQuestion').removeAttr('disabled');
            });

            $('#word2').on('click',function(event){
                event.preventDefault();
                wordSelected = $('#word2').text();
                $('#nextQuestion').removeAttr('disabled');
            });

            $('#word3').on('click',function(event){
                event.preventDefault();
                wordSelected = $('#word3').text();
                $('#nextQuestion').removeAttr('disabled');
            });

            $('#word3').on('click',function(event){
                event.preventDefault();
                wordSelected = $('#word3').text();
                $('#nextQuestion').removeAttr('disabled');
            });

            $('#word4').on('click',function(event){
                event.preventDefault();
                wordSelected = $('#word4').text();
                $('#nextQuestion').removeAttr('disabled');
            });

            $('#start-over').on('click',function(event){
                event.preventDefault();
                location.reload(true);
            });

            //function to calcualte the correction rate
            function calculateResult(){
                result = [];
                for(var i=0;i<corrects.length;i++){
                    if(answers[i] === corrects[i]){
                        result.push(answers[i]);
                    }
                }
                return result;
            }
        });


    </script>

    <title>4AFC</title>

    <style>
        .page-login{
            /* display: none; */
        }

        .page-question{
            display:none;
        }

        .page-result{
            display: none;
        }

        .page-question{
            display:none;
        }
        .container-login{
            justify-content: center;
            text-align: center;
            margin-top: 50px;
        }

        .questions{
            width:80%;
            margin-bottom: 15px
        }

        .description{
            margin-top:15px;
            margin-bottom: 15px;
        }

        .container-questions{
            justify-content: center;
            text-align: center;
            margin-top: 30px;
        }
        .form-group{
            max-width: 280px;
            margin: auto;
        }

        .form-control{
            margin-bottom: 10px;
        }

        .btn-margin{
            margin-bottom: 10px;
        }
        #AppTitle{
            margin-bottom: 10px;
        }

        #progressBar{
            width: 0%;
        }

        #resultTable{
            margin-top: 20px;
        }

    </style>
</head>
<body>

    <section class = "page-login">
        <div class="container-login">
            <h3 class="AppTitle">4AFC Web App</h3>
            <div class="form-group">
            <input class="form-control" type="text" placeholder="Tester ID" id = "testerID">
            <input class="form-control" itype="text" placeholder="Participant ID" id = "participantID">
            </div>
            <div style="max-width:150px; margin:auto">
                <button type="button" class="btn btn-info btn-block" id="login">Log in</button>
            <a href = "{{url('/')}}" class="btn btn-outline-danger btn-block">Exit</a>
            </div>
            
        </div>
    </section>

    <section class="page-question">

        <div class="container-questions">
            <h4 class="AppTitle">
                4AFC Web App
            </h4>
            <div class="progress">
                    <div class="progress-bar bg-info progress-bar-striped progress-bar-animated"
                    role="progressbar"
                    aria-valuenow="10"
                    aria-valuemin="0"
                    aria-valuemax="100"
                    id = "progressBar"
                    >
                    </div>
            </div>
            <h5 class="description">
                Press play and select the word you hear
            </h5>

            {{-- play button --}}
        
                <button type="button" id = "playSound" class="btn btn-default btn-play" 
                    style="width: 60px;height:60px;text-align:center;font-size:40px;"
                >
                    <span class="oi oi-play-circle oi-play"></span>
                </button>

            {{-- choices --}}
            <div class="container questions">
                <div class="row">
                    <div class="col-sm">
                            <button type="button"
                             class="btn btn-outline-info btn-lg btn-block btn-margin"
                             id = "word1"
                             >

                            </button>
                    </div>
                    <div class="col-sm">
                            <button type="button"
                             class="btn btn-outline-info btn-lg btn-block btn-margin"
                             id = "word2"
                             >

                            </button>
                    </div>
                </div>
                <div class="row">
                        <div class="col-sm">
                                <button type="button"
                                 class="btn btn-outline-info btn-lg btn-block btn-margin"
                                 id = "word3"
                                 >

                                </button>
                        </div>
                        <div class="col-sm">
                                <button type="button"
                                 class="btn btn-outline-info btn-lg btn-block btn-margin"
                                 id = "word4"
                                 >
                                </button>
                        </div>
                    </div>
            </div>
            <button type="button"
            class="btn btn-secondary btn-lg"
            id = "nextQuestion"
            disabled
            >
            Next</button>
        </div>
    </section>

    <section class="page-result">
        <div class="container-login">
            <h5 class="AppTitle" id="resultTitle">Test Result</h5>
            <div class="container" id = "info">

            </div>
            <div class="container">
                    <table class="table" id="resultTable">
                            <thead>
                              <tr>
                                <th scope="col">Question #</th>
                                <th scope="col">Words</th>
                                <th scope="col">Correct Answer</th>
                                <th scope="col">Participant's Answer</th>
                              </tr>
                            </thead>
                            <tbody>
                            </tbody>
                    </table>
                    <div style="max-width:300px;margin:auto;">
                            <button class="btn btn-info btn-lg btn-block" 
                                    id  = "start-over"    
                            >
                                Start Over
                            </button>
                            <button class="btn btn-outline-secondary btn-lg btn-block"
                                    id = "download"
                            >
                                Download Result
                            </button>
                    </div>
                    
            </div>

        </div>



    </section>




</body>
</html>
