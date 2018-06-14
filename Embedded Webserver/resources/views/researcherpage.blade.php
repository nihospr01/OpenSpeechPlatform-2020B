<html lang="en">

<head>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">

<style>
.form-control {
    background-color: rgba(0, 0, 0, 0);
    border: 0;
}
.form-control:focus {
    background-color: rgba(0, 0, 0, 0);
}
</style>

    <title>MHA Control</title>
</head>


<body>

  <div class="container">
    <div class="row">
        <div class="col-md-4 col-md-offset-3">
            Control Via:
            <input class="form-check form-check-inline align-middle" type="radio" name="ctrlSrc" id="radio1" value="G50/G80" onclick="allg50g80()">
            <label class="form-check-label" for="radio1">G50/G80</label>
            <input class="form-check form-check-inline align-middle" type="radio" name="ctrlSrc" id="radio2" value="CompRatio/G65" onclick="allcrg65()"> 
            <label class="form-check-label" for="radio2">CompRatio/G65</label>
        </div>
    </div>
  </div>

    <div class="container">
        <h3 class="text-center" style="padding-top: 10; padding-bottom: 10; font-size: 2.5rem;">Researcher Page</h3>

        <div class="row">
            <table class="table table-striped table-bordered table-sm" style="border-color: white">
                <thead>
                    <th class="text-center" style="font-size: 2.5rem; border-color: white">Frequency</th>
                    <th class="center-text" style="font-size: 2rem; border-color: white">177</th>
                    <th class="center-text" style="font-size: 2rem; border-color: white">354</th>
                    <th class="center-text" style="font-size: 2rem; border-color: white">707</th>
                    <th class="center-text" style="font-size: 2rem; border-color: white">1414</th>
                    <th class="center-text" style="font-size: 2rem; border-color: white">2828</th>
                    <th class="center-text" style="font-size: 2rem; border-color: white">5657</th>
                </thead>

                <tbody>

                    <tr>
                        <th class="text-center" style="font-size: 2.5rem; width: 40%; border-color: white">Compression Ratio</th>
                        <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" id="cr_0" class="form-control form-control-sm table-field font-weight-bold" type="number" value="0" onchange="changeColor(this.id)" onblur="crg65(0)"></td>
                        <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" id="cr_1" class="form-control form-control-sm table-field font-weight-bold" type="number" value="0" onchange="changeColor(this.id)" onblur="crg65(1)"></td>
                        <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" id="cr_2" class="form-control form-control-sm table-field font-weight-bold" type="number" value="0" onchange="changeColor(this.id)" onblur="crg65(2)"></td>
                        <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" id="cr_3" class="form-control form-control-sm table-field font-weight-bold" type="number" value="0" onchange="changeColor(this.id)" onblur="crg65(3)"></td>
                        <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" id="cr_4" class="form-control form-control-sm table-field font-weight-bold" type="number" value="0" onchange="changeColor(this.id)" onblur="crg65(4)"></td>
                        <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" id="cr_5" class="form-control form-control-sm table-field font-weight-bold" type="number" value="0" onchange="changeColor(this.id)" onblur="crg65(5)"></td>
                    </tr>

                    <tr>
                        <th class="text-center" style="font-size: 2.5rem; border-color: white">G50</th>
                        <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" id="g50_0" class="form-control form-control-sm table-field font-weight-bold" type="number" value="0" onchange="changeColor(this.id)" onblur="g50g80(0)"></td>
                        <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" id="g50_1" class="form-control form-control-sm table-field font-weight-bold" type="number" value="0" onchange="changeColor(this.id)" onblur="g50g80(1)"></td>
                        <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" id="g50_2" class="form-control form-control-sm table-field font-weight-bold" type="number" value="0" onchange="changeColor(this.id)" onblur="g50g80(2)"></td>
                        <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" id="g50_3" class="form-control form-control-sm table-field font-weight-bold" type="number" value="0" onchange="changeColor(this.id)" onblur="g50g80(3)"></td>
                        <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" id="g50_4" class="form-control form-control-sm table-field font-weight-bold" type="number" value="0" onchange="changeColor(this.id)" onblur="g50g80(4)"></td>
                        <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" id="g50_5" class="form-control form-control-sm table-field font-weight-bold" type="number" value="0" onchange="changeColor(this.id)" onblur="g50g80(5)"></td>
                    </tr>

                    <tr>
                        <th class="text-center" style="font-size: 2.5rem; border-color: white">G65</th>
                        <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" id="g65_0" class="form-control form-control-sm table-field font-weight-bold" type="number" value="0" onchange="changeColor(this.id)" onblur="crg65(0)"></td>
                        <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" id="g65_1" class="form-control form-control-sm table-field font-weight-bold" type="number" value="0" onchange="changeColor(this.id)" onblur="crg65(1)"></td>
                        <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" id="g65_2" class="form-control form-control-sm table-field font-weight-bold" type="number" value="0" onchange="changeColor(this.id)" onblur="crg65(2)"></td>
                        <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" id="g65_3" class="form-control form-control-sm table-field font-weight-bold" type="number" value="0" onchange="changeColor(this.id)" onblur="crg65(3)"></td>
                        <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" id="g65_4" class="form-control form-control-sm table-field font-weight-bold" type="number" value="0" onchange="changeColor(this.id)" onblur="crg65(4)"></td>
                        <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" id="g65_5" class="form-control form-control-sm table-field font-weight-bold" type="number" value="0" onchange="changeColor(this.id)" onblur="crg65(5)"></td>
                    </tr>

                    <tr>
                        <th class="text-center" style="font-size: 2.5rem; border-color: white">G80</th>
                        <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" id="g80_0" class="form-control form-control-sm table-field font-weight-bold" type="number" value="0" onchange="changeColor(this.id)" onblur="g50g80(0)"></td>
                        <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" id="g80_1" class="form-control form-control-sm table-field font-weight-bold" type="number" value="0" onchange="changeColor(this.id)" onblur="g50g80(1)"></td>
                        <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" id="g80_2" class="form-control form-control-sm table-field font-weight-bold" type="number" value="0" onchange="changeColor(this.id)" onblur="g50g80(2)"></td>
                        <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" id="g80_3" class="form-control form-control-sm table-field font-weight-bold" type="number" value="0" onchange="changeColor(this.id)" onblur="g50g80(3)"></td>
                        <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" id="g80_4" class="form-control form-control-sm table-field font-weight-bold" type="number" value="0" onchange="changeColor(this.id)" onblur="g50g80(4)"></td>
                        <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" id="g80_5" class="form-control form-control-sm table-field font-weight-bold" type="number" value="0" onchange="changeColor(this.id)" onblur="g50g80(5)"></td>
                    </tr>

                        <tr>
                        <th class="text-center" style="font-size: 2.5rem; border-color: white">Knee Low</th>
                        <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" id="kneelow_0" class="form-control form-control-sm table-field font-weight-bold" type="number" value="0" onchange="changeColor(this.id)"></td>
                        <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" id="kneelow_1" class="form-control form-control-sm table-field font-weight-bold" type="number" value="0" onchange="changeColor(this.id)"></td>
                        <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" id="kneelow_2" class="form-control form-control-sm table-field font-weight-bold" type="number" value="0" onchange="changeColor(this.id)"></td>
                        <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" id="kneelow_3" class="form-control form-control-sm table-field font-weight-bold" type="number" value="0" onchange="changeColor(this.id)"></td>
                        <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" id="kneelow_4" class="form-control form-control-sm table-field font-weight-bold" type="number" value="0" onchange="changeColor(this.id)"></td>
                        <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" id="kneelow_5" class="form-control form-control-sm table-field font-weight-bold" type="number" value="0" onchange="changeColor(this.id)"></td>
                    </tr>

                    <tr>
                        <th class="text-center" style="font-size: 2.5rem; border-color: white">Knee High</th>
                        <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" id="kneehigh_0" class="form-control form-control-sm table-field font-weight-bold" type="number" value="0" onchange="changeColor(this.id)"></td>
                        <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" id="kneehigh_1" class="form-control form-control-sm table-field font-weight-bold" type="number" value="0" onchange="changeColor(this.id)"></td>
                        <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" id="kneehigh_2" class="form-control form-control-sm table-field font-weight-bold" type="number" value="0" onchange="changeColor(this.id)"></td>
                        <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" id="kneehigh_3" class="form-control form-control-sm table-field font-weight-bold" type="number" value="0" onchange="changeColor(this.id)"></td>
                        <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" id="kneehigh_4" class="form-control form-control-sm table-field font-weight-bold" type="number" value="0" onchange="changeColor(this.id)"></td>
                        <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" id="kneehigh_5" class="form-control form-control-sm table-field font-weight-bold" type="number" value="0" onchange="changeColor(this.id)"></td>
                    </tr>

                    <tr>
                        <th class="text-center" style="font-size: 2.5rem; border-color: white">Attack</th>
                        <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" id="attack_0" class="form-control form-control-sm table-field font-weight-bold" type="number" value="0" onchange="changeColor(this.id)"></td>
                        <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" id="attack_1" class="form-control form-control-sm table-field font-weight-bold" type="number" value="0" onchange="changeColor(this.id)"></td>
                        <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" id="attack_2" class="form-control form-control-sm table-field font-weight-bold" type="number" value="0" onchange="changeColor(this.id)"></td>
                        <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" id="attack_3" class="form-control form-control-sm table-field font-weight-bold" type="number" value="0" onchange="changeColor(this.id)"></td>
                        <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" id="attack_4" class="form-control form-control-sm table-field font-weight-bold" type="number" value="0" onchange="changeColor(this.id)"></td>
                        <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" id="attack_5" class="form-control form-control-sm table-field font-weight-bold" type="number" value="0" onchange="changeColor(this.id)"></td>
                    </tr>

                    <tr>
                        <th class="text-center" style="font-size: 2.5rem; border-color: white">Release</th>
                        <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" id="release_0" class="form-control form-control-sm table-field font-weight-bold" type="number" value="0" onchange="changeColor(this.id)"></td>
                        <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" id="release_1" class="form-control form-control-sm table-field font-weight-bold" type="number" value="0" onchange="changeColor(this.id)"></td>
                        <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" id="release_2" class="form-control form-control-sm table-field font-weight-bold" type="number" value="0" onchange="changeColor(this.id)"></td>
                        <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" id="release_3" class="form-control form-control-sm table-field font-weight-bold" type="number" value="0" onchange="changeColor(this.id)"></td>
                        <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" id="release_4" class="form-control form-control-sm table-field font-weight-bold" type="number" value="0" onchange="changeColor(this.id)"></td>
                        <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" id="release_5" class="form-control form-control-sm table-field font-weight-bold" type="number" value="0" onchange="changeColor(this.id)"></td>
                    </tr>
                </tbody>

            </table>
        </div>
        <div class="container">
            <div class="row">
            <div class=col>
                <div class="alert alert-danger" role="alert" id="not_connected_alert" style="visibility: hidden">
                    <strong>Error!</strong> Looks like you're not connected to a hearing aid.
                </div>
            </div>
            <div class="col-md-3">
                <button type="button" class="btn btn-primary btn-secondary float-right" style="font-size: 2rem" onclick="resetParams()">Reset</button>
            </div>
            <div class="col-md-3">
                <button type="button" class="btn btn-primary btn-secondary" style="font-size: 2rem;" onclick="submitParams()">Transmit</button>
            </div>
            </div>
        </div>
