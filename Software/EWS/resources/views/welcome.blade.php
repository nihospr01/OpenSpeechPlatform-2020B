<!doctype html>
<!--This is the syntax for making a comment in HTML-->
<!--Follow my comments for information about different components of the code -->
<html lang="{{ app()->getLocale() }}">
    <head>
      <!--Lines 7-9 makes it so that the interface works on devices of all sizes, and scales accordingly.-->
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <!--Lines 11-14 are accessing bootstrap, jquery, and other javascript elements from the asset folder for use in this interface.-->
        <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
        <script type="text/javascript" src="{{ asset('js/jquery-3.3.1.min.js') }}"></script>
        <script type="text/javascript" src="{{ asset('js/popper.min.js')}}"></script>
        <script type="text/javascript" src="{{ asset('js/bootstrap.min.js')}}"></script>
<!--The title element is what will display in the heading of your browser bar next to the page icon.-->
        <title>OSP</title>


      </head>


    <body>
      <!--This is some Javascript that checks whether or not this page needs to be logged into. It does not,
      because it is the home page. If it was another page, then this piece of code would redirect the user to the home page or to the login. -->
        <div class="flex-center position-ref full-height ">
            @if (Route::has('login'))
                <div class="top-right links">
                    @if (Auth::check())
                        <a href="{{ url('/home') }}">Home</a>
                    @else
                        <a href="{{ url('/login') }}">Login</a>
                        <a href="{{ url('/register') }}">Register</a>
                    @endif
                </div>
            @endif

            <!--A container is a bootstrap element, which serves as a sort of wrapper for all of the items and
          content that are within its div. As you can see, the end div for this containter is almost at the end
          of the page, so it is responsible for holding all of the elements. -->
          <!--The container-fluid class is a part of bootstrap, a styling api you can access by looking it up on google-->
          <!--If you are styling yourself, you can add styling directly to the div like this example.
                If you style a div outside of other divs, the styling will carry over, such as the padding here.-->
            <div class="container-fluid" style="padding:10px;">
              <!--h3 is a bootstrap heading class that makes the heading appear as large text on the page-->
                <h3>
                <!--This is the text that appears in the heading-->
                The Open Speech Platform
                <!--text-muted is a bootstrap class that makes the heading appear as large text on the page-->
                <small class="text-muted"> Webapps for research. </small>
                </h3>

              <div class="row">
                <div class="col col-md-4">

                  <div class="card" style="width: 23rem; height: 23rem; padding:10px;">
                    <h5 class="card-header">Researcher Page</h5>
                    <div class="card-body">
                      <h6 class="card-subtitle mb-2 text-muted">Includes amplification, noise and feedback parameters.</h6>
                      <p class="card-text">You can use this page to send parameters directly to the real-time master hearing aid, and to save or load in parameters you have created before.</p>
                      <a href="{{ url('/researcher/amplification') }}" class="btn btn-primary">Go to the Researcher Page</a>
                    </div>
                  </div>

            <p>
            </p>

                <div class="card" style="width: 23rem; height: 23rem; padding:10px;">
                  <h5 class="card-header">Ecological Momentary Assessment (EMA) </h5>
                  <div class="card-body">
                    <h6 class="card-subtitle mb-2 text-muted"> Includes an EMA webapp, using which an end user can respond to a prompted question or set of questions. </h6>
                    <p class="card-text">You can use this page to send the end user questions about his/her experience in real time, and the answers are recorded to a mySQL database.</p>
                    <a href="{{ url('/researcher/amplification') }}" class="btn btn-primary">In Progress!</a>
                  </div>
              </div>
            <p>
            </p>
          </div>

          <div class="col col-md-4">
              <div class="card" style="width: 23rem; height: 23rem; padding:10px;">
                <h5 class="card-header">4 Alternate Forced Choice (4AFC) Task</h5>
                  <div class="card-body">
                    <h6 class="card-subtitle mb-2 text-muted">Includes a 4AFC Task webapp in which an end user can play a sound on click and select a response from 4 options. .</h6>
                    <p class="card-text">You can use this page to run  multiple choice option experiments in response to a sound file.</p>
                    <a href="{{ url('/4afc') }}" class="btn btn-primary">Go to 4AFC</a>
                </div>
              </div>
              <p>
              </p>

              <div class="card" style="width: 23rem; height: 23rem; padding:10px;">
                <h5 class="card-header">AB Task</h5>
                <div class="card-body">
                  <h6 class="card-subtitle mb-2 text-muted">Includes an AB Task webapp, using which an end user can select a relationship between two presented stimuli, A and B, evaluated on a 7 point likert scale.  </h6>
                  <p class="card-text">You can use this page to run an AB task comparing anything from different sound files, to different sets of RT-MHA parameters. </p>
                  <a href="{{ url('/researcher/amplification') }}" class="btn btn-primary">In Progress!</a>
                </div>
              </div>
          </div>

<p>
</p>

          <div class="col col-md-4">
              <div class="card" style="width: 23rem; height: 23rem; padding:10px;">
                <h5 class="card-header">Goldilocks</h5>
                <div class="card-body">
                  <h6 class="card-subtitle mb-2 text-muted">Includes researcher interface and end user interface</h6>
                  <p class="card-text">You can use the researcher interface to set up a Goldilocks self-fitting session, and an end user can use the end user interface to complete your specified session.</p>
                  <a href="{{ url('/goldilocks') }}" class="btn btn-primary">Go to Goldilocks</a>

              </div>
            </div>

            <p>
            </p>
            <div class="card" style="width: 23rem; height: 23rem; padding:10px;">
              <h5 class="card-header">Learn More</h5>
                <div class="card-body">
                  <h6 class="card-subtitle mb-2 text-muted">This is a link to the Open Speech Platform website!</h6>
                  <p class="card-text">Here, you can find the latest information on code releases, publications, and more!</p>
                  <a href="{{ url('http://openspeechplatform.ucsd.edu') }}" class="btn btn-primary">Go to our website</a>
              </div>
            </div>
        </div>

      </div>
    </div>
  </div>



    </body>
</html>
