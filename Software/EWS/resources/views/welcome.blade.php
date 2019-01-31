<!doctype html>
<!--This is the syntax for making a comment in HTML-->
<!--Follow my comments for information about different components of the code -->
<html lang="{{ app()->getLocale() }}">
<!--The head tag denotes the first section of the code, which includes links to important dependencies -->
    <head>
      <!--The next few lines make it so that the interface works on devices of all sizes, and scales accordingly.-->
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

<!-- The body tag, below, is the start of the body section of the page, and includes the main skeleton of the user interface -->
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
              <!-- A row is a bootstrap element that you can use to create a UI in bootstrap. Bootstrap has a grid layout,
               so you can switch to a new row with this tag -->
              <!-- Note that you are opening a div on the next line, and be mindful that the div is closed later. -->
              <div class="row">

                <!-- A col is a bootstrap element that you can use to create columns in your grid UI. -->
                <!-- Here we call col and col-md-4. The row above will be divided evenly into the specified number of cols.
                Notie that there are three cols total, corresponding to the three columns on the page -->
                <div class="col col-md-4">

                  <!-- A card is a bootstrap element which can be used to easily drop in an element on a page -->
                  <!-- In this page, each webapp has a card which contains the link to the webapp -->
                  <div class="card" style="width: 23rem; height: 10rem; padding:10px;">
                    <!-- The line below contains the actual link to the researcher page. Notice that the link
                  is the same as the location of the blade.php file for the page in the views folder. Because
                  we are using the Laravel framework, we must denote links using this syntax. -->
                    <a href="{{ url('/researcher/amplification') }}" class= "btn btn-info">Researcher Page</a>
                    <div class="card-body">
                      <h6 class="card-subtitle mb-2 text-muted">Includes amplification, noise and feedback parameters.</h6>
                    <!--The line below contains a closed div. It is very important to close divs that have been opened -->
                    </div>
                  </div>

                        <p>
                        </p>

                  <div class="card" style="width: 23rem; height: 10rem; padding:10px;">
                  <a href="{{ url('/ema') }}" class="btn btn-info">Ecological Momentary Assessment (EMA)</a>
                    <div class="card-body">
                    <h6 class="card-subtitle mb-2 text-muted"> Includes an EMA webapp, using which an end user can respond to a prompted question or set of questions. </h6>
                    </div>
                ` `</div>
                          <p>
                          </p>
              </div>
              <!-- This is the second column of the page, and contains the 4AFC and AB task webapps-->
              <div class="col col-md-4">

                <div class="card" style="width: 23rem; height: 10rem; padding:10px;">
                <a href="{{ url('/4afc') }}" class="btn btn-info">4 Alternate Forced Choice (4AFC) Task</a>
                  <div class="card-body">
                    <h6 class="card-subtitle mb-2 text-muted">Includes a 4AFC Task webapp in which an end user can play a sound on click and select a response from 4 options.</h6>
                  </div>
                </div>
                        <p>
                        </p>

                <div class="card" style="width: 23rem; height: 10rem; padding:10px;">
                <a href="{{ url('/ab') }}" class="btn btn-info">AB Task</a>
                  <div class="card-body">
                  <h6 class="card-subtitle mb-2 text-muted">Includes an AB Task webapp, using which an end user can select a relationship between two presented stimuli, A and B, evaluated on a 7 point likert scale.  </h6>
                  </div>
                </div>
            </div>

                      <p>
                      </p>
          <!--This is the third column of the welcome page, and contains the links to Goldilocks and our website-->
          <!--Notice that we include both col and col-md-4 in all three columns. This is so that the columns change their
            size automatically depending on the size of the device. -->
            <div class="col col-md-4">

              <div class="card" style="width: 23rem; height: 10rem; padding:10px;">
              <a href="{{ url('/goldilocks') }}" class="btn btn-info">Goldilocks</a>
                  <div class="card-body">
                  <h6 class="card-subtitle mb-2 text-muted">Includes researcher interface and end user interface</h6>
                  </div>
              </div>

                  <p>
                  </p>
              <div class="card" style="width: 23rem; height: 10rem; padding:10px;">
              <!--In the line below, you can see how to add a link to an http webpage using the Laravel syntax -->
              <a href="{{ url('http://openspeechplatform.ucsd.edu') }}" class="btn btn-info">Go to our website</a>
                <div class="card-body">
                  <h6 class="card-subtitle mb-2 text-muted">This is a link to the Open Speech Platform website!</h6>
                </div>
              </div>

            </div>

          </div>
        </div>
      </div>

    </body>
</html>
