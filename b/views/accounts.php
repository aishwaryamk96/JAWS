<!doctype html>
<html lang="en">
<head>
	<meta charset="utf-8"></meta>
	<title>Login to Jigsaw Academy</title>
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no"></meta>
	<meta name="google-signin-client_id" content="659519706302-a3klkag43u3dsvkja0k8h86f9ll61a6e"></meta>
	<!-- <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous"> -->
	<link href="https://fonts.googleapis.com/css?family=Montserrat:400,700" rel="stylesheet">
	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.3.1/css/all.css" integrity="sha384-mzrmE5qonljUremFsqc01SB46JvROS7bZs3IO2EmfFsd15uHvIt+Y8vEf7N7fWAU" crossorigin="anonymous">
	<link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
	<link rel="stylesheet" href="https://unpkg.com/bootstrap-material-design@4.1.1/dist/css/bootstrap-material-design.min.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/arrive/2.4.1/arrive.min.js"></script>
	<script src="https://unpkg.com/popper.js@1.12.6/dist/umd/popper.js" integrity="sha384-fA23ZRQ3G/J53mElWqVJEGJzU0sTs+SvzG8fXVWP+kJQ1lwFAOkcUOysnlKJC33U" crossorigin="anonymous"></script>
	<!-- <script src="https://cdn.rawgit.com/FezVrasta/snackbarjs/1.1.0/dist/snackbar.min.js"></script> -->
	<script src="https://unpkg.com/bootstrap-material-design@4.1.1/dist/js/bootstrap-material-design.js" integrity="sha384-CauSuKpEqAFajSpkdjv3z9t8E7RlpJ1UP0lKM/+NdtSarroVKu069AlsRPKkFBz9" crossorigin="anonymous"></script>
	<script>
	$(document).ready(function() {
		$('body').bootstrapMaterialDesign();
	});
