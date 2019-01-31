<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
  <link rel="stylesheet" href="{{ asset('css/mobile.css') }}">
  <link rel="stylesheet" href="https://cdn.plyr.io/3.4.7/plyr.css">
  <link rel="stylesheet" href="{{ asset('css/open-iconic-bootstrap.min.css')}}">
  {{-- <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.5.0/css/all.css" integrity="sha384-B4dIYHKNBt8Bc12p+WXckhzcICo0wtJAoU8YZTY5qE0Id1GSseTk6S+L3BlXeVIU" crossorigin="anonymous"> --}}
  <script type="text/javascript" src="{{ asset('js/jquery-3.3.1.min.js') }}"></script>
  <script type="text/javascript" src="{{ asset('js/popper.min.js')}}"></script>
  <script type="text/javascript" src="{{ asset('js/bootstrap.min.js')}}"></script>
  <script src="https://cdn.plyr.io/3.4.7/plyr.polyfilled.js"></script>

  <script type='text/javascript'>

   $(document).ready(function(){
      const supportAudio = !!document.createElement('audio').canPlayType;
      //Create the audio player if the browser supports 
      if(supportAudio){
        const player = new Plyr("#audioPlayer",{
          controls:[
            'restart',
            'play',
            'progress',
            'current-time',
            'duration',
            'mute',
            'volume'
          ]
        });
    }
  

   });

  </script>
 
 <style>

 .container{
   width: 100%;

 }

 .container-choices{
 
  margin-top: 15px;
  justify-content: center;
  text-align: center;

 }

 .grid-choices{
  width:80%;
    display: inline-block;
 }
 .leftBtn{
   padding: .5rem;
   font-size:3.2rem;
   line-height:1.5;
   border-radius:.3rem;
   width: 250px;
   height: 190px;
   align-self: left;
   margin-left: 330px;
 }
 .rightBtn{

   padding: .5rem;
   font-size:3.2rem;
   line-height:1.5;
   border-radius:.3rem;
   left:0;
   width:250px;
   height: 190px;
   align-self: right;
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
  width: 70%
 }
 #statusAction{
   width:30%
 }

 #statusClip{
    width:70%
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

 </style>

</head>

<div class="container" style="background-color: white;">

  <div class="container-fluid"
       id = "title" 
  >
    <span id = "titleText"><h4>AB Test</h4></span>
  </div>

  {{-- Container for audio player --}}
  <div class="container">
    <div class="container" id = "status"> 
      <span id = "statusAction">
       Paused...
      </span>
      <span id = "statusClip">
      </span>
    </div>
    <div id="audio0">
        <audio preload controls id = "audioPlayer"></audio>
    </div>
  </div>

    <div class="text-center" >
      <h3 >This is your text</h3>
    </div>

    
    <div class="row ">
        <div class="col" >
          <button type="button" id = "A" class="btn btn-default btn-play">
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

    <div class="container-choices">
        <div class="grid-choices">
            <div class="row">
                <div class="col-sm">
                    <button type="button" class="btn btn-outline-primary btn-lg btn-block">A >>> B</button>
                    <button type="button" class="btn btn-outline-primary btn-lg btn-block">A >> B</button>
                    <button type="button" class="btn btn-outline-primary btn-lg btn-block">A > B</button>
          
                </div>
          
                <div class="col-sm">
                    <button type="button" class="btn btn-outline-primary btn-lg btn-block">A = B</button>
                  
                </div>
          
                <div class="col-sm">
                    <button type="button" class="btn btn-outline-primary btn-lg btn-block">B >>> A</button>
                    <button type="button" class="btn btn-outline-primary btn-lg btn-block">B >> A</button>
                    <button type="button" class="btn btn-outline-primary btn-lg btn-block">B > A</button>
                </div>
        </div>


    </div>






    </div>
    <br>

</div>
