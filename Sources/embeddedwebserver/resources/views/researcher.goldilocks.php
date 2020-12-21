<!DOCTYPE html>
<html lang="en">

<head>

  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="../../public/css/bootstrap.min.css">
  <link rel="stylesheet" href="../../public/css/login.css">
  <script src="jquery-3.3.1.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.0/umd/popper.min.js"></script>
  <script src="../../public/js/bootstrap.min.js"></script>
</head>

<body>

  <div class="row">
    <div class="col">
      <p class="text-center h10">GOLDILOCKS</p>
    </div>
  </div>

  <div class="container-fluid">
    <div class="row">
      <div class="col-sm-8">
        <p></p>
        <form class="form-horizontal">
          <div class="form-inline">
            <label for="listenerid" class="h7">Listener ID</label>
            <div class="col-sm-8">
              <input type="text" class="text_1" id="listenerid" placeholder="Listener ID">
            </div>
          </div>
        </div>
          <p></p>
                   <div class="form-inline">
                     <label for="testerid" class="h7">Tester ID</label>
                     <div class="col-sm-8">
                       <input type="text" class="text_1" id="testerid" placeholder="Tester ID">
                     </div>
                   </div>
                 </form>

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
                       <th class="text-center" style="font-size: 2.5rem; border-color: white">Assumed Speech Level</th>
                       <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" id="asl_0" class="form-control form-control-sm table-field font-weight-bold" type="number" value="0" onchange="changeColor(this.id)" onblur="crgasl(0)"></td>
                       <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" id="asl_1" class="form-control form-control-sm table-field font-weight-bold" type="number" value="0" onchange="changeColor(this.id)" onblur="crgasl(1)"></td>
                       <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" id="asl_2" class="form-control form-control-sm table-field font-weight-bold" type="number" value="0" onchange="changeColor(this.id)" onblur="crgasl(2)"></td>
                       <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" id="asl_3" class="form-control form-control-sm table-field font-weight-bold" type="number" value="0" onchange="changeColor(this.id)" onblur="crgasl(3)"></td>
                       <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" id="asl_4" class="form-control form-control-sm table-field font-weight-bold" type="number" value="0" onchange="changeColor(this.id)" onblur="crgasl(4)"></td>
                       <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" id="asl_5" class="form-control form-control-sm table-field font-weight-bold" type="number" value="0" onchange="changeColor(this.id)" onblur="crgasl(5)"></td>
                   </tr>
                   <tr>
                       <th class="text-center" style="font-size: 2.5rem; border-color: white">Listener Thresholds</th>
                       <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" id="lt_0" class="form-control form-control-sm table-field font-weight-bold" type="number" value="0" onchange="changeColor(this.id)" onblur="crg65(0)"></td>
                       <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" id="lt_1" class="form-control form-control-sm table-field font-weight-bold" type="number" value="0" onchange="changeColor(this.id)" onblur="crg65(1)"></td>
                       <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" id="lt_2" class="form-control form-control-sm table-field font-weight-bold" type="number" value="0" onchange="changeColor(this.id)" onblur="crg65(2)"></td>
                       <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" id="lt_3" class="form-control form-control-sm table-field font-weight-bold" type="number" value="0" onchange="changeColor(this.id)" onblur="crg65(3)"></td>
                       <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" id="lt_4" class="form-control form-control-sm table-field font-weight-bold" type="number" value="0" onchange="changeColor(this.id)" onblur="crg65(4)"></td>
                       <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" id="lt_5" class="form-control form-control-sm table-field font-weight-bold" type="number" value="0" onchange="changeColor(this.id)" onblur="crg65(5)"></td>
                   </tr>
                   <tr>
                       <th class="text-center" style="font-size: 2.5rem; border-color: white">Targets</th>
                       <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" id="targets_0" class="form-control form-control-sm table-field font-weight-bold" type="number" value="0" onchange="changeColor(this.id)" onblur="crg65(0)"></td>
                       <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" id="targets_1" class="form-control form-control-sm table-field font-weight-bold" type="number" value="0" onchange="changeColor(this.id)" onblur="crg65(1)"></td>
                       <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" id="targets_2" class="form-control form-control-sm table-field font-weight-bold" type="number" value="0" onchange="changeColor(this.id)" onblur="crg65(2)"></td>
                       <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" id="targets_3" class="form-control form-control-sm table-field font-weight-bold" type="number" value="0" onchange="changeColor(this.id)" onblur="crg65(3)"></td>
                       <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" id="targets_4" class="form-control form-control-sm table-field font-weight-bold" type="number" value="0" onchange="changeColor(this.id)" onblur="crg65(4)"></td>
                       <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" id="targets_5" class="form-control form-control-sm table-field font-weight-bold" type="number" value="0" onchange="changeColor(this.id)" onblur="crg65(5)"></td>
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
                       <th class="text-center" style="font-size: 2.5rem; border-color: white">Multiplier I</th>
                       <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" id="multi1_0" class="form-control form-control-sm table-field font-weight-bold" type="number" value="0" onchange="changeColor(this.id)" onblur="crg65(0)"></td>
                       <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" id="multi1_1" class="form-control form-control-sm table-field font-weight-bold" type="number" value="0" onchange="changeColor(this.id)" onblur="crg65(1)"></td>
                       <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" id="multi1_2" class="form-control form-control-sm table-field font-weight-bold" type="number" value="0" onchange="changeColor(this.id)" onblur="crg65(2)"></td>
                       <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" id="multi1_3" class="form-control form-control-sm table-field font-weight-bold" type="number" value="0" onchange="changeColor(this.id)" onblur="crg65(3)"></td>
                       <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" id="multi1_4" class="form-control form-control-sm table-field font-weight-bold" type="number" value="0" onchange="changeColor(this.id)" onblur="crg65(4)"></td>
                       <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" id="multi1_5" class="form-control form-control-sm table-field font-weight-bold" type="number" value="0" onchange="changeColor(this.id)" onblur="crg65(5)"></td>
                   </tr>
                   <tr>
                       <th class="text-center" style="font-size: 2.5rem; border-color: white">Multiplier II</th>
                       <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" id="multi2_0" class="form-control form-control-sm table-field font-weight-bold" type="number" value="0" onchange="changeColor(this.id)" onblur="crg65(0)"></td>
                       <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" id="multi2_1" class="form-control form-control-sm table-field font-weight-bold" type="number" value="0" onchange="changeColor(this.id)" onblur="crg65(1)"></td>
                       <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" id="multi2_2" class="form-control form-control-sm table-field font-weight-bold" type="number" value="0" onchange="changeColor(this.id)" onblur="crg65(2)"></td>
                       <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" id="multi2_3" class="form-control form-control-sm table-field font-weight-bold" type="number" value="0" onchange="changeColor(this.id)" onblur="crg65(3)"></td>
                       <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" id="multi2_4" class="form-control form-control-sm table-field font-weight-bold" type="number" value="0" onchange="changeColor(this.id)" onblur="crg65(4)"></td>
                       <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" id="multi2_5" class="form-control form-control-sm table-field font-weight-bold" type="number" value="0" onchange="changeColor(this.id)" onblur="crg65(5)"></td>
                   </tr>
                 </tbody>
               </div>

                 <p></p>
        <form class="form-control">
          <div class="form-horizontal">
            <label for="selectL" class="h7">L</label>
            <div class="col-sm-10">
              <select id="selectL" class="form-control">
                <option>0</option>
                <option>1</option>
                <option>2</option>
              </select>
            </div>
          </div>
          <div class="form-horizontal">
            <label for="selectV" class="h7">V</label>
            <div class="col-sm-10">
              <select id="selectV" class="form-control">
                <option>0</option>
                <option>1</option>
                <option>2</option>
              </select>
            </div>
          </div>
          <div class="form-horizontal">
            <label for="selectH" class="h7">H</label>
            <div class="col-sm-10">
              <select id="selectH" class="form-control">
                <option>0</option>
                <option>1</option>
                <option>2</option>
              </select>
            </div>
          </div>
        <p></p>



        <div class="form-group">
            <label for="inlineRadio1" class="h7">No. of parameters:</label>
            <label class="radio-inline h9">
              <input type="radio" name="inlineRadioOptions1" id="inlineRadio1" value="option1"> 2
            </label>
            <label class="radio-inline h9">
              <input type="radio" name="inlineRadioOptions1" id="inlineRadio1" value="option2"> 3
            </label>
          </div>

          <div class="form-group">
            <label for="inlineRadio2" class="h7">First Parameter:</label>
            <label class="radio-inline h9">
              <input type="radio" name="inlineRadioOptions2" id="inlineRadio2" value="option1"> crispness
            </label>
            <label class="radio-inline h9">
              <input type="radio" name="inlineRadioOptions2" id="inlineRadio2" value="option2"> loudness
            </label>
          </div>

          <div class="form-group">
           <div class="col-sm-offset-6 col-sm-20">
             <button type="submit" class="btn1 btn-info btn-block">SELF ADJUSTMENT</button>
           </div>
         </div>
       </form>
     </div>
</body>
