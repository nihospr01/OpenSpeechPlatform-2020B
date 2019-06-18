<!DOCTYPE html>
<html lang="en">

<head>

  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
  <script type="text/javascript" src="{{ asset('js/jquery-3.3.1.min.js') }}"></script>
  <script type="text/javascript" src="{{ asset('js/popper.min.js')}}"></script>
  <script type="text/javascript" src="{{ asset('js/bootstrap.min.js')}}"></script>
  <script type="text/javascript">
    $(document).ready(function(){

        //Convert 0-255 to -95.625-0
        function adcConvert(x){
          var res =  -95.625 + 0.375*x;
          return Number.parseFloat(res).toFixed(3);
        }

        //Conver 0-64 to -12-35.25
        function pgaConvert(x){
          var res = -12+0.75*x;
          return Number.parseFloat(res).toFixed(2);
        }

        var parameters  = {
          'left':{
            'pga_gain': 0,
            'adc_vol':0,
            'dac_vol':0,
          },
          'right':{
            'pga_gain': 0,
            'adc_vol':0,
            'dac_vol':0,
          }
        };

        // $("#adc_label").text("ADC Volume:" + Number.parseInt(parameters['left']['adc_vol']));

        $("#adc_vol").on('input',function(event){
            var res = Number(adcConvert($(this).val()));
            parameters['left']['adc_vol'] = res;
            parameters['right']['adc_vol'] = res;
            $("#adc_label").text("ADC Volume:" + res);
        });

        $("#dac_vol").on('input',function(event){
            var res = Number(adcConvert($(this).val()));
            parameters['left']['dac_vol'] = res;
            parameters['right']['dac_vol'] = res;
            $("#dac_label").text("DAC Volume:" + res);

        });

        $("#pga_gain").on('input',function(event){
            var res = Number(pgaConvert($(this).val()));
            console.log(res)
            parameters['left']['pga_gain'] = res;
            parameters['right']['pga_gain'] = res;
            $("#pga_label").text("PGA Gain:" + res);
        });

        //pass in parameters
        $("#run_ansi").on('click',function(event){
          event.preventDefault();
          $.ajax({
            method: 'POST',
            url:'/api/params',
            data:JSON.stringify({
              user_id: -1,
              method: "set",
              request_action:1,
              data:{
                left:{
                  pga_gain: parameters['left']['pga_gain'],
                  adc_vol:  parameters['left']['adc_vol'],
                  dac_vol:  parameters['left']['dac_vol']
                },
                right:{
                  pga_gain: parameters['right']['pga_gain'],
                  adc_vol:  parameters['right']['adc_vol'],
                  dac_vol:  parameters['right']['dac_vol']
                }
              }
            }),
            success: function(response){
              alert(response);
            },
            error: function(jqXHR, textStatus,errorThrown){
              alert("Parameters were not sent to the MHA");
            }
          });
        });

    });

  </script>

  <style>
    .container{
      width: 100%;
      justify-content:center;
      margin-top:30px;
    }
  </style>
</head>


<body>
  <div class="container">

        <h1>Pass Through Mode</h1>
        <h5>1. When using this app, please make sure that you are running OSP in pass through mode</h5>
        <h5>2. Connect the receiver </h5>
        <h5>3. You will see the ANSI 3.22 outputs in a device like verifit </h5>
        <h5> *Please note that the same values will be passed to both left and right channels.* </h5>
        <form style="max-width:600px">
          <div class="form-group" >
            <label for="adc_vol" id="adc_label">ADC Volume:</label>
            <input type="range" id="adc_vol" class="custom-range" min="0" max = "255">
            <div class="row" style="margin-bottom:25px">
              <div class="col" style="text-align:left">
                <small class="form-text text-muted">-95.625dB</small>
              </div>
              <div class="col" style="text-align:right">
                <small class="form-text text-muted">0dB</small>
              </div>
            </div>

            <label for="dac_vol" id="dac_label">DAC Volume:</label>
            <input type="range" id="dac_vol" class="custom-range" min="0" max = "255">
            <div class="row" style="margin-bottom:25px">
              <div class="col" style="text-align:left">
                <small class="form-text text-muted">-95.625dB</small>
              </div>
              <div class="col" style="text-align:right">
                <small class="form-text text-muted">0dB</small>
              </div>
            </div>

            <label for="pga_gain" id="pga_label">PGA Gain:</label>
            <input type="range" id="pga_gain" class="custom-range" min="0" max = "63">
            <div class="row">
              <div class="col" style="text-align:left">
                <small class="form-text text-muted">-12db</small>
              </div>
              <div class="col" style="text-align:right">
                <small class="form-text text-muted">35.25db</small>
              </div>
            </div>


          </div>

          <div class="row">
          <div class="col" style="text-align: left">
            <button type="button" class="btn btn-info" id="run_ansi">RUN ANSI</button>
          </div>
        <div class="col" style="text-align:right">
          <button type="button" class="btn btn-info" id="save_values">Save Values</button>
        </div>
    </div>
        </form>

      </div>

  
    <img src="{{ asset('passThrough.png') }}" style="width:80%;">
  
    </div>








</body>
