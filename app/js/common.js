
// Masked phone
$("#icon_telephone").mask("(999) 999-9999");

// scroll top

	$('.contacts__btn').click(function(){

		 $('html, body').animate({scrollTop:0}, 'slow');
 });

 // scroll catalog
 $('.catalog').click(function(){
		
	$('html, body').animate({scrollTop:3200}, 'slow');
});

//callback

document.querySelector('#callback').onclick = function(){

	let	phone = document.querySelector('#icon_telephone').value

//	console.log(phone);
}