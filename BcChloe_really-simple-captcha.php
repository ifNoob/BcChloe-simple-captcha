<?php
/*
Plugin Name: BcChloe Really Simple CAPTCHA Clone
Plugin URI: https://github.com/ifNoob/BcChloe-simple-captcha
Description: <strong>BcChloe Clone</strong> Really Simple CAPTCHA is a CAPTCHA module intended to be called from other plugins. It is originally created for my Contact Form 7 plugin. origin(https://ideasilo.wordpress.com/)
Author: Clone by BcChloe
Author URI: https://bcchloe.jp
Text Domain: really-simple-captcha
Version: 2.0 + 1.5
*/

define( 'REALLYSIMPLECAPTCHA_VERSION', '2.0' );

# contact form 7 really-simple-captcha Mode ON
define( 'WPCF7_USE_REALLY_SIMPLE_CAPTCHA', true );

# 設定ファイルが存在しない場合 (whiteout回避)
	$config_file = dirname( __FILE__ ) . '/captcha_config.php';
	if ( !file_exists($config_file) ) {
		require_once ( dirname ( __FILE__ ) . '/captcha_config_sample.php' );
	}

# wpcf7_really_simple_captcha override load
require ( dirname ( __FILE__ ) . '/wpcf7_add_form_tag_captcha_override.php');

class ReallySimpleCaptcha {

	public $chars;
	public $char_length;
	public $fonts;
	public $tmp_dir;
	public $img_size;
	public $bg;
	public $fg;
	public $base;
	public $font_size;
	public $font_char_width;
	public $img_type;
	public $file_mode;
	public $answer_file_mode;

	public function __construct() {

	$conf = character_config();	# captcha_config.php//var_dump($conf);

		/* Characters available in images */
		if ($conf['mode'] === 'en') {					$this->chars = $conf['chars_en'];
		} elseif ($conf['mode'] === 'jp') {		$this->chars = $conf['chars_jp'];
		} else { echo 'Mode Error...'; }

		/* Length of a word in an image */
		$this->char_length = $conf['char_length'];									# 文字数

		$this->fonts = $conf['fonts'];															# 文字フォント

		/* Directory temporary keeping CAPTCHA images and corresponding text files */
		$this->tmp_dir = path_join( dirname( __FILE__ ), 'tmp' );

		/* Array of CAPTCHA image size. Width and height */
# キャプチャ画像サイズは、wpcf7_add_form_tag_captcha_override.php内が優先
		$this->img_size = array( $conf['img_w'], $conf['img_h'] );	#array( 72, 24 );	origin

		/* Background color of CAPTCHA image. RGB color 0-255 */
# 背景透明化する色指定
		$this->bg = array( $conf['bg'], $conf['bg'], $conf['bg'] );	# array( 255, 255, 255 ); origin

		/* Foreground (character) color of CAPTCHA image. RGB color 0-255 */
		$this->fg = array( $conf['fg'], $conf['fg'], $conf['fg'] );	# array(0,0,0); origin

		/* Coordinates for a text in an image. I don't know the meaning. Just adjust. */
# 文字開始位置は、wpcf7_add_form_tag_captcha_override.php内が優先
		$this->base = array( $conf['base_x'], $conf['base_y'] );		# array( 6, 18 ); origin

		/* Font size */
		$this->font_size = $conf['font_size'];											# 14;

		/* Width of a character */
		$this->font_char_width = $conf['font_char_width'];					# 15;

		/* Image type. 'png', 'gif' or 'jpeg' */
		$this->img_type = $conf['type'];														# 'png';

		/* Mode of temporary image files */
		$this->file_mode = 0644;

		/* Mode of temporary answer text files */
		$this->answer_file_mode = 0640;

//var_dump($this);

	}

	/**
	 * Generate and return a random word.
	 *
	 * @return string Random word with $chars characters x $char_length length
	 */
	public function generate_random_word() {
		$word = '';

/* origin
		for ( $i = 0; $i < $this->char_length; $i++ ) {
			$pos = mt_rand( 0, strlen( $this->chars ) - 1 );
			$char = $this->chars[$pos];
			$word .= $char;
		}
*/

# 日本語文字用 2017/6
		$chars_size = mb_strlen( $this->chars );
		for ( $i = 0; $i < $this->char_length; $i++ ) {
			$pos = mt_rand( 0, $chars_size );#- 1 );
			$char = mb_substr( $this->chars, $pos, 1 );
			$word .= $char;
		}

		define("CAPTCHA_WORD", $word);	# 定数へ
		return $word;
	}