</script>
	<script type="text/javascript" src="//platform.linkedin.com/in.js">
		api_key: 81wzatdlzo1p9v
		authorize: true
		onLoad: onLinkedInLoad
	</script>
	<link rel="stylesheet" type="text/css" href="/b/css/login.css">
    <script>
		function authenticate(auth) {
			$.post("/btcapi/identity/authentication", auth, function(data) {
				window.location.href = $("#ru").val();
			});
		}
    </script>
    </head>
    <body>
        <noscript>
        	<div class="no-js">
        		<div class="no-js-content text-center">
        			<p>Javascript not available...You have choosen the dark side.</p>
        			<h1>Force cannot be with you!!!</h1>
        			<img src="https://www.jigsawacademy.com/jaws/media/jaws/frontend/images/no-force-yoda.jpeg" alt="No Force Yoda">
        		</div>
        	</div>
        </noscript>
        <input type="hidden" id="ru" value="<?= $ru; ?>">
        <div class="container center" style="display: flex;flex-wrap: wrap;justify-content: center;">
			<div class="d-flex  " style="width: 100%;justify-content: center;">
                <label class="text-center text-uppercase text-muted border-bottom mb-0 pb-3" style="font-size: 2rem;">
                    Login to jigsaw academy
                </label>
			</div>
			<div class="d-flex social-icons" style="justify-content: space-between;align-items: center;width: 40%;margin-top: 20px;">
                <div class=" m-0" onclick="fbLogin()" id="fbButton0">
                    <i class="fab fa-5x fa-facebook-square"></i>
                </div>
                <div  onclick="googleLogin()">
                    <i class="fab fa-5x fa-google"></i>
                </div>
                <div class=" m-0" onclick="liAuth()">
                    <i class="fab fa-5x fa-linkedin"></i>
                </div>
			</div>
		</div>
        <!-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script> -->
        <script> /* Facebook Login */
            // This is called with the results from from FB.getLoginStatus().
            function statusChangeCallback(response) {
                console.log('statusChangeCallback');
                console.log(response);
                // The response object is returned with a status field that lets the
                // app know the current login status of the person.
                // Full docs on the response object can be found in the documentation
                // for FB.getLoginStatus().
                if (response.status === 'connected') {
                    // Logged into your app and Facebook.
                    getFbUserData();
                } else {
                    // The person is not logged into your app or we are unable to tell.
                    // document.getElementById('status').innerHTML = 'Please log into this app.';
                    $("#fbButton").html("Login with Facebook");
                }
            }

            // This function is called when someone finishes with the Login
            // Button.  See the onlogin handler attached to it in the sample
            // code below.
            function checkLoginState() {
                FB.getLoginStatus(function (response) {
                    statusChangeCallback(response);
                });
            }

            window.fbAsyncInit = function () {
                FB.init({
                    appId: '1277255255686445',
                    cookie: true,  // enable cookies to allow the server to access
                    // the session
                    xfbml: true,  // parse social plugins on this page
                    version: 'v3.2' // use graph api version 2.8
                });

                // Now that we've initialized the JavaScript SDK, we call
                // FB.getLoginStatus().  This function gets the state of the
                // person visiting this page and can return one of three states to
                // the callback you provide.  They can be:
                //
                // 1. Logged into your app ('connected')
                // 2. Logged into Facebook, but not your app ('not_authorized')
                // 3. Not logged into Facebook and can't tell if they are logged into
                //    your app or not.
                //
                // These three cases are handled in the callback function.

                FB.getLoginStatus(function (response) {
                    statusChangeCallback(response);
                });

            };

            // Load the SDK asynchronously
            (function (d, s, id) {
                var js, fjs = d.getElementsByTagName(s)[0];
                if (d.getElementById(id)) return;
                js = d.createElement(s); js.id = id;
                js.src = "https://connect.facebook.net/en_US/sdk.js";
                fjs.parentNode.insertBefore(js, fjs);
            }(document, 'script', 'facebook-jssdk'));

            // Here we run a very simple test of the Graph API after login is
            // successful.  See statusChangeCallback() for when this call is made.
            function getFbUserData(autoConnect = true) {
                // console.log('Welcome!  Fetching your information.... ');
                if (!fbAuth()) {
                    FB.api('/me', {fields: 'name, email'}, function (response) {
                        $("#fbButton0").attr("data-name", response.name).attr("data-email", response.email);
                    });
                    if (!autoConnect) {
                        fbAuth();
                    }
                }
            }
            function fbAuth() {
                var btn = $("#fbButton0");
                if (!!btn.data("email")) {
                    var auth = {"name": btn.data("name"), "email": btn.data("email"), soc: 'fb'};
                    authenticate(auth);
                    return true;
                }
                else {
                    return false;
                }
            }
            function fbLogin() {
                if (!fbAuth()) {
                    FB.login(
                        function (response) {
                            if (response.authResponse) {
                                // Get and display the user profile data
                                getFbUserData(false);
                            } else {
                                document.getElementById('status').innerHTML = 'User cancelled login or did not fully authorize.';
                            }
                        }, {scope: 'email'}
                    );
                }
            }
        </script>
        <script> /* Google Login */
            // var auth2 = {};
            // var helper = (function() {
            //     return {
            //         /**
            //         * Hides the sign in button and starts the post-authorization operations.
            //         *
            //         * @param {Object} authResult An Object which contains the access token and
            //         *   other authentication information.
            //         */
            //         onSignInCallback: function(authResult) {
            //             $('#authResult').html('Auth Result:<br/>');
            //             for (var field in authResult) {
            //                 $('#authResult').append(' ' + field + ': ' +
            //                     authResult[field] + '<br/>');
            //             }
            //             if (authResult.isSignedIn.get()) {
            //                 $('#authOps').show('slow');
            //                 $('#gConnect').hide();
            //                 helper.profile();
            //                 // helper.people();
            //             } else {
            //                 if (authResult['error'] || authResult.currentUser.get().getAuthResponse() == null) {
            //                     // There was an error, which means the user is not signed in.
            //                     // As an example, you can handle by writing to the console:
            //                     console.log('There was an error: ' + authResult['error']);
            //                 }
            //                 $('#authResult').append('Logged out');
            //                 $('#authOps').hide('slow');
            //                 $('#gConnect').show();
            //             }

            //             console.log('authResult', authResult);
            //         },

            //         /**
            //         * Calls the OAuth2 endpoint to disconnect the app for the user.
            //         */
            //         disconnect: function() {
            //             // Revoke the access token.
            //             auth2.disconnect();
            //         },

            //         /**
            //         * Gets and renders the list of people visible to this app.
            //         */
            //         people: function() {
            //             gapi.client.plus.people.list({
            //                 'userId': 'me',
            //                 'collection': 'visible'
            //             }).then(function(res) {
            //                 var people = res.result;
            //                 $('#visiblePeople').empty();
            //                 $('#visiblePeople').append('Number of people visible to this app: ' +
            //                     people.totalItems + '<br/>');
            //                 for (var personIndex in people.items) {
            //                 person = people.items[personIndex];
            //                 $('#visiblePeople').append('<img src="' + person.image.url + '">');
            //                 }
            //             });
            //         },

            //         /**
            //         * Gets and renders the currently signed in user's profile data.
            //         */
            //         profile: function(){
            //             gapi.client.plus.people.get({
            //                 'userId': 'me'
            //             }).then(function(res) {
            //                 var profile = res.result;
            //                 console.log(profile);
            //                 // $('#profile').empty();
            //                 // $('#profile').append(
            //                 //     $('<p><img src=\"' + profile.image.url + '\"></p>'));
            //                 // $('#profile').append(
            //                 //     $('<p>Hello ' + profile.displayName + '!<br />Tagline: ' +
            //                 //     profile.tagline + '<br />About: ' + profile.aboutMe + '</p>'));
            //                 // if (profile.emails) {
            //                 // $('#profile').append('<br/>Emails: ');
            //                 // for (var i=0; i < profile.emails.length; i++){
            //                 //     $('#profile').append(profile.emails[i].value).append(' ');
            //                 // }
            //                 // $('#profile').append('<br/>');
            //                 // }
            //                 // if (profile.cover && profile.coverPhoto) {
            //                 // $('#profile').append(
            //                 //     $('<p><img src=\"' + profile.cover.coverPhoto.url + '\"></p>'));
            //                 // }
            //                 auth = {name: profile.displayName, email: };
            //             }, function(err) {
            //                 var error = err.result;
            //                 $('#profile').empty();
            //                 $('#profile').append(error.message);
            //             });
            //         }
            //     };
            // })();

            // /**
            // * jQuery initialization
            // */
            // $(document).ready(function() {
            //     $('#disconnect').click(helper.disconnect);
            //     $('#loaderror').hide();
            //     if ($('meta')[0].content == '659519706302-a3klkag43u3dsvkja0k8h86f9ll61a6e') {
            //         alert('This sample requires your OAuth credentials (client ID) ' +
            //             'from the Google APIs console:\n' +
            //             '    https://code.google.com/apis/console/#:access\n\n' +
            //             'Find and replace YOUR_CLIENT_ID with your client ID.'
            //         );
            //     }
            // });

            // /**
            // * Handler for when the sign-in state changes.
            // *
            // * @param {boolean} isSignedIn The new signed in state.
            // */
            // var updateSignIn = function() {
            //     console.log('update sign in state');
            //     if (auth2.isSignedIn.get()) {
            //         console.log('signed in');
            //         helper.onSignInCallback(gapi.auth2.getAuthInstance());
            //     }else{
            //         console.log('signed out');
            //         helper.onSignInCallback(gapi.auth2.getAuthInstance());
            //     }
            // }

            // /**
            // * This method sets up the sign-in listener after the client library loads.
            // */
            // function startApp() {
            //     gapi.load('auth2', function() {
            //         gapi.client.load('plus','v1').then(function() {
            //         gapi.signin2.render('signin-button', {
            //             scope: 'https://www.googleapis.com/auth/plus.login',
            //             fetch_basic_profile: false });
            //         gapi.auth2.init({fetch_basic_profile: false,
            //             scope:'https://www.googleapis.com/auth/plus.login'}).then(
            //                 function (){
            //                     console.log('init');
            //                     auth2 = gapi.auth2.getAuthInstance();
            //                     auth2.isSignedIn.listen(updateSignIn);
            //                     auth2.then(updateSignIn);
            //                 });
            //         });
            //     });
            // }
            var gpClicked = true;
            function onSignIn(googleUser) {
                var profile = googleUser.getBasicProfile();
                // console.log('ID: ' + profile.getId()); // Do not send to your backend! Use an ID token instead.
                // console.log('Name: ' + profile.getName());
                // console.log('Image URL: ' + profile.getImageUrl());
                // console.log('Email: ' + profile.getEmail()); // This is null if the 'email' scope is not present.
                if (!gpClicked) {
                    gpClicked = true;
                }
                else {
                    var auth = {name: profile.getName(), email: profile.getEmail(), photo_url: profile.getImageUrl(), soc: 'gp'};
                    authenticate(auth);
                }
            }
            function onSignInFailure() {

            }
            function renderButton() {
                gapi.signin2.render('my-signin2', {
                    'scope': 'profile email',
                    'width': 215,
                    'height': 34,
                    'longtitle': true,
                    'theme': 'dark',
                    'onsuccess': onSignIn,
                    'onfailure': onSignInFailure
                });
            }
            function googleLogin(){
                let gauth = gapi.auth2.getAuthInstance();
                console.log(gauth);
                
                gauth.signIn()
                .then(function(r) {
                    onSignIn(r)
                }, function(e) {
                    onSignInFailure(e)
                });
            }
            function onLoad() {
                gapi.load('auth2', function() {
                    gapi.auth2.init();
                });
            }
        </script>
        <!-- <script src="https://apis.google.com/js/client:platform.js?onload=startApp"></script> -->
        <script src="https://apis.google.com/js/platform.js?onload=onLoad" async defer></script>
        <script> /* LinkedIn Login */
            // Setup an event listener to make an API call once auth is complete
            function onLinkedInLoad() {
                IN.Event.on(IN, "auth", getProfileData);
            }

            // Handle the successful return from the API call
            function onSuccess(data) {
                // console.log(data);
                if (data.emailAddress) {
                	var name = data.firstName + " " + data.lastName;
	                $("#liButton").html("Continue as " + name).attr("data-name", name).attr("data-email", data.email)
                }
                else {
                	$("#liButton").attr("disabled", "true");
                }
            }

            // Handle an error response from the API call
            function onError(error) {
                console.log(error);
            }

            function liAuth(){
            	var btn = $("#liButton");
            	if (btn.data("email")) {
            		var auth = {name: btn.data("name"), email: btn.data("email"), soc: 'li'};
            		authenticate(auth);
            		return;
            	}
				IN.User.authorize(function(){
					// IN.API.Profile("me")
					// 	.fields("firstName", "lastName", "email-address")
					// 	.result(onSuccess);
					getProfileData();
				});
			}

            // Use the API call wrapper to request the member's basic profile data
            function getProfileData() {
                IN.API.Raw("/people/~:(id,firstName,lastName,emailAddress)").result(onSuccess).error(onError);
            }

        </script>
        <style>
            .social-icons div{
                cursor:pointer;
            }
        </style>
    </body>
</html>