<?php
/**==========================
* really simple captcha override
* 2018/6
===========================*/

global $wpcf7_confflag;
$wpcf7_confflag = false;

add_action( 'plugins_loaded', 'wpcf7_really_simple_captcha_override', 11 );

function wpcf7_really_simple_captcha_override() {
	wpcf7_add_form_tag_captcha_ov();
}

/**----------------
* image & input
-----------------*/
function wpcf7_add_form_tag_captcha_ov() {
	// CAPTCHA-Challenge (image)
	wpcf7_add_form_tag( 'captchac',
		'wpcf7_captchac_form_tag_handler_ov',
		array(
			'name-attr' => true,
			'zero-controls-container' => true,
			'not-for-mail' => true,
		)
	);
	// CAPTCHA-Response (input)
	wpcf7_add_form_tag( 'captchar',
		'wpcf7_captchar_form_tag_handler_ov',
		array(
			'name-attr' => true,
			'do-not-store' => true,
			'not-for-mail' => true,
		)
	);
}

/**----------------
* <img> tag 生成
* alt 属性 追加
-----------------*/
function wpcf7_captchac_form_tag_handler_ov( $tag ) {
	if ( ! class_exists( 'ReallySimpleCaptcha' ) ) {
		$error = sprintf(
			/* translators: %s: link labeled 'Really Simple CAPTCHA' */
			esc_html( __( "To use CAPTCHA, you need %s plugin installed.", 'contact-form-7' ) ),
			wpcf7_link( 'https://wordpress.org/plugins/really-simple-captcha/', 'Really Simple CAPTCHA' ) );

		return sprintf( '<em>%s</em>', $error );
	}

	if ( empty( $tag->name ) ) {
		return '';
	}

	$class = wpcf7_form_controls_class( $tag->type );
	$class .= ' wpcf7-captcha-' . $tag->name;

	$atts = array();
	$atts['class'] = $tag->get_class_option( $class );
	$atts['id'] = $tag->get_id_option();
/*	origin
	$op = array( // Default
		'img_size' => array( 72, 24 ),
		'base' => array( 6, 18 ),
		'font_size' => 14,
		'font_char_width' => 15,
	);
*/
/**----------------
* チャプチャ画像サイズ指定
* 文字開始位置
* 文字サイズ指定
----------------*/
	$conf = character_config();	# captcha_config.php//var_dump($conf);
	$op = array(
		'img_size' 				=> array( $conf['img_w'], $conf['img_h'] ),			# 幅, 縦
		'base' 						=> array( $conf['base_x'], $conf['base_y'] ),		# x, y
		'font_size'				=> $conf['font_size'],													# 文字サイズ
		'font_char_width'	=> $conf['font_char_width'],										# 文字太
	);

	$op = array_merge( $op, wpcf7_captchac_options( $tag->options ) );

	if ( ! $filename = wpcf7_generate_captcha( $op ) ) {
		return '';
	}

	if ( ! empty( $op['img_size'] ) ) {
		if ( isset( $op['img_size'][0] ) ) {
			$atts['width'] = $op['img_size'][0];
		}

		if ( isset( $op['img_size'][1] ) ) {
			$atts['height'] = $op['img_size'][1];
		}
	}

	$strs = mb_convert_kana(CAPTCHA_WORD, 'C');	# 定数より ひらがな < カタカナ変換
/*-----------------
*	<img> のalt属性を追加
-----------------*/
	$atts['alt'] = $strs;
	$atts['src'] = wpcf7_captcha_url( $filename );

	$atts = wpcf7_format_atts( $atts );

	$prefix = substr( $filename, 0, strrpos( $filename, '.' ) );

	$html = sprintf(
		'<input type="hidden" name="_wpcf7_captcha_challenge_%1$s" value="%2$s" /><img title="キャプチャ文字はひらがな" %3$s />',
		$tag->name, esc_attr( $prefix ), $atts );

	return $html;
}
