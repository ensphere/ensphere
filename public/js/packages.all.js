	jQuery(document).ready(function($){

		$('a.btn-danger').click(function(event){
			event.preventDefault();
			var href = $(this).attr('href');
			alertify.confirm("Are you sure you want to delete this item?", function (e) {
			    if (e) {
			        window.location = href;
			    }
			});
		});

		$(window).load(function(){

		});
	});
//# sourceMappingURL=packages.all.js.map