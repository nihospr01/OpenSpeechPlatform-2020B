<html>

<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}"> 
<link href="css/app.css" rel="stylesheet">
<link href="css/question.css" rel="stylesheet">
<script type="text/javascript" src="{{ asset('js/jquery-3.3.1.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/popper.min.js')}}"></script>
<script type="text/javascript" src="{{ asset('js/bootstrap.min.js')}}"></script>

<header>
  <h2> This bar will track your progress in the task </h2>

  <div class="progress">
    <div class="progress-bar" role="progressbar" style="width: 100%" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
  </div>

</header>

<head>
  <title></title>

  <link href="css/app.css" rel="stylesheet">
  <meta name="viewport" content="width=device-width, initial-scale= 1.0">

  <h1>Welcome, user!</h1>

  <h2> Dr.Doctor has left this message for you. Once you have read it, please start the task! </h2>

  <div class="startTaskButton">
    <a href="/question" <button id="start">
      <span>Start Task</span>
      </button>
    </a>
  </div>

</head>