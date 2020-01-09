var isVisible = false;
$('#mybutton').click(function(){
    //$(window).scrollTop(0);
});
$(window).scroll(function(){
     var shouldBeVisible = $(window).scrollTop()>400;
     if (shouldBeVisible && !isVisible) {
          isVisible = true;
          $('#mybutton').show();
     } else if (isVisible && !shouldBeVisible) {
          isVisible = false;
          $('#mybutton').hide();
    }
});