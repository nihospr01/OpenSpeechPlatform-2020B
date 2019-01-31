
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/mobile.css') }}">
    <script type="text/javascript" src="{{ asset('js/jquery-3.3.1.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/popper.min.js')}}"></script>
    <script type="text/javascript" src="{{ asset('js/bootstrap.min.js')}}"></script>
    <script type="text/javascript">

        $(document).ready(function(){
            $('.page-login').show();
            
            testerID= "";
            participantID = "";
            title = "";
            partATitle = "";
            var questions = [];
            var currQuestion = 0;
            var numQuestions = 0;
            var singleRes = "";
            var responses = [];
            var multipleRes = new Set([]);
            var isMultiple = [];
            const titleRow = ["Questions#", "Participant's Answer"];
            //log in handler
            $('#login').click(function(event){
                event.preventDefault();

                //get the tester and participant ID
                testerID = $('#testerID').val();
                participantID = $('#participantID').val();

                $('.page-login').hide();
            
                //load the questions from json file
                async function load_survey(){
                    try{
                        const response = await fetch("ema.json");
                       
                        const survey = await response.json();
                       
                        return survey;
                    }
                    catch(err){
                        alert(err.message);
                    }
                }

                //resolve the promise from async load function
                load_survey().then(data => {
                    title = data["title"];
                    partATitle = data["partA"];
                    
                    let tempQuestions = data["questions"];

                    for(var i =0;i<tempQuestions.length;i++){
                        if(tempQuestions[i]["question_title"]!==""){
                            questions.push(tempQuestions[i]);
                        }
                    }

                    console.log(questions);
                    numQuestions = questions.length;
                    for(var i =0;i<numQuestions;i++){
                        isMultiple.push(questions[i]["isMultiple"]);
                    }
                    $("#SurveyTitle").show();
                    $("#surveyTitle").text(title);
                    $('#progressBar').css('width',(100/numQuestions)+'%');
                });
            });

            $('#continue').click(function(event){
                event.preventDefault();
                $('#SurveyTitle').hide();
                $("#partATitle").text(partATitle);
                $('#PartA').show();
                $('#nextQuestion').attr('disabled','disabled');
            });

            

            $('#toQuestions').click(function(event){
                event.preventDefault();
                $('#PartA').hide();
                $('#SurveyQuestioins').show();
                setQuestions(currQuestion);
            });

            $('#nextQuestion').click(function(event){
                event.preventDefault();
                $('#nextQuestion').attr('disabled','disabled');
                if(multipleRes.size === 0){
                    responses.push(singleRes);
                }
                else{
                    responses.push(multipleRes);
                }
                singleRes = "";
                multipleRes = new Set([]);
                currQuestion += 1;
                if(currQuestion === numQuestions){
                    $('#SurveyQuestioins').hide();
                  
                    $('#info').append("<p>Tester ID: "+testerID+"</p>");
                    $('#info').append("<p>Participant ID: "+participantID+"</p>")
                    
                    //Fill out result form
                    for(var i=0;i<numQuestions;i++){
                        let res = "";
                        if(isMultiple[i] == 1){
                            var count = 1;
                            responses[i].forEach(element => {
                                let temp = "("+count+")"+element+".<br/> ";
                                res += temp;
                                count++;
                            });
                        }
                        else{
                            res = responses[i];
                        }
                       
                        id = (i+1).toString();
                        $("#resultTable tr:last").after(
                            "<tr><th scope='row'>"+id+"</th><td style='text-align:left'>"+res+"</td><tr>"
                        );
                    }

                    $("#SurveyResult").show();
                    return;
                }

                if(currQuestion === numQuestions-1){
                    $('#nextQuestion').text("Done");
                }

                $("#titleArea").empty();
                $("#choiceArea").empty();
                setQuestions(currQuestion);
                var value = (currQuestion+1)*(100/numQuestions);
                $('#progressBar').css('width',value+'%').attr('aria-valuenow',value);
            });



            $('#download').on("click",function(event){
                event.preventDefault();
                var ret = [];
                ret.push(titleRow);
                for(var i =0;i<numQuestions;i++){
                    let line = [];
                    line.push(i.toString());
                    let ans = "";
                    if(isMultiple[i] == 0){
                        ans = responses[i];
                    }
                    else{
                        var count = 1;
                        responses[i].forEach(element => {
                            let temp = "("+count+")"+element+" ";
                            ans += temp;
                            count++;
                        });
                    }
                    line.push(ans);
                    ret.push(line);
                }
     
                let csvContent = "data:text/csv;charset=utf-8,";
                ret.forEach(function(rowArray){
                    let row = rowArray.join(";");
                    csvContent += row+"\r\n";
                });
     
                var encodeUri = encodeURI(csvContent);
                window.open(encodeUri);

            });



            $('#prevQuestion').click(function(event){
                event.preventDefault();
                currQuestion -= 1;
                singleRes = "";
                multipleRes = new Set([]);
                responses.pop();
                if(currQuestion < 0){
                    window.location.replace("/");
                    return;
                }
                $("#titleArea").empty();
                $("#choiceArea").empty();
                setQuestions(currQuestion);
                var value = (currQuestion+1)*(100/numQuestions);
                $('#progressBar').css('width',value+'%').attr('aria-valuenow',value);
            });


            function setQuestions(questionIndex){
                let title = questions[questionIndex]["question_title"];
                $("<h4 style=>"+title+"</h4>").appendTo("#titleArea");
                let choices = questions[questionIndex]["choices"];
                var res = "";

                //If the user can only select one answer
                if(questions[questionIndex]["isMultiple"]===0){
                    for(var i = 0;i<choices.length;i++){
                        res += "<button class='btn btn-lg btn-outline-secondary btn-block btn-choice-single' >"+choices[i]+"</button>";
                    }
                    $(document).on("click", ".btn-choice-single", function(event){
                        event.preventDefault();
                        
                        if(questions[questionIndex]["isMultiple"]===0){
                            singleRes = $(this).text();
                            $('#nextQuestion').removeAttr('disabled');
                        }   
                    }); 
                }
                //If the user can select multiple answers
                else{
                    for(var i = 0;i<choices.length;i++){
                        res += "<button class='btn btn-lg btn-block btn-choice-multiple' >"+choices[i]+"</button>";
                    }
                    $(document).on("click", ".btn-choice-multiple", function(event){

                        if($(this).hasClass("btn-secondary")){
                            // $(this).toggleClass("btn-outline-secondary");
                            multipleRes.delete($(this).text());
                            $(this).toggleClass(" btn-secondary");
                           
                        }
                        else{
                            $(this).toggleClass("btn-secondary");
                            multipleRes.add($(this).text());
                            // $(this).removeClass("btn-secondary");
                            
                        } 
                        if(multipleRes.size>0){
                            $('#nextQuestion').removeAttr('disabled');
                        }
                    });

                }

                $(res).appendTo("#choiceArea");
            }

            $('#StartOver').on('click',function(event){
                event.preventDefault();
                location.reload(true);
            });
        });


    </script>

    <title>EMA</title>

    <style>
        .page-login{
            /* display: none; */
        }

        #SurveyTitle{
            display: none;
        }

        #PartA{
            display: none;
        }

        #SurveyResult{
            display: none;
        }

        .btn-choice-single{
            white-space: normal;
        }

        .btn-choice-multiple{
            white-space: normal;
        }
        
        .progress{
            margin-bottom: 20px;
        }
        .container-login{
            justify-content: center;
            text-align: center;
            margin-top: 50px;
        }

        .description{
            margin-top:15px;
            margin-bottom: 15px;
        }

        .container-questions{
            justify-content: center;
            text-align: center;
            margin-top: 30px;
            margin-left: auto;
            margin-right: auto;
        }
        .form-group{
            max-width: 300px;
            margin: auto;
        }

        .form-control{
            margin-bottom: 10px;
        }

        .btn-margin{
            margin-bottom: 10px;
        }

        .AppTitle{
            margin-bottom: 20px;
            max-width: 300px;
            margin-left: auto;
            margin-right: auto;
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

    {{-- Section for log in page --}}
    <section class = "page-login">
        <div class="container-login">
            <h3 class="AppTitle">EMA Web App</h3>
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

    {{-- section for task description --}}
    <section id = "SurveyTitle">
        <div class="container-questions">    
            <div class="AppTitle" style="text-align:left">
                <h4 id = "surveyTitle">
                
                </h4>
            </div>
            <div style="max-width:300px;margin:auto">
                <button class="btn btn-info btn-lg btn-block" id = "continue">
                    Continue
                </button>
                <a href="{{ url('/') }}" class = "btn btn-outline-danger btn-lg btn-block" >
                    Exit
                </a>
            </div>
        </div>
    </section>


    {{-- Section for Part-A Description --}}
    <section id = "PartA">
            <div class="container-questions">
                
                <div class="AppTitle" style="text-align:left">
                    <h4 id = "partATitle">
                    
                    </h4>
                </div>
                <div style="max-width:300px;margin:auto">
                    <button class="btn btn-info btn-lg btn-block" id="toQuestions">
                        Next ->
                    </button>
                </div>
            </div>
    </section>

    {{-- Sections for actual survey questions --}}
    <section id = "SurveyQuestioins" style="display:none">

            <div class="container-questions">
                <div class="progress">
                    <div class="progress-bar bg-info"
                    role="progressbar"
                    aria-valuenow="10"
                    aria-valuemin="0"
                    aria-valuemax="100"
                    id = "progressBar"
                    >
                    </div>
                </div>
                    <div class="AppTitle" style="text-align:left" id = "titleArea">
    
                    </div>

                    <div style="max-width:300px;margin-left:auto;margin-right:auto;margin-bottom:30px" id="choiceArea">
                        
                    </div>

                    <div style="max-width:300px;margin:auto;display:flex" id="controlArea">
                        <button class="btn btn-danger btn-lg "
                            id = "prevQuestion"
                            style="flex-grow:1; margin-right:5px">
                            <- Back
                        </button>
                        <button class="btn btn-info btn-lg " 
                                id  = "nextQuestion"    
                                style="flex-grow:1; margin-left:5px">
                            Next ->
                        </button>
                    </div>
            </div>
    </section>

    {{-- Section for survey result --}}
    <section id="SurveyResult">
        <div class="container-login">
            <h4 class="AppTitle">Survey has ended. <br> Plesase confirm your response.
            </h5>

            <div class="container" id = "info"> 
            </div>

            <div class="container">
                <table class="table" id="resultTable">
                        <thead>
                            <tr>
                            <th scope="col">Question #</th>
                            <th scope="col">Participant's Answer</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                </table>
                <div style="max-width:300px;margin:auto;">
                        <button class="btn btn-info btn-lg btn-block" 
                                id  = "download"    
                        >
                            Download Result
                        </button>
                        <button class="btn btn-outline-danger btn-lg btn-block"
                                id = "StartOver"
                        >
                            Start Over
                        </button>
                </div>
            </div>

        </div>



    </section>


</body>
</html>
