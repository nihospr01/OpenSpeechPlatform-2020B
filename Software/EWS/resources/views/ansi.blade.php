<!DOCTYPE html>
<html lang="en">

<head>

  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
  <script type="text/javascript" src="{{ asset('js/jquery-3.3.1.min.js') }}"></script>
  <script type="text/javascript" src="{{ asset('js/popper.min.js')}}"></script>
  <script type="text/javascript" src="{{ asset('js/bootstrap.min.js')}}"></script>


</head>


<body>
  <div class="container-fluid">
    <div class="row">
      <div class="col-sm-8">
        <h3>ANSI Test</h3>
        <h4>1. When using this app, please make sure that you are running OSP in pass through mode</h4>
        <h4>2. Connect the receiver </h4>
        <h4>3. You will see the ANSI 3.22 outputs in a device like verifit </h4>

        <p></p>
        <form class="form-horizontal">
          <div class="form-inline">
            <label for="micPathGain"  class="h5" style="color:Green">Mic Path Gain</label>
            <div class="col-sm-8">
              <input type="text" style="border:2px" id="micPathGain" placeholder="0">
            </div>
          </div>
          <p></p>
          <div class="form-inline">
            <label for="rxPathGain" class="h5" style="color:Red">Receiver Path Gain</label>
            <div class="col-sm-8">
              <input type="text" class="text_1" style="border:2px" id="rxPathGain" placeholder="0">
            </div>
          </div>
        </form>
        <p></p>
        <button type="button" class="btn btn-primary">RUN ANSI</button>
      </div>

      <div class="row">
        <img src="{{ asset('ansi2.png') }}">
      </div>
    </div>








</body>