</div>
<!--         <div class="row">
            <div class="alert alert-danger" role="alert" id="not_connected_alert" style="visibility: hidden">
                <strong>Error!</strong> Looks like you're not connected to a hearing aid.
            </div>
        </div>
 -->
</div>
</div>

 <script src="https://code.jquery.com/jquery-3.1.1.min.js"></script>
 <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>


</body>

</html>

<script type='text/javascript'>

    var cr = [0,0,0,0,0,0];
    var g50 = [0,0,0,0,0,0];
    var g65 = [0,0,0,0,0,0];
    var g80 = [0,0,0,0,0,0];
    var kneehigh = [0,0,0,0,0,0];
    var kneelow = [0,0,0,0,0,0];
    var attack = [0,0,0,0,0,0];
    var release = [0,0,0,0,0,0];

$('input').click(function () {
    this.select();
});

  function changeColor(id){
    document.getElementById(id).style.border = "solid #f7e656";
  }

  function allcrg65(){
    console.log("cr/g65");
    var i;
    for(i = 0; i < 6; i++){
        crg65(i);
    }
  }

  function allg50g80(){
    console.log("g50/g80");
    var i;
    for(i = 0; i < 6; i++){
        g50g80(i);
    }
  }

    function crg65(i){
      if(document.getElementById('radio2').checked) {
      switch(i){
        case 0:
          if(document.getElementById('cr_0').value != 0){
              var cr = parseInt(document.getElementById('cr_0').value);
              var g65 = parseInt(document.getElementById('g65_0').value);
              var slope = (1 - cr)/(cr);
              document.getElementById('g50_0').value = (g65 - (slope * 15)).toFixed((g65 - (slope * 15)) % 1 && 2);
              document.getElementById('g80_0').value = (g65 + (slope * 15)).toFixed((g65 + (slope * 15)) % 1 && 2);
          }

          console.log(cr,g50,g65,g80);
          break;
        case 1:
           if(document.getElementById('cr_1').value != 0){
              var cr = parseInt(document.getElementById('cr_1').value);
              var g65 = parseInt(document.getElementById('g65_1').value);
              var slope = (1 - cr)/(cr);
              document.getElementById('g50_1').value = (g65 - (slope * 15)).toFixed((g65 - (slope * 15)) % 1 && 2);
              document.getElementById('g80_1').value = (g65 + (slope * 15)).toFixed((g65 + (slope * 15)) % 1 && 2); 
            }  

          console.log(cr,g50,g65,g80);
          break;
        case 2:
          if(document.getElementById('cr_2').value != 0){
            var cr = parseInt(document.getElementById('cr_2').value);
            var g65 = parseInt(document.getElementById('g65_2').value);
            var slope = (1 - cr)/(cr);
            document.getElementById('g50_2').value = (g65 - (slope * 15)).toFixed((g65 - (slope * 15)) % 1 && 2);
            document.getElementById('g80_2').value = (g65 + (slope * 15)).toFixed((g65 + (slope * 15)) % 1 && 2);
          }

          console.log(cr,g50,g65,g80);
          break;
        case 3:
          if(document.getElementById('cr_3').value != 0){
            var cr = parseInt(document.getElementById('cr_3').value);
            var g65 = parseInt(document.getElementById('g65_3').value);
            var slope = (1 - cr)/(cr);
            document.getElementById('g50_3').value = (g65 - (slope * 15)).toFixed((g65 - (slope * 15)) % 1 && 2);
            document.getElementById('g80_3').value = (g65 + (slope * 15)).toFixed((g65 + (slope * 15)) % 1 && 2);
          }

          console.log(cr,g50,g65,g80);
          break;
        case 4: 
          if(document.getElementById('cr_4').value != 0){
            var cr = parseInt(document.getElementById('cr_4').value);
            var g65 = parseInt(document.getElementById('g65_4').value);
            var slope = (1 - cr)/(cr);
            document.getElementById('g50_4').value = (g65 - (slope * 15)).toFixed((g65 - (slope * 15)) % 1 && 2);
            document.getElementById('g80_4').value = (g65 + (slope * 15)).toFixed((g65 + (slope * 15)) % 1 && 2);
        }

          console.log(cr,g50,g65,g80);
          break;
        case 5:
          if(document.getElementById('cr_5').value != 0){
            var cr = parseInt(document.getElementById('cr_5').value);
            var g65 = parseInt(document.getElementById('g65_5').value);
            var slope = (1 - cr)/(cr);
            document.getElementById('g50_5').value = (g65 - (slope * 15)).toFixed((g65 - (slope * 15)) % 1 && 2);
            document.getElementById('g80_5').value = (g65 + (slope * 15)).toFixed((g65 + (slope * 15)) % 1 && 2);
          }

          console.log(cr,g50,g65,g80);
          break;
      }
      }
    }


    function g50g80(i){
      if(document.getElementById('radio1').checked) {
      switch(i){
        case 0:
          var g50 = parseInt(document.getElementById('g50_0').value);
          var g80 = parseInt(document.getElementById('g80_0').value);
          var slope = ((g80 - g50)/30);

          document.getElementById('g65_0').value = (g50 + (slope * 15)).toFixed((g50 + (slope * 15)) % 1 && 2);
          if(slope != -1){
            document.getElementById('cr_0').value = (1 / (1 + slope)).toFixed((1 / (1 + slope)) % 1 && 2);
          }

          console.log(cr,g50,g65,g80);
          break;
        case 1:
          var g50 = parseInt(document.getElementById('g50_1').value);
          var g80 = parseInt(document.getElementById('g80_1').value);
          var slope = ((g80 - g50)/30);

          document.getElementById('g65_1').value = (g50 + (slope * 15)).toFixed((g50 + (slope * 15)) % 1 && 2);
          if(slope != -1){
            document.getElementById('cr_1').value = (1 / (1 + slope)).toFixed((1 / (1 + slope)) % 1 && 2);
          }

          console.log(cr,g50,g65,g80);
          break;
        case 2:
          var g50 = parseInt(document.getElementById('g50_2').value);
          var g80 = parseInt(document.getElementById('g80_2').value);
          var slope = ((g80 - g50)/30);

          document.getElementById('g65_2').value = (g50 + (slope * 15)).toFixed((g50 + (slope * 15)) % 1 && 2);
          if(slope != -1){
            document.getElementById('cr_2').value = (1 / (1 + slope)).toFixed((1 / (1 + slope)) % 1 && 2);
          }

          console.log(cr,g50,g65,g80);
          break;
        case 3:
          var g50 = parseInt(document.getElementById('g50_3').value);
          var g80 = parseInt(document.getElementById('g80_3').value);
          var slope = ((g80 - g50)/30);
  
          document.getElementById('g65_3').value = (g50 + (slope * 15)).toFixed((g50 + (slope * 15)) % 1 && 2);
          if(slope != -1){
            document.getElementById('cr_3').value = (1 / (1 + slope)).toFixed((1 / (1 + slope)) % 1 && 2);
          }

          console.log(cr,g50,g65,g80);
          break;
        case 4: 
          var g50 = parseInt(document.getElementById('g50_4').value);
          var g80 = parseInt(document.getElementById('g80_4').value);
          var slope = ((g80 - g50)/30);

          document.getElementById('g65_4').value = (g50 + (slope * 15)).toFixed((g50 + (slope * 15)) % 1 && 2);
          if(slope != -1){
            document.getElementById('cr_4').value = (1 / (1 + slope)).toFixed((1 / (1 + slope)) % 1 && 2);
          }

          console.log(cr,g50,g65,g80);
          break;
        case 5:
          var g50 = parseInt(document.getElementById('g50_5').value);
          var g80 = parseInt(document.getElementById('g80_5').value);
          var slope = ((g80 - g50)/30);

          document.getElementById('g65_5').value = (g50 + (slope * 15)).toFixed((g50 + (slope * 15)) % 1 && 2);
          if(slope != -1){
            document.getElementById('cr_5').value = (1 / (1 + slope)).toFixed((1 / (1 + slope)) % 1 && 2);
          }

          console.log(cr,g50,g65,g80);
          break;
      }
      }

    }