	/**
	 * Generate CAPTCHA image and corresponding answer file.
	 *
	 * @param string $prefix File prefix used for both files
	 * @param string $word Random word generated by generate_random_word()
	 * @return string|bool The file name of the CAPTCHA image. Return false if temp directory is not available.
	 */
	public function generate_image( $prefix, $word ) {
		if ( ! $this->make_tmp_dir() ) {
			return false;
		}

	$conf = character_config();	# captcha_config.php//var_dump($conf);

		$this->cleanup();

		$dir = trailingslashit( $this->tmp_dir );
		$filename = null;

		if ( $im = imagecreatetruecolor( $this->img_size[0], $this->img_size[1] ) ) {

			$bg = imagecolorallocate( $im, $this->bg[0], $this->bg[1], $this->bg[2] );
			$fg = imagecolorallocate( $im, $this->fg[0], $this->fg[1], $this->fg[2] );

			imagefill( $im, 0, 0, $bg );

/*-----------------
* 透明化 2017/6
-----------------*/
			imagecolortransparent($im, $bg);

			// randam lines
			for ( $i = 0; $i < 5; $i++ ) {
				$color  = imagecolorallocate( $im, $conf['line_color'], $conf['line_color'], $conf['line_color'] );	//196, 196, 196 );
//	origin	$color = imagecolortransparent($im, imagecolorallocate($im, 196, 196, 196));
				imageline( $im, mt_rand( 0, $this->img_size[0] - 1 ), mt_rand( 0, $this->img_size[1] - 1 ), mt_rand( 0, $this->img_size[0] - 1 ), mt_rand( 0, $this->img_size[1] - 1 ), $color );
			}

			$x = $this->base[0] + mt_rand( -2, 2 );

			$gd_info = gd_info( );
			$word_size = mb_strlen( $word );

			for ( $i = 0; $i < strlen( $word ); $i++ ) {
				$font = $this->fonts[array_rand( $this->fonts )];
				$font = $this->normalize_path( $font );

				if ( $gd_info['JIS-mapped Japanese Font Support'] ) {
					$char = mb_convert_encoding( mb_substr( $word, $i, 1 ), 'SJIS', 'UTF-8' );
				} else {
					$char = mb_substr( $word, $i, 1 );
				}
//	origin	imagettftext( $im, $this->font_size, mt_rand( -12, 12 ), $x, $this->base[1] + mt_rand( -2, 2 ), $fg, $font, $word[$i] );
				imagettftext( $im, $this->font_size, mt_rand( -12, 12 ), $x, $this->base[1] + mt_rand( -2, 2 ), $fg, $font, $char );
				$x += $this->font_char_width;
			}

			switch ( $this->img_type ) {
				case 'jpeg':
					$filename = sanitize_file_name( $prefix . '.jpeg' );
					$file = $this->normalize_path( $dir . $filename );
					imagejpeg( $im, $file );
					break;
				case 'gif':
					$filename = sanitize_file_name( $prefix . '.gif' );
					$file = $this->normalize_path( $dir . $filename );
					imagegif( $im, $file );
					break;
				case 'png':
				default:
					$filename = sanitize_file_name( $prefix . '.png' );
					$file = $this->normalize_path( $dir . $filename );

					imagepng( $im, $file );
			}

			imagedestroy( $im );
			chmod( $file, $this->file_mode );
		}

		$this->generate_answer_file( $prefix, $word );

		return $filename;
	}

	/**
	 * Generate answer file corresponding to CAPTCHA image.
	 *
	 * @param string $prefix File prefix used for answer file
	 * @param string $word Random word generated by generate_random_word()
	 */
	public function generate_answer_file( $prefix, $word ) {
		$dir = trailingslashit( $this->tmp_dir );
		$answer_file = $dir . sanitize_file_name( $prefix . '.txt' );
		$answer_file = $this->normalize_path( $answer_file );

		if ( $fh = fopen( $answer_file, 'w' ) ) {
			$word = strtoupper( $word );
			$salt = wp_generate_password( 64 );
			$hash = hash_hmac( 'md5', $word, $salt );
			$code = $salt . '|' . $hash;
			fwrite( $fh, $code );
			fclose( $fh );
		}

		chmod( $answer_file, $this->answer_file_mode );
	}

