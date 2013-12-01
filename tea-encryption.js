// TEAencrypt: Use Corrected Block TEA to encrypt plaintext using password
//             (note plaintext & password must be strings not string objects)
//
// Return encrypted text as string
//
function TEAencrypt(plaintext, password)
{
    if (plaintext.length == 0) return('');  // nothing to encrypt
    // 'escape' plaintext so chars outside ISO-8859-1 work in single-byte packing, but keep
    // spaces as spaces (not '%20') so encrypted text doesn't grow too long (quick & dirty)
    var asciitext = escape(plaintext).replace(/%20/g,' ');
    var v = strToLongs(asciitext);  // convert string to array of longs
    if (v.length <= 1) v[1] = 0;  // algorithm doesn't work for n<2 so fudge by adding a null
    var k = strToLongs(password.slice(0,16));  // simply convert first 16 chars of password as key
    var n = v.length;

    var z = v[n-1], y = v[0], delta = 0x9E3779B9;
    var mx, e, q = Math.floor(6 + 52/n), sum = 0;

    while (q-- > 0) {  // 6 + 52/n operations gives between 6 & 32 mixes on each word
        sum += delta;
        e = sum>>>2 & 3;
        for (var p = 0; p < n; p++) {
            y = v[(p+1)%n];
            mx = (z>>>5 ^ y<<2) + (y>>>3 ^ z<<4) ^ (sum^y) + (k[p&3 ^ e] ^ z);
            z = v[p] += mx;
        }
    }

    var ciphertext = longsToStr(v);

    return escCtrlCh(ciphertext);
}

//
// TEAdecrypt: Use Corrected Block TEA to decrypt ciphertext using password
//
function TEAdecrypt(ciphertext, password)
{
    if (ciphertext.length == 0) return('');
    var v = strToLongs(unescCtrlCh(ciphertext));
    var k = strToLongs(password.slice(0,16)); 
    var n = v.length;

    var z = v[n-1], y = v[0], delta = 0x9E3779B9;
    var mx, e, q = Math.floor(6 + 52/n), sum = q*delta;

    while (sum != 0) {
        e = sum>>>2 & 3;
        for (var p = n-1; p >= 0; p--) {
            z = v[p>0 ? p-1 : n-1];
            mx = (z>>>5 ^ y<<2) + (y>>>3 ^ z<<4) ^ (sum^y) + (k[p&3 ^ e] ^ z);
            y = v[p] -= mx;
        }
        sum -= delta;
    }

    var plaintext = longsToStr(v);

    // strip trailing null chars resulting from filling 4-char blocks:
    plaintext = plaintext.replace(/\0+$/,'');

    return unescape(plaintext);
}


// supporting functions

function strToLongs(s) {  // convert string to array of longs, each containing 4 chars
    // note chars must be within ISO-8859-1 (with Unicode code-point < 256) to fit 4/long
    var l = new Array(Math.ceil(s.length/4));
    for (var i=0; i<l.length; i++) {
        // note little-endian encoding - endianness is irrelevant as long as 
        // it is the same in longsToStr() 
        l[i] = s.charCodeAt(i*4) + (s.charCodeAt(i*4+1)<<8) + 
               (s.charCodeAt(i*4+2)<<16) + (s.charCodeAt(i*4+3)<<24);
    }
    return l;  // note running off the end of the string generates nulls since 
}              // bitwise operators treat NaN as 0

function longsToStr(l) {  // convert array of longs back to string
    var a = new Array(l.length);
    for (var i=0; i<l.length; i++) {
        a[i] = String.fromCharCode(l[i] & 0xFF, l[i]>>>8 & 0xFF, 
                                   l[i]>>>16 & 0xFF, l[i]>>>24 & 0xFF);
    }
    return a.join('');  // use Array.join() rather than repeated string appends for efficiency
}

function escCtrlCh(str) {  // escape control chars etc which might cause problems with encrypted texts
    return str.replace(/[\0\t\n\v\f\r\xa0'"!]/g, function(c) { return '!' + c.charCodeAt(0) + '!'; });
}

function unescCtrlCh(str) {  // unescape potentially problematic nulls and control characters
    return str.replace(/!\d\d?\d?!/g, function(c) { return String.fromCharCode(c.slice(1,-1)); });
}


// Ryan's code
jQuery(function($){

	if (typeof encryption_frontend == 'undefined') {

		// We want to encrypt the data every time we hit publish
		$('#publish').click(function() {
			var encryption_key = $('#encryption-key').val();

			// Don't encrypt if encryption key is non-existent (someone may not want to encrypt this post)
			if ( '' != encryption_key ) {
				var encryption_text = $('#content').val();
				var encrypted_blob = TEAencrypt(encryption_text, encryption_key);
				$('#content').attr('value', encrypted_blob);
			}
		});

		// If encryption key has been previously set, then 
		if (true == encryption_set) {
			// Copy content for temporary storage
			var content = $('#content').val();
			$('#secure-content-temporary-storage').html(content);

			$('#encryption-key').keyup(function() {
				var encryption_key = $('#encryption-key').val();
				var encryption_text = $('#secure-content-temporary-storage').html();
				var decrypted_blob = TEAdecrypt(encryption_text, encryption_key);
				$('#content').attr('value', decrypted_blob);
			});
		}
	} else {
		var content = $('#secure-content-text').html();
		$('#secure-content-temporary-storage').html(content);

		$('#encryption-key').keyup(function() {
			var encryption_key = $('#encryption-key').val();
			var encryption_text = $('#secure-content-temporary-storage').html();
			var decrypted_blob = TEAdecrypt(encryption_text, encryption_key);
			$('#secure-content-text').html(decrypted_blob);
		});
	}
});
