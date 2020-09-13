// Implementing End to end encryption within WordPress
jQuery(function($){

	if (typeof end2end_set != 'undefined') {
		var end2end_decrypt_button = '<span id="end2end-submit" class="preview button" id="end2end-submit">Decrypt</span>';
	} else {
		var end2end_decrypt_button = '';
	}

	// JS for the backend
	if (typeof end2end_frontend == 'undefined') {

		// Inject the form into the page
		var end2end_form = '<label>'+end2end_label+'</label> <input type="password" id="end2end-key" name="end2end-key" />'+end2end_decrypt_button+'<div style="display:none" id="end2end-temporary-storage"></div>';
		$("#end2end-backend-form").html(end2end_form);

		// Set temporary storage area
		var content = $('#content').html();
		$('#end2end-temporary-storage').html(content);

		// If encryption key has been previously set, then ... 
		if (typeof end2end_set != 'undefined') {
			if (true == end2end_set) {
				// Decrypt from temporary storage
				$('#end2end-submit').click(function() {
					var end2end_key = $('#end2end-key').val();
					var end2end_text = $('#end2end-temporary-storage').html();
					var end2end_blob = Aes.Ctr.decrypt(end2end_text, end2end_key, 256)
					$('#content').attr('value', end2end_blob);
					$('#content').html(end2end_blob);
				});
			}
		}

		// We want to encrypt the data every time we hit publish
		$('#publish').click(function() {
			var end2end_key = $('#end2end-key').val();

			// Don't encrypt if encryption key is non-existent (someone may not want to encrypt this post)
			if ( '' != end2end_key ) {
				$('#end2end-key').val('end2end_key'); // Kill value in input field - otherwise it gets sent back to the server
				var end2end_text = $('#content').val();
				var end2end_blob = Aes.Ctr.encrypt(end2end_text, end2end_key, 256)
				$('#content').attr('value', end2end_blob);
				$('#content').html(end2end_blob);
			}
		});

	} else {

		// Inject the form into the page
		var end2end_form = '<form id="end2end"><p><label for="end2end-key">'+end2end_label+'</label><input id="end2end-key" type="password"/></p></form>';
		$("#end2end-frontend-form").html(end2end_form);

		// Block form from submitting (otherwise it could inadvertently send your encryption key back to the server)
		$("#end2end").submit(function(){
			return false;
		});

		// Set temporary storage area
		var content = $('#end2end-text').html();
		$('#end2end-temporary-storage').html(content);

		// Re-encrypt whenever the key is changed
		$('#end2end-key').keyup(function() {
			var end2end_key = $('#end2end-key').val();
			var end2end_text = $('#end2end-temporary-storage').html();
			var end2end_blob = Aes.Ctr.decrypt(end2end_text, end2end_key, 256)
			$('#end2end-text').html(end2end_blob);
		});
	}
});