	/**
	 * Check a response against the code kept in the temporary file.
	 *
	 * @param string $prefix File prefix used for both files
	 * @param string $response CAPTCHA response
	 * @return bool Return true if the two match, otherwise return false.
	 */
	public function check( $prefix, $response ) {
		if ( 0 == strlen( $prefix ) ) {
			return false;
		}

		$response = str_replace( array( " ", "\t" ), '', $response );
		$response = strtoupper( $response );

		$dir = trailingslashit( $this->tmp_dir );
		$filename = sanitize_file_name( $prefix . '.txt' );
		$file = $this->normalize_path( $dir . $filename );

		if ( is_readable( $file ) && ( $code = file_get_contents( $file ) ) ) {
			$code = explode( '|', $code, 2 );
			$salt = $code[0];
			$hash = $code[1];

			if ( hash_hmac( 'md5', $response, $salt ) == $hash ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Remove temporary files with given prefix.
	 *
	 * @param string $prefix File prefix
	 */
	public function remove( $prefix ) {
		$dir = trailingslashit( $this->tmp_dir );
		$suffixes = array( '.jpeg', '.gif', '.png', '.php', '.txt' );

		foreach ( $suffixes as $suffix ) {
			$filename = sanitize_file_name( $prefix . $suffix );
			$file = $this->normalize_path( $dir . $filename );

			if ( is_file( $file ) ) {
				unlink( $file );
			}
		}
	}

	/**
	 * Clean up dead files older than given length of time.
	 *
	 * @param int $minutes Consider older files than this time as dead files
	 * @return int|bool The number of removed files. Return false if error occurred.
	 */
	public function cleanup( $minutes = 60, $max = 100 ) {
		$dir = trailingslashit( $this->tmp_dir );
		$dir = $this->normalize_path( $dir );

		if ( ! is_dir( $dir ) || ! is_readable( $dir ) ) {
			return false;
		}

		$is_win = ( 'WIN' === strtoupper( substr( PHP_OS, 0, 3 ) ) );

		if ( ! ( $is_win ? win_is_writable( $dir ) : is_writable( $dir ) ) ) {
			return false;
		}

		$count = 0;

		if ( $handle = opendir( $dir ) ) {
			while ( false !== ( $filename = readdir( $handle ) ) ) {
				if ( ! preg_match( '/^[0-9]+\.(php|txt|png|gif|jpeg)$/', $filename ) ) {
					continue;
				}

				$file = $this->normalize_path( $dir . $filename );

				if ( ! file_exists( $file ) || ! ( $stat = stat( $file ) ) ) {
					continue;
				}

				if ( ( $stat['mtime'] + $minutes * 60 ) < time() ) {
					if ( ! unlink( $file ) ) {
						chmod( $file, 0644 );
						unlink( $file );
					}

					$count += 1;
				}

				if ( $max <= $count ) {
					break;
				}
			}

			closedir( $handle );
		}

		return $count;
	}

	/**
	 * Make a temporary directory and generate .htaccess file in it.
	 *
	 * @return bool True on successful create, false on failure.
	 */
	public function make_tmp_dir() {
		$dir = trailingslashit( $this->tmp_dir );
		$dir = $this->normalize_path( $dir );

		if ( ! wp_mkdir_p( $dir ) ) {
			return false;
		}

		$htaccess_file = $this->normalize_path( $dir . '.htaccess' );

		if ( file_exists( $htaccess_file ) ) {
			return true;
		}

		if ( $handle = fopen( $htaccess_file, 'w' ) ) {
			fwrite( $handle, 'Order deny,allow' . "\n" );
			fwrite( $handle, 'Deny from all' . "\n" );
			fwrite( $handle, '<Files ~ "^[0-9A-Za-z]+\\.(jpeg|gif|png)$">' . "\n" );
			fwrite( $handle, '    Allow from all' . "\n" );
			fwrite( $handle, '</Files>' . "\n" );
			fclose( $handle );
		}

		return true;
	}

	/**
	 * Normalize a filesystem path.
	 *
	 * This should be replaced by wp_normalize_path when the plugin's
	 * minimum requirement becomes WordPress 3.9 or higher.
	 *
	 * @param string $path Path to normalize.
	 * @return string Normalized path.
	 */
	private function normalize_path( $path ) {
		$path = str_replace( '\\', '/', $path );
		$path = preg_replace( '|/+|', '/', $path );
		return $path;
	}
}
