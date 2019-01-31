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
       
            word_sets = [];
            
            testerID= "";
            participantID = "";
            var questionIndex = 1;
            numQuestions = 0;
            corrects = [];
            answers = [];
            wordSelected = "";

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
                        const response = await fetch('word_sets.json');
                        const word_sets = await response.json();
                        return word_sets;
                    }
                    catch(err){
                        alert('fetch questions failed',err);
                    }
                }
                
                //resolve the promise from async load function
                load_word_set().then(data => {
                    word_sets = data;
                    numQuestions = word_sets.length;
                    //get all correct answers
                    for(var i =0;i<numQuestions;i++){
                        corrects.push(word_sets[i]["correct_answer"]);
                    }
                    console.log(corrects);
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
            
            //handler when press next question button
            $('#nextQuestion').on('click',function(event){
                event.preventDefault();
                answers.push(wordSelected);
                questionIndex += 1;
                if(questionIndex>4){
                    $('.page-question').hide();
                    $('.page-result').show();
                    result = calculateResult();
                    $('#resultTitle').text("Test Result: "+result.length.toString()+"/"+numQuestions);
                    
                    //add table entries
                    for(var i=0;i<numQuestions;i++){
                        words = word_sets[i]['words'];
                        wordString = "";
                        for(var j=0;j<words.length;j++){
                            wordString += words[i]+", ";
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

            $('#word1').on('click',function(event){
                event.preventDefault();
                wordSelected = $('#word1').text();
            });

            $('#word2').on('click',function(event){
                event.preventDefault();
                wordSelected = $('#word2').text();
            });
            
            $('#word3').on('click',function(event){
                event.preventDefault();
                wordSelected = $('#word3').text();
            });

            $('#word4').on('click',function(event){
                event.preventDefault();
                wordSelected = $('#word4').text();
            });


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
        .container-login{
            /* width:60%; */
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
            width: 30%;
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
            <h3 id="AppTitle">4AFC Web App</h3>
            <div class="form-group">
            <input class="form-control" type="text" placeholder="Tester ID" id = "testerID">
            <input class="form-control" itype="text" placeholder="Participant ID" id = "participantID">
            </div>
            <button type="button" class="btn btn-primary" id="login">Log in</button>
        </div>
    </section>

    <section class="page-question">
        
        <div class="container-questions">
            <h4 class="AppTitle">
                4AFC Web App
            </h4>
            <div class="progress">
                    <div class="progress-bar progress-bar-striped progress-bar-animated" 
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
            <div class="container questions">
                <div class="row">
                    <div class="col-sm">
                            <button type="button"
                             class="btn btn-outline-primary btn-lg btn-block btn-margin"
                             id = "word1"
                             >
                             
                            </button>           
                    </div>
                    <div class="col-sm">
                            <button type="button"
                             class="btn btn-outline-primary btn-lg btn-block btn-margin"
                             id = "word2"
                             >
                          
                            </button>           
                    </div>
                </div>
                <div class="row">
                        <div class="col-sm">
                                <button type="button"
                                 class="btn btn-outline-primary btn-lg btn-block btn-margin"
                                 id = "word3"
                                 >
               
                                </button>           
                        </div>
                        <div class="col-sm">
                                <button type="button"
                                 class="btn btn-outline-primary btn-lg btn-block btn-margin"
                                 id = "word4"
                                 >
                    
                                </button>           
                        </div>
                    </div>
            </div>
            <button type="button" 
            class="btn btn-secondary btn-lg"
            id = "nextQuestion"
            >
            Next</button>
        </div>
    </section>

    <section class="page-result">
        <div class="container-login">
            <h5 class="AppTitle" id="resultTitle">Test Result</h5>
            
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
            </div>
   
        </div>



    </section>



    
</body>
</html>