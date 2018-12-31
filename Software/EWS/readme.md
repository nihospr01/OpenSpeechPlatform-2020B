
# Setting up

## Intalling Laravel
First you must install [composer](https://getcomposer.org/download/) and make sure to have npm and PHP installed.

Next, you can run these commands inside the root directory of the Laravel application to install the application:

1. `composer install`
2. `cp .env.example .env`
3. `php artisan key:generate`
4. `npm install react`
5. `npm install react-dom`
6. `npm install`
7. `npm run dev`

After this, to run locally you can use PHP's development server, and initialize a socket to communicate with the MHA (on two separate terminal windows):

1. `php artisan serve` (by default this serves on localhost:8000, you can change the port number with the option: --port=____)
2. `php artisan socket:start` (make sure to run this command after osp is already running)

Alternatively, you can set up Laravel to be served via Apache, in which case only the second command from above needs to be run. Apache is already preinstalled on macOS, and can be installed with `sudo apt-get install apache2` on linux-based systems. Once installed you need to edit the config files in Apache so it serves the /public directory in the Laravel application. Once this is done, the only thing left to be able to connect remotely is ensuring you have read/write permissions on the /storage and /bootstrap/cache directories.

To open the applications, connect via the browser at localhost:8000 if running on php's development server, or type your ip address into a browser if running on Apache.

## Intalling NAL-NL2 extension
For NAL-NL2 to work, you must install a custom PHP extension. To do so follow these steps:

1. copy '../resources/nalnl2_extension/nalnl2extension.so' into your PHP extension directory
2. copy '../resources/nalnl2_extension/nalnl2extension.ini' into your PHP ini directory


## Documentation

Go to 'localhost:8000/docs' route to the ApiGen docs of all controller methods.

## Learning Laravel
The [Laravel documentation](https://laravel.com/docs) is thorough, complete, and makes it easy to get started learning the framework. Please refer to this if you are having trouble with Laravel.


# Proper Calibration Teqniques

## Researcher page

1.  Local:  localhost:8000/researcherpage; Apache: IP_ADDRESS/researcherpage
2.  Add at least 5db to each cell in the the G65 row
3.  Press transmit and listen to the sound that plays
4.  Make another change, let’s say removing those 5db and listen again
5.  As soon as you have heard a difference between two parameter states, you can be sure that the software is working as intended.



## Nalnl2

1.  Local:  localhost:8000/nalnl2; Apache: IP_ADDRESS/nalnl2
2.  Start out by filling out some dummy information into the provided form
3.  Enter the PTA for a person with flat heating loss and press get parameters
4.  Make sure that the parameters populate the table below
5.  Now, change the PTA to have sloping loss and press get parameters
6.  Confirm that the parameters have actually changed by visually comparing the table to its state in step 2.
7.  Once you have seen the difference, you can be sure the app is running as intended



## 4AFC


1.  Local:  localhost:8000/4afc; Apache: IP_ADDRESS/4afc
2.  Click on the four choices and the play symbol and make sure you are hearing the appropriate word
3.  Once you hear the words playing on click, you know that the 4AFC app is working properly



# API

## Connecting
Logging into osp manually

POST request on .../api/connect

required body:

```
{
	"URI": string
}
```

* where URI is the login info you want to use


## Updating Parameters
POST request on .../api/params

required body:

```
{
	"noOp": int,
	"afc": int,
	"feedback": int,
	"rear": int,
	"g50": [int,int,int,int,int,int],
	"g80": [int,int,int,int,int,int],
	"kneelow": [int,int,int,int,int,int],
	"mpoLimit": [int,int,int,int,int,int],
	"attackTime": [int,int,int,int,int,int],
	"releaseTime": [int,int,int,int,int,int]
}
```


## Playing sound
POST request on ../api/4afc

required body:

```
{
	"Word_set": int
	"Actual_answer": int
}
```

* where wordset is the path corresponding to the directory in the resources/sounds directory, and actual_answer is the path corresponding to the sound file in that directory.

## Disconnect

To manually log off (you may need to reinitialize socket connection after this)

POST request on .../api/disconnect

* there is no required body