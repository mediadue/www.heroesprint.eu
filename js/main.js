$(window).scroll(function (event) {
		
    var scroll = $(window).scrollTop();
    if (scroll > 450) {
        $('.back-to-top').fadeIn(300);
    } else {
        $('.back-to-top').fadeOut(300);
    }

});


$(window).load(function() {
    $('.flexslider').flexslider({
  		animation: "slide",
  		direction: "vertical",
  		controlNav: false,
  		directionNav: false
	});
});


$(document).ready(function(){

	/* BACK TO TOP BUTTON */
	$('.back-to-top').click(function(event) {
	    event.preventDefault();
	    $('html, body').animate({scrollTop: 0}, 300);
	    return false;
	})

/* COOKIES LAW MANDATORY ALERT */
//Cookies.remove('cookie-alert');//for testing purpose only
  a = Cookies.get('cookie-alert'); 
  if(  a !== 'accepted' ){
    $('.cookie-alert').css("display","table");
  }
  $('.cookie-alert button').click(function( e ){
      e.preventDefault(); // Do not perform default action when button is clicked      
       Cookies.set('cookie-alert', 'accepted', { expires: 365, path: '/' });
  });

  $('#prodDetailsModal').on('show.bs.modal', function (event) {

    var button = $(event.relatedTarget) // Button that triggered the modal
    var product = button.data('prod') // Extract info from data-* attributes
    var modal = $(this)
    if (product == "carta") { modal.find('.modal-title').text('Stampa manifesti Carta ');}
    if (product == "pvc") { modal.find('.modal-title').text('Stampa su PVC ');}
    if (product == "banner") { modal.find('.modal-title').text('Stampa Banner ');}
    
  });


}); // document.Ready
