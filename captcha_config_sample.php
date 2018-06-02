<?php
/**==========================
* character config
* 各値設定
* FTPにてserverへアップロード後ファイル名を、
* このファイル captcha_config_sample.php
* captcha_config.php へ変更する
===========================*/

function character_config() {

	$conf = array();
	$conf['img_w'] = 120;									# キャプチャ images 幅サイズ
	$conf['img_h'] = 40;									# キャプチャ image 縦サイズ
	$conf['base_x'] = 4;									# キャプチャ 文字開始 X
	$conf['base_y'] = 27;									# キャプチャ 文字開始 Y
	$conf['font_size'] = 20;							# キャプチャ 文字サイズ
	$conf['char_length'] = 4;							# キャプチャ 表示文字数
	$conf['font_char_width'] = 26;				# キャプチャ 文字間

	$conf['bg'] = 127;										# 背景透明化する色RGB指定 ,array( 127, 127, 127 );
	$conf['fg'] = 1;											# /* Foreground (character) color of CAPTCHA image. RGB color 0-255 */ array( 0, 0, 0 );
	$conf['line_color'] = 192;						# キャプチャ ライン色
	$conf['type'] = 'png';								# Image type. 'png', 'gif' or 'jpeg' */

	$conf['mode'] = 'jp';									# ひらがなモード 英数数度 = en

	$conf['chars_en'] = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
	$conf['chars_jp'] = 'あいうえおかきくけこさしすせそたちつてとなにのひふへまみむもやゆよらりん';

/*-----------------
* キャプチャ ひらがなフォント変更
* 使うフォントの // コメントアウトを外す
-----------------*/

	if ($conf['mode'] === 'jp') {		/* Array of fonts. Randomly picked up per character */
		$conf['fonts'] = array(
//				dirname( __FILE__ ) . '/mplus-TESTFLIGHT-058/mplus-1c-hiragana-black.ttf',
				dirname( __FILE__ ) . '/mplus-TESTFLIGHT-058/mplus-1c-hiragana-bold.ttf',
//				dirname( __FILE__ ) . '/mplus-TESTFLIGHT-058/mplus-1c-hiragana-heavy.ttf',
//				dirname( __FILE__ ) . '/mplus-TESTFLIGHT-058/mplus-1c-hiragana-light.ttf',
//				dirname( __FILE__ ) . '/mplus-TESTFLIGHT-058/mplus-1c-hiragana-medium.ttf',
//				dirname( __FILE__ ) . '/mplus-TESTFLIGHT-058/mplus-1c-hiragana-regular.ttf',
//				dirname( __FILE__ ) . '/mplus-TESTFLIGHT-058/mplus-1c-hiragana-thin.ttf',

				dirname( __FILE__ ) . '/mplus-TESTFLIGHT-058/mplus-1m-hiragana-bold.ttf',
//				dirname( __FILE__ ) . '/mplus-TESTFLIGHT-058/mplus-1m-hiragana-light.ttf',
//				dirname( __FILE__ ) . '/mplus-TESTFLIGHT-058/mplus-1m-hiragana-medium.ttf',
//				dirname( __FILE__ ) . '/mplus-TESTFLIGHT-058/mplus-1m-hiragana-regular.ttf',
//				dirname( __FILE__ ) . '/mplus-TESTFLIGHT-058/mplus-1m-hiragana-thin.ttf',

				dirname( __FILE__ ) . '/mplus-TESTFLIGHT-058/mplus-1mn-hiragana-bold.ttf',
//				dirname( __FILE__ ) . '/mplus-TESTFLIGHT-058/mplus-1mn-hiragana-light.ttf',
//				dirname( __FILE__ ) . '/mplus-TESTFLIGHT-058/mplus-1mn-hiragana-medium.ttf',
//				dirname( __FILE__ ) . '/mplus-TESTFLIGHT-058/mplus-1mn-hiragana-regular.ttf',
//				dirname( __FILE__ ) . '/mplus-TESTFLIGHT-058/mplus-1mn-hiragana-thin.ttf',

//				dirname( __FILE__ ) . '/mplus-TESTFLIGHT-058/mplus-1p-hiragana-black.ttf',
//				dirname( __FILE__ ) . '/mplus-TESTFLIGHT-058/mplus-1p-hiragana-bold.ttf',
//				dirname( __FILE__ ) . '/mplus-TESTFLIGHT-058/mplus-1p-hiragana-heavy.ttf',
//				dirname( __FILE__ ) . '/mplus-TESTFLIGHT-058/mplus-1p-hiragana-light.ttf',
				dirname( __FILE__ ) . '/mplus-TESTFLIGHT-058/mplus-1p-hiragana-medium.ttf',
//				dirname( __FILE__ ) . '/mplus-TESTFLIGHT-058/mplus-1p-hiragana-regular.ttf',
//				dirname( __FILE__ ) . '/mplus-TESTFLIGHT-058/mplus-1p-hiragana-thin.ttf',

//				dirname( __FILE__ ) . '/mplus-TESTFLIGHT-058/mplus-2c-hiragana-black.ttf',
//				dirname( __FILE__ ) . '/mplus-TESTFLIGHT-058/mplus-2c-hiragana-bold.ttf',
//				dirname( __FILE__ ) . '/mplus-TESTFLIGHT-058/mplus-2c-hiragana-heavy.ttf',
//				dirname( __FILE__ ) . '/mplus-TESTFLIGHT-058/mplus-2c-hiragana-light.ttf',
//				dirname( __FILE__ ) . '/mplus-TESTFLIGHT-058/mplus-2c-hiragana-medium.ttf',
//			dirname( __FILE__ ) . '/mplus-TESTFLIGHT-058/mplus-2c-hiragana-regular.ttf',
				dirname( __FILE__ ) . '/mplus-TESTFLIGHT-058/mplus-2c-hiragana-thin.ttf',

//				dirname( __FILE__ ) . '/mplus-TESTFLIGHT-058/mplus-2m-hiragana-bold.ttf',
//				dirname( __FILE__ ) . '/mplus-TESTFLIGHT-058/mplus-2m-hiragana-light.ttf',
//				dirname( __FILE__ ) . '/mplus-TESTFLIGHT-058/mplus-2m-hiragana-medium.ttf',
//				dirname( __FILE__ ) . '/mplus-TESTFLIGHT-058/mplus-2m-hiragana-regular.ttf',
				dirname( __FILE__ ) . '/mplus-TESTFLIGHT-058/mplus-2m-hiragana-thin.ttf',

//				dirname( __FILE__ ) . '/mplus-TESTFLIGHT-058/mplus-2p-hiragana-black.ttf',
				dirname( __FILE__ ) . '/mplus-TESTFLIGHT-058/mplus-2p-hiragana-bold.ttf',
//				dirname( __FILE__ ) . '/mplus-TESTFLIGHT-058/mplus-2p-hiragana-heavy.ttf',
//				dirname( __FILE__ ) . '/mplus-TESTFLIGHT-058/mplus-2p-hiragana-light.ttf',
//				dirname( __FILE__ ) . '/mplus-TESTFLIGHT-058/mplus-2p-hiragana-medium.ttf',
//				dirname( __FILE__ ) . '/mplus-TESTFLIGHT-058/mplus-2p-hiragana-regular.ttf',
//				dirname( __FILE__ ) . '/mplus-TESTFLIGHT-058/mplus-2p-hiragana-thin.ttf',
		);
	}
/*  英数字の場合は修正なし */
	if ($conf['mode'] === 'en') {
		$conf['fonts'] = array(
			dirname( __FILE__ ) . '/gentium/GenBkBasR.ttf',
			dirname( __FILE__ ) . '/gentium/GenBkBasI.ttf',
			dirname( __FILE__ ) . '/gentium/GenBkBasBI.ttf',
			dirname( __FILE__ ) . '/gentium/GenBkBasB.ttf',
		);
	}

	return $conf;

}