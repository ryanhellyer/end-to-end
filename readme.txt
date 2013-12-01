=== End to End ===
Contributors: ryanhellyer
Tags: encryption
Requires at least: 3.8
Stable tag: 1.0
License: GPLv2 or later

Provides true end to end encryption for WordPress. An encrypted data blob will be sent from your browser back to WordPress and can only be decrypted through the use of your unique (per-page) encryption key (password).


== Description ==

Attacks on privacy have increased peoples awareness of privacy. It is well known that true <a href="http://en.wikipedia.org/wiki/End-to-end_encryption">end to end encryption</a> is necessary to obtain complete privacy. WordPress provides private pages and password protected pages, but these still leave your data open to spying at both the server level and whilst in transit. Even if your server is locked down and secure and you route the data over https, a man in the middle attack could still be used to forcibly gain access to your data.

With the End to end plugin, not even your WordPress installation will be able to decrypt your content due to it's use of true end to end encryption techniques. The only weak link in the encryption is you (don't hand over your keys to anyone) and your computer itself.

The encryption used, is a simple but highly effective <a href="http://en.wikipedia.org/wiki/Advanced_Encryption_Standard">AES</a> style encryption provided by <a href="http://www.movable-type.co.uk/scripts/aes.html">Chris Veness</a>. AES is a simple but powerful encryption algorithm based on the Rijndael cipher. This is the exact same cipher used and recommended by many government agencies for top secret data storage and is considered the industry standard for storing extremely private data. To learn more about this cipher, check out <a href="http://twit.tv/show/security-now/125">Symmetric Ciphers from Leo Laporte and Steve Gibson</a>.

To ensure maximal security, it is recommended that use a complex encryption key. Head on over to <a href="https://www.grc.com/haystack.htm">Steve Gibson's password haystacks page</a> to help find a suitable password.


== Installation ==

Upload the End to End plugin to your blog and activate it.

There will be an encryption key input field above "Publish" when editing a post. If a key is set, then encryption will be applied and you will be prompted to re-enter it to view the content again.

It is critical to ensure that you do not forget the keys. If you forget them, you will <strong>never</strong> be able to recover the content (which is the point of this plugin - it should be uncrackable).

<em>Note: Since this is true end-to-end encryption, you will be required to enter this EVERY time you access this content. The server does not (and should not) know how to decrypt this content.</em>


== Changelog ==

= 1.0 =

* Initial plugin creation

