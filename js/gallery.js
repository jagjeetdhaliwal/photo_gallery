$(document).ready(function(){

    // Override append function to handle dynamic dom additions using triggers.
    (function($) {
      var origAppend = $.fn.append;

      $.fn.append = function () {
          return origAppend.apply(this, arguments).trigger("append");
      };
    })(jQuery);

    $(".cards-container").bind("append", function() { $('.materialboxed').materialbox();});

    // Handle contact form submission
    $('#contact_form').on('submit', function(e) {
        e.preventDefault();  //prevent form from submitting

        var form_button = $('#contact_form_submit');
        form_button.prop('disabled', true);

        var form = $(this);
        var error = {};
    		var first_name = $('#first_name').val().trim();
    		var last_name = $('#last_name').val().trim();
    		var email = $('#email').val().trim();
    		var message = $('#message').val().trim();
        var csrf_token = $('#csrf_token').val().trim();

        // Validations
    		if (first_name == '' || first_name == null) {
    			$('#first_name').addClass('invalid');
    			$('#first_name_error').show();
    			error['first_name'] = true;
    		} else {
    			$('#first_name').removeClass('invalid');
    			$('#first_name_error').hide();
    			delete error['first_name'];
    		}

    		if (email == '' || !validateEmail(email)) {
    			$('#email').addClass('invalid');
    			$('#email_error').show();
    			error['email'] = true;
    		} else {
    			$('#email').removeClass('invalid');
    			$('#email_error').hide();
    			delete error['email'];
    		}

    		if (message == '' || message == null) {
    			$('#message').addClass('invalid');
    			$('#message_error').show();
    			error['message'] = true;
    		} else {
    			$('#message').removeClass('invalid');
    			$('#message_error').hide();
    			delete error['message'];
    		}

        // Submit using ajax if validation is successful
    		if (Object.keys(error).length == 0) {
    			var formData = {
              'first_name' : first_name,
              'last_name' : last_name,
              'email': email,
              'message': message,
              'csrf_token': csrf_token //for csrf validation
    			};
    	        $.ajax({
    	            type        : 'POST',
    	            url         : '/php/contact_handler.php',
    	            data        : formData,
    	            dataType    : 'json',
    	            encode      : true,
    	            beforeSend  : function() {
    	            	form_button.html('SUBMITTING...');
    	            }
    	        })
    	        .done(function(data) {
    	            if (data !=null && data.hasOwnProperty('success')) {
    	            	form.hide();
    	            	$('.contact-h4').text('Thank you for contacting us.');
    	            	$('.form-message').text('Please feel free to check the gallery any time while we try to resolve this at our earliest.').removeClass('red').show();
    	            } else if(data == null || data.hasOwnProperty('error')) {
    	    		 	form_button.html('Submit <i class="material-icons right">send</i>');
    	    		 	if (data.hasOwnProperty('error') && data.hasOwnProperty('source')) {
    	    		 		$('#' + data.source).addClass('invalid');
    	    		 		$('#' + data.source + '_error').show();
    	    		 	} else {
    	    		 		$('.form-message').text('Something went wrong, go back and try again!').show().addClass('red');
    	    		 	}
    	            }
             });
  		}

  		form_button.prop('disabled', false);
  });


  // handle load more button to get more instagram pictures.
  $('#load_more').click(function() {
    var next_max_id = $(this).data('next-max-id');
    var csrf_token = $(this).data('csrf');

    var that = $(this);

    $.ajax({
      type: 'POST',
      url: '/php/instagram.php',
      data: {
        next_max_id: next_max_id,
        csrf_token: csrf_token //for csrf validation
      },
      dataType: 'json',
      cache: false,
      success: function(data) {
        // Output data
        if (data.hasOwnProperty('next_max_id')) {
          that.data('next-max-id',data.next_max_id);
        } else {
          that.hide();
        }

        $.each(data.images, function(i, src) {
            $('.cards-container').append(getNewCardHtml(src));
        });
      }
    });
  });
});


// function to get html for every new image card
function getNewCardHtml(src) {
  var html = '';
  html += '<div class="col s6 l4">';
  html += '<div class="card hoverable">';
  html += '<div class="card-image">';
  html += '<img class="materialboxed" data-caption="'+src.description+'" src="'+src.url+'">';
  html += '<a target="_blank" class="open-on-instagram" href="'+src.link+'"><i class="material-icons">open_in_browser</i></a>';
  html += '</div></div></div>';

  return html;
}

// function to validate email on front end
function validateEmail(value) {
    filter = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
    return filter.test(value);
}
