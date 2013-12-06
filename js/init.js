
// Implementing End to end encryption within WordPress
jQuery(function($){

	if (typeof encryption_frontend == 'undefined') {

		// Set temporary storage area
		var content = $('#content').html();
		$('#secure-content-temporary-storage').html(content);

		// We want to encrypt the data every time we hit publish
		$('#publish').click(function() {
			var encryption_key = $('#encryption-key').val();

			// Don't encrypt if encryption key is non-existent (someone may not want to encrypt this post)
			if ( '' != encryption_key ) {
				var encryption_text = $('#content').val();
				var encrypted_blob = Aes.Ctr.encrypt(encryption_text, encryption_key, 256)
				$('#content').attr('value', encrypted_blob);
			}
		});

		// If encryption key has been previously set, then ... 
		if (true == encryption_set) {
			// Decrypt from temporary storage
			$('#encryption-key').keyup(function() {
				var encryption_key = $('#encryption-key').val();
				var encryption_text = $('#secure-content-temporary-storage').html();
				var decrypted_blob = Aes.Ctr.decrypt(encryption_text, encryption_key, 256)
				$('#content').attr('value', decrypted_blob);
			});
		}

	} else {

		// Set temporary storage area
		var content = $('#secure-content-text').html();
		$('#secure-content-temporary-storage').html(content);

		// Re-encrypt whenever the key is changed
		$('#encryption-key').keyup(function() {
			var encryption_key = $('#encryption-key').val();
			var encryption_text = $('#secure-content-temporary-storage').html();
			var decrypted_blob = Aes.Ctr.decrypt(encryption_text, encryption_key, 256)
			$('#secure-content-text').html(decrypted_blob);
		});
	}
});
