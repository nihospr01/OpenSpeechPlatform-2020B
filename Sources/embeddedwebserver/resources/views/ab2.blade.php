<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
  <link rel="stylesheet" href="{{ asset('css/mobile.css') }}">
  <link rel="stylesheet" href="{{ asset('css/open-iconic-bootstrap.min.css')}}">
  <link rel="stylesheet" href="{{ asset('css/animate.min.css')}}">
  <script type="text/javascript" src="{{ asset('js/jquery-3.3.1.min.js') }}"></script>
  <script type="text/javascript" src="{{ asset('js/popper.min.js')}}"></script>
  <script type="text/javascript" src="{{ asset('js/bootstrap.min.js')}}"></script>
  <script type='text/javascript'>

   $(document).ready(function(){
    $('.section-choices').hide();
    $('.section-controls').hide();
    $('.section-results').hide();
    var A_listened = 0;
    var B_listened = 0;
    var numIteration = 1;
    var a = 0;
    var b = 0;
    var low = -20;
    var high = 0;

    function calculateA(lo, hi){
      var diff = (hi-lo)/4;
      a = lo + diff;
      return {
        'left':{
          'g50': [a, a, a, a, a, a],
          'g80': [a, a, a, a, a, a]
        },
        'right':{
          'g50': [a, a, a, a, a, a],
          'g80': [a, a, a, a, a, a]
        }
      }
    }

    function calculateB(lo, hi){
      var diff = (hi-lo)/4;
      b = hi - diff;
      return {
        'left':{
          'g50': [b, b, b, b, b, b],
          'g80': [b, b, b, b, b, b]
        },
        'right':{
          'g50': [b, b, b, b, b, b],
          'g80': [b, b, b, b, b, b]
        }
      }
    }
    
    var A = calculateA(low,high);
    var B = calculateB(low,high); 

    function transmitParams(direction){
      $.ajax({
        method: 'POST',
        url: '/api/params',
        data: JSON.stringify({
          user_id: -1,
          method: "set",
          request_action: 1,
          data: direction
        }),
        success: function(response){
          var paramKeys = Object.keys(direction['left']);
          for(var i =2;i<paramKeys.length;i++){
            console.log(paramKeys[i]+": "+direction['left'][paramKeys[i]]);
          }
          console.log(response);
        },
        error: function(jqXHR, textStatus, errorThrown){
          console.log("Parameters were not sent to the MHA");
        }
      });
    }


    function onStartPlaying(){
      // console.log("attempting to transmit");
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
              audio_play:1,
              audio_repeat:1,
              alpha:1,
              afc:0
            },
            right:{
              audio_filename:'/audio/AB/MPEG_es01_s.wav',
              audio_play:1,
              audio_repeat:1,
              alpha:1,
              afc:0
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

    function onExit(){
      // console.log("attempting to transmit");
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
              audio_play:0
            },
            right:{
              audio_filename:'/audio/AB/MPEG_es01_s.wav',
              audio_play:0
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

    function showControls(callback){
      $('.section-controls').addClass("animated fadeInUp");
      function handleAnimationEnd(){
        $('.section-controls').removeClass("animted fadeInUp");
        if(typeof callback == 'function') callback();
      }
      $('.section-controls').on("animationend",handleAnimationEnd)
    }

    $('#A').click(function(){
      $('#A').addClass('bg-info');
      $('#B').removeClass('bg-info');
      transmitParams(A);
    });
    
    $('#B').click(function(){
      $('#B').addClass('bg-info');
      $('#A').removeClass('bg-info');
      transmitParams(B);
    });

    $('#startPlaying').click(function(){
      $('.section-intro').hide();
      $('.section-controls').show();
      showControls(function(){
        onStartPlaying();
      })
      $('.section-choices').show()
      $('.section-choices').addClass("animated fadeInUp");
    });

    $('#A_better').click(function(){
      high = b;
      A = calculateA(low,high);
      B = calculateB(low,high);
      console.log(A)
      transmitParams(A);
      A_listened = 0;
      numIteration++;
    });

    $('#B_better').click(function(){
      low = a;
      A = calculateA(low,high);
      B = calculateB(low,high);
      console.log(B);
      transmitParams(B);
      B_listened = 0;
      numIteration++;
    });

    $('#AB_equal').click(function(){
      $('.section-controls').hide();
      $('.section-choices').hide();
      $("#valueA").text("The overall gain of A is: "+a);
      $("#valueB").text("The overall gain of B is: "+b);
      $("#valueDiff").text("The difference is: "+(b-a));
      $("#iteration").text("The number of iterations is: "+numIteration);
      $('.section-results').show();
      onExit();
    });

    $('#exit').click(function(){
      onExit();
    });
   });
  </script>
 
 <style>

 .container{
   width: 100%;
   justify-content: center;
 }

 .container-choices{
  margin-top: 30px;
  justify-content: center;
  text-align: center;
 }

 .grid-choices{
    width:80%;
    display: inline-block;
    margin-top: 20px;
 }

 .btn{
   margin-bottom: 10px;

 }

 .h3{
   text-align: center;
   align-items: center;
   align-self: center;
   align-content: center;
 }

 .col{
   text-align: center;

 }

 .btn-play{
  width: 60px;
  height: 60px;
  text-align: center;
  font-size: 40px;
 }

 #status{
  margin-top: 100px;
  text-align: center;
  margin-bottom: 20px;
 }

 #title{
   height: 1.5cm;
   text-align: center;
   display: table
 }

 #titleText{
  display: table-cell;
  vertical-align: middle;
 }

 #audio0{
  margin: auto;
 }

 </style>