function resetParams(){
        document.getElementById("cr_0").value = this.cr[0];
        document.getElementById("cr_1").value = this.cr[1];
        document.getElementById("cr_2").value = this.cr[2];
        document.getElementById("cr_3").value = this.cr[3];
        document.getElementById("cr_4").value = this.cr[4];
        document.getElementById("cr_5").value = this.cr[5];

        document.getElementById("g50_0").value = this.g50[0];
        document.getElementById("g50_1").value = this.g50[1];
        document.getElementById("g50_2").value = this.g50[2];
        document.getElementById("g50_3").value = this.g50[3];
        document.getElementById("g50_4").value = this.g50[4];
        document.getElementById("g50_5").value = this.g50[5];

        document.getElementById("g65_0").value = this.g65[0];
        document.getElementById("g65_1").value = this.g65[1];
        document.getElementById("g65_2").value = this.g65[2];
        document.getElementById("g65_3").value = this.g65[3];
        document.getElementById("g65_4").value = this.g65[4];
        document.getElementById("g65_5").value = this.g65[5];

        document.getElementById("g80_0").value = this.g80[0];
        document.getElementById("g80_1").value = this.g80[1];
        document.getElementById("g80_2").value = this.g80[2];
        document.getElementById("g80_3").value = this.g80[3];
        document.getElementById("g80_4").value = this.g80[4];
        document.getElementById("g80_5").value = this.g80[5];

        document.getElementById("kneelow_0").value = this.kneelow[0];
        document.getElementById("kneelow_1").value = this.kneelow[1];
        document.getElementById("kneelow_2").value = this.kneelow[2];
        document.getElementById("kneelow_3").value = this.kneelow[3];
        document.getElementById("kneelow_4").value = this.kneelow[4];
        document.getElementById("kneelow_5").value = this.kneelow[5];

        document.getElementById("kneehigh_0").value = this.kneehigh[0];
        document.getElementById("kneehigh_1").value = this.kneehigh[1];
        document.getElementById("kneehigh_2").value = this.kneehigh[2];
        document.getElementById("kneehigh_3").value = this.kneehigh[3];
        document.getElementById("kneehigh_4").value = this.kneehigh[4];
        document.getElementById("kneehigh_5").value = this.kneehigh[5];

        document.getElementById("attack_0").value = this.attack[0];
        document.getElementById("attack_1").value = this.attack[1];
        document.getElementById("attack_2").value = this.attack[2];
        document.getElementById("attack_3").value = this.attack[3];
        document.getElementById("attack_4").value = this.attack[4];
        document.getElementById("attack_5").value = this.attack[5];

        document.getElementById("release_0").value = this.release[0];
        document.getElementById("release_1").value = this.release[1];
        document.getElementById("release_2").value = this.release[2];
        document.getElementById("release_3").value = this.release[3];
        document.getElementById("release_4").value = this.release[4];
        document.getElementById("release_5").value = this.release[5];

        document.getElementById("cr_0").style.removeProperty('border');
        document.getElementById("cr_1").style.removeProperty('border');
        document.getElementById("cr_2").style.removeProperty('border');
        document.getElementById("cr_3").style.removeProperty('border');
        document.getElementById("cr_4").style.removeProperty('border');
        document.getElementById("cr_5").style.removeProperty('border');

        document.getElementById("g50_0").style.removeProperty('border');
        document.getElementById("g50_1").style.removeProperty('border');
        document.getElementById("g50_2").style.removeProperty('border');
        document.getElementById("g50_3").style.removeProperty('border');
        document.getElementById("g50_4").style.removeProperty('border');
        document.getElementById("g50_5").style.removeProperty('border');

        document.getElementById("g65_0").style.removeProperty('border');
        document.getElementById("g65_1").style.removeProperty('border');
        document.getElementById("g65_2").style.removeProperty('border');
        document.getElementById("g65_3").style.removeProperty('border');
        document.getElementById("g65_4").style.removeProperty('border');
        document.getElementById("g65_5").style.removeProperty('border');

        document.getElementById("g80_0").style.removeProperty('border');
        document.getElementById("g80_1").style.removeProperty('border');
        document.getElementById("g80_2").style.removeProperty('border');
        document.getElementById("g80_3").style.removeProperty('border');
        document.getElementById("g80_4").style.removeProperty('border');
        document.getElementById("g80_5").style.removeProperty('border');

        document.getElementById("kneelow_0").style.removeProperty('border');
        document.getElementById("kneelow_1").style.removeProperty('border');
        document.getElementById("kneelow_2").style.removeProperty('border');
        document.getElementById("kneelow_3").style.removeProperty('border');
        document.getElementById("kneelow_4").style.removeProperty('border');
        document.getElementById("kneelow_5").style.removeProperty('border');

        document.getElementById("kneehigh_0").style.removeProperty('border');
        document.getElementById("kneehigh_1").style.removeProperty('border');
        document.getElementById("kneehigh_2").style.removeProperty('border');
        document.getElementById("kneehigh_3").style.removeProperty('border');
        document.getElementById("kneehigh_4").style.removeProperty('border');
        document.getElementById("kneehigh_5").style.removeProperty('border');

        document.getElementById("attack_0").style.removeProperty('border');
        document.getElementById("attack_1").style.removeProperty('border');
        document.getElementById("attack_2").style.removeProperty('border');
        document.getElementById("attack_3").style.removeProperty('border');
        document.getElementById("attack_4").style.removeProperty('border');
        document.getElementById("attack_5").style.removeProperty('border');

        document.getElementById("release_0").style.removeProperty('border');
        document.getElementById("release_1").style.removeProperty('border');
        document.getElementById("release_2").style.removeProperty('border');
        document.getElementById("release_3").style.removeProperty('border');
        document.getElementById("release_4").style.removeProperty('border');
        document.getElementById("release_5").style.removeProperty('border');

}


    function submitParams(){

        document.getElementById("not_connected_alert").style.visibility = "hidden";


        this.cr[0] = document.getElementById("cr_0").value;
        this.cr[1] = document.getElementById("cr_1").value;
        this.cr[2] = document.getElementById("cr_2").value;
        this.cr[3] = document.getElementById("cr_3").value;
        this.cr[4] = document.getElementById("cr_4").value;
        this.cr[5] = document.getElementById("cr_5").value;

        this.g50[0] = document.getElementById("g50_0").value;
        this.g50[1] = document.getElementById("g50_1").value;
        this.g50[2] = document.getElementById("g50_2").value;
        this.g50[3] = document.getElementById("g50_3").value;
        this.g50[4] = document.getElementById("g50_4").value;
        this.g50[5] = document.getElementById("g50_5").value;

        this.g65[0] = document.getElementById("g65_0").value;
        this.g65[1] = document.getElementById("g65_1").value;
        this.g65[2] = document.getElementById("g65_2").value;
        this.g65[3] = document.getElementById("g65_3").value;
        this.g65[4] = document.getElementById("g65_4").value;
        this.g65[5] = document.getElementById("g65_5").value;

        this.g80[0] = document.getElementById("g80_0").value;
        this.g80[1] = document.getElementById("g80_1").value;
        this.g80[2] = document.getElementById("g80_2").value;
        this.g80[3] = document.getElementById("g80_3").value;
        this.g80[4] = document.getElementById("g80_4").value;
        this.g80[5] = document.getElementById("g80_5").value;

        this.kneelow[0] = document.getElementById("kneelow_0").value;
        this.kneelow[1] = document.getElementById("kneelow_1").value;
        this.kneelow[2] = document.getElementById("kneelow_2").value;
        this.kneelow[3] = document.getElementById("kneelow_3").value;
        this.kneelow[4] = document.getElementById("kneelow_4").value;
        this.kneelow[5] = document.getElementById("kneelow_5").value;

        this.kneehigh[0] = document.getElementById("kneehigh_0").value;
        this.kneehigh[1] = document.getElementById("kneehigh_1").value;
        this.kneehigh[2] = document.getElementById("kneehigh_2").value;
        this.kneehigh[3] = document.getElementById("kneehigh_3").value;
        this.kneehigh[4] = document.getElementById("kneehigh_4").value;
        this.kneehigh[5] = document.getElementById("kneehigh_5").value;

        this.attack[0] = document.getElementById("attack_0").value;
        this.attack[1] = document.getElementById("attack_1").value;
        this.attack[2] = document.getElementById("attack_2").value;
        this.attack[3] = document.getElementById("attack_3").value;
        this.attack[4] = document.getElementById("attack_4").value;
        this.attack[5] = document.getElementById("attack_5").value;

        this.release[0] = document.getElementById("release_0").value;
        this.release[1] = document.getElementById("release_1").value;
        this.release[2] = document.getElementById("release_2").value;
        this.release[3] = document.getElementById("release_3").value;
        this.release[4] = document.getElementById("release_4").value;
        this.release[5] = document.getElementById("release_5").value;

        $.ajax({
            method: 'POST', // Type of response and matches what we said in the route
            url: '/researcherpage', // This is the url we gave in the route
            data: {
                "_token": "{{ csrf_token() }}",
                'noOp': 0,
                'afc': 1,
                'feedback': 1,
                'rear': 1,
                'g50': this.g50,
                'g80': this.g80,
                'kneelow': this.kneelow,
                'mpoLimit': this.kneehigh,
                'attackTime': this.attack,
                'releaseTime': this.release
            }, // a JSON object to send back
            success: function(response){ // What to do if we succeed
                console.log(response);
                    document.getElementById("cr_0").style.removeProperty('border');
                    document.getElementById("cr_1").style.removeProperty('border');
                    document.getElementById("cr_2").style.removeProperty('border');
                    document.getElementById("cr_3").style.removeProperty('border');
                    document.getElementById("cr_4").style.removeProperty('border');
                    document.getElementById("cr_5").style.removeProperty('border');

                    document.getElementById("g50_0").style.removeProperty('border');
                    document.getElementById("g50_1").style.removeProperty('border');
                    document.getElementById("g50_2").style.removeProperty('border');
                    document.getElementById("g50_3").style.removeProperty('border');
                    document.getElementById("g50_4").style.removeProperty('border');
                    document.getElementById("g50_5").style.removeProperty('border');

                    document.getElementById("g65_0").style.removeProperty('border');
                    document.getElementById("g65_1").style.removeProperty('border');
                    document.getElementById("g65_2").style.removeProperty('border');
                    document.getElementById("g65_3").style.removeProperty('border');
                    document.getElementById("g65_4").style.removeProperty('border');
                    document.getElementById("g65_5").style.removeProperty('border');

                    document.getElementById("g80_0").style.removeProperty('border');
                    document.getElementById("g80_1").style.removeProperty('border');
                    document.getElementById("g80_2").style.removeProperty('border');
                    document.getElementById("g80_3").style.removeProperty('border');
                    document.getElementById("g80_4").style.removeProperty('border');
                    document.getElementById("g80_5").style.removeProperty('border');

                    document.getElementById("kneelow_0").style.removeProperty('border');
                    document.getElementById("kneelow_1").style.removeProperty('border');
                    document.getElementById("kneelow_2").style.removeProperty('border');
                    document.getElementById("kneelow_3").style.removeProperty('border');
                    document.getElementById("kneelow_4").style.removeProperty('border');
                    document.getElementById("kneelow_5").style.removeProperty('border');

                    document.getElementById("kneehigh_0").style.removeProperty('border');
                    document.getElementById("kneehigh_1").style.removeProperty('border');
                    document.getElementById("kneehigh_2").style.removeProperty('border');
                    document.getElementById("kneehigh_3").style.removeProperty('border');
                    document.getElementById("kneehigh_4").style.removeProperty('border');
                    document.getElementById("kneehigh_5").style.removeProperty('border');

                    document.getElementById("attack_0").style.removeProperty('border');
                    document.getElementById("attack_1").style.removeProperty('border');
                    document.getElementById("attack_2").style.removeProperty('border');
                    document.getElementById("attack_3").style.removeProperty('border');
                    document.getElementById("attack_4").style.removeProperty('border');
                    document.getElementById("attack_5").style.removeProperty('border');

                    document.getElementById("release_0").style.removeProperty('border');
                    document.getElementById("release_1").style.removeProperty('border');
                    document.getElementById("release_2").style.removeProperty('border');
                    document.getElementById("release_3").style.removeProperty('border');
                    document.getElementById("release_4").style.removeProperty('border');
                    document.getElementById("release_5").style.removeProperty('border');

            },
            error: function(jqXHR, textStatus, errorThrown) { // What to do if we fail
                console.log(JSON.stringify(jqXHR));
                console.log("AJAX error: " + textStatus + ' : ' + errorThrown);
                document.getElementById("not_connected_alert").style.visibility = "visible";
            }
        }); 

    }


</script>