
$(document).ready(function(){
	// Add smooth scrolling to all links
	$("a").on('click', function(event) {

		// Make sure this.hash has a value before overriding default behavior
		if (this.hash !== "") {
			// Prevent default anchor click behavior
			event.preventDefault();

			// Store hash
			var hash = this.hash;

			// Using jQuery's animate() method to add smooth page scroll
			// The optional number (800) specifies the number of milliseconds it takes to scroll to the specified area
			$('html, body').animate({
				scrollTop: $(hash).offset().top
			}, 800, function(){
	 
				// Add hash (#) to URL when done scrolling (default click behavior)
				window.location.hash = hash;
			});
		} // End if
	});
});

$(document).ready(function() {
 
  /*$("#owl-demo").owlCarousel({
 
      autoPlay: 3000, //Set AutoPlay to 3 seconds
 
      items : 3,
      itemsDesktop : [1199,3],
      itemsDesktopSmall : [979,2]
 
  });*/
 
});

/*function makeSticky() {
			var myWindow = $( window ),
				myHeader = $( ".site-header" );

			myWindow.scroll( function() {
				if ( myWindow.scrollTop() == 0 ) {
					myHeader.removeClass( "sticky-nav" );
				} else {
					myHeader.addClass( "sticky-nav" );
				}
			} );
		}

		$( function() {
			// makeSticky();
			 
			$( ".site-header" ).waypoint( 'sticky' );
		} );

	var menu = document.querySelector('.menu');
function toggleMenu () {
  menu.classList.toggle('open');
}
menu.addEventListener('click', toggleMenu);*/