</head>


<!-- Section for intro -->
<section class="section-intro">
 <div class="container">
    <div class="container" id="title" style="margin-top:100px; max-width:400px">
          <h1 class="animated fadeInUp display-4">
            AB Test
          </h1>
  
          <h3 class="text-secondary animated fadeInUp delay-1s"
          style="margin-top:20px;margin-left:auto;margin-right:auto">
             Play the audio file and select between two sets of parameter until you can not tell the difference.
          </h3>
          <button class="btn btn-info btn-lg btn-block animated fadeInUp delay-2s" 
                  style="margin-top:30px"
                  id = "startPlaying"       
          >
            Let's get started
          </button>
          <div class="container animated fadeInUp delay-2s" style="max-width:150px;margin-top:15px">
          <a href = "{{url('/')}}" class="btn btn-block btn-outline-danger">Exit</a>
          </div>
      </span>
    </div>
 </div>
</section>


<!-- Section for A/B contro -->

<section class="section-controls">
  <div class="container"
       id = "status" >
    <h2 id = "listen_again">Listen to both A and B</h2>
  </div>
  <div class="container">
    <div class="row ">
        <div class="col" >
          <button type="button" id = "A" class="btn btn-default btn-play" 
  
          >
            <span class="oi oi-play-circle oi-play"></span>
          </button>
          <h3>A</h3>
        </div>

        <div class="col">
          <button type="button" id = "B" class="btn btn-default btn-play">
            <span class="oi oi-play-circle oi-play"></span>
          </button>
          <h3>B</h3>
        </div>
    </div>
  </div>
</section>


<!-- Section for 3-scale likert Control -->
<section class="section-choices">
  <div class="container-choices">
  <h3 class="text-secondary">Select which one is better</h3>
    <div class="grid-choices">
      <div class="row">
        <div class="col-sm">
          <button 
            type="button"
            class="btn btn-outline-info btn-lg btn-block"
            id = "A_better"
          >
            A is better than  B
          </button>
        </div>
        <div class="col-sm">
          <button 
            type="button" 
            class="btn btn-outline-info btn-lg btn-block"
            id = "AB_equal"
          >
            A and B are equal
          </button>
        </div>
        <div class="col-sm">
          <button 
            type="button" 
            class="btn btn-outline-info btn-lg btn-block"
            id = "B_better"
          >
            B is better than A
          </button>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Section for result -->
<section class="section-results">
  <div class="container"
       id = "status" >
    <h1  id = "result" class = "animated fadeInUp">Here is your result</h2>  
    <div class="container animated fadeInUp delay-1s " style="margin-top:20px">
      <h4 class = "text-secondary" id="valueA">
     
      </h4>
      <h4 class = "text-secondary" id="valueB">
  
      </h4>
      <h4 class="text-secondary" id="valueDiff">
  
      </h4>
      <h4 class="text-secondary" id="iteration">
  
      </h4>
    </div>
    <div class = "animated fadeInUp delay-2s" style="max-width:150px;margin-top:30px;margin-left:auto;margin-right:auto">
      <a  href = "{{url('/ab2')}}" class="btn btn-info btn-block" id="exit">
          Start over
      </a>
      <a  href = "{{url('/')}}" class="btn btn-outline-danger btn-block" id="exit">
            Exit
     </a>
    </div>
  </div>

</section>
