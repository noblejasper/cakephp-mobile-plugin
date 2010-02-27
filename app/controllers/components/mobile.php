<?php
/**
 * includes
if(!class_exists('lib3gk')){
    require_once(VENDORS.'ecw'.DS.'lib3gk.php');
}
 */

define('CARRIER_UNKNOWN',  0);
define('CARRIER_DOCOMO',   1);
define('CARRIER_KDDI',     2);
define('CARRIER_SOFTBANK', 3);
define('CARRIER_EMOBILE',  4);
define('CARRIER_IPHONE',   5);
define('CARRIER_PHS',      6);

/**
 * Ktai component class for CakePHP1.2
 */
class MobileComponent extends Object {

    var $c;
    var $_carrier;
    var $_translationTable;

    var $_options = array(
        'input_encoding'          => 'UTF-8',
        'output_encoding'         => 'SJIS',
        'img_emoji_url'           => "/img/emoji/",
        'enable_ktai_session'     => true,
        'use_redirect_session_id' => false,
        'imode_session_name'      => 'csid',
        'session_save'            => 'php',
    );

    //--------------------------------------------------
    //Initialize MobileComponent
    //--------------------------------------------------
    function initialize(&$controller){
        $this->c = &$controller;

        if( isset($this->c->mobile) ){
            $this->_options  = array_merge( $this->_options, $controller->mobile );
            $this->c->mobile = &$this->_options;
        }

        if( $this->_options['enable_ktai_session'] ){
            $this->_options['session_save'] = Configure::read('Session.save');
            Configure::write('Session.save', 'mobile_session');
        }
    }

    function beforeRender() {
        if ( $this->is_mobile() && !isset($this->c->params['prefix']) ) {
            $this->c->redirect('/m' . env('REQUEST_URI'));
        }

        if ( !$this->is_mobile() &&
             isset($this->c->params['prefix']) &&
             $this->c->params['prefix'] == 'mobile'
        ){
            $this->c->redirect(preg_replace( '/^\/m/', '', env('REQUEST_URI') ));
        }

        // change layout mobile_xxxxx.ctp
        if ( $this->is_mobile() ) {
            $this->c->layout = 'mobile_'.$this->c->layout;
        }
        // view に関数セット
        $this->c->set('is_ezweb', $this->is_ezweb());
        $this->c->set('is_softbank', $this->is_softbank());
        $this->c->set('is_imode', $this->is_imode());
    }

    function inputFilter($str) {
        if (is_array($str)) {
            return array_map( array($this,"inputFilter"), $str );
        }

        // 入力された絵文字はUnicodeで保存するためSJIS-win
        $str = mb_convert_kana($str, 'KVrns', 'SJIS-win');
        $str = mb_convert_encoding($str, "UTF-8", "SJIS-win");

        $str = trim($str);
        $str = h($str);
        return $str;
    }

    // キャリアの数字を返す
    // 一番上にかいてあるdefineを参照
    function carrier() {
        if( $this->_carrier === null ) {
            $this->_carrier = $this->analyze_user_agent();
        }
        return $this->_carrier;
    }

    // UserAgent を解析してキャリアを分析
    function analyze_user_agent(){
        $carrier    = CARRIER_UNKNOWN;
        $user_agent = env('HTTP_USER_AGENT');

        //DoCoMo
        if ( strpos($user_agent, 'DoCoMo') !== false ) {
            $carrier = CARRIER_DOCOMO;
        }
        //Softbank
        elseif ( strpos($user_agent, 'SoftBank') !== false ) {
            $carrier = CARRIER_SOFTBANK;
        }
        elseif ( strpos($user_agent, 'Vodafone') !== false ) {
            $carrier = CARRIER_SOFTBANK;
        }
        elseif ( strpos($user_agent, 'J-PHONE') !== false ) {
            $carrier = CARRIER_SOFTBANK;
        }
        elseif ( strpos($user_agent, 'MOT-C980') !== false ) {
            $carrier = CARRIER_SOFTBANK;
        }
        elseif ( strpos($user_agent, 'MOT-V980') !== false ) {
            $carrier = CARRIER_SOFTBANK;
        }
        //KDDI
        elseif ( strpos($user_agent, 'KDDI-') !== false ) {
            $carrier = CARRIER_KDDI;
        }
        //EMOBILE
        elseif ( strpos($user_agent, 'emobile') !== false ) {
            $carrier = CARRIER_EMOBILE;
        //iPhone
        }
        elseif ( strpos($user_agent, 'iPhone') !== false ) {
            $carrier = CARRIER_IPHONE;
        }
        //PHS
        elseif ( strpos($user_agent, 'WILLCOM') !== false ){
            $carrier = CARRIER_PHS;
        }
        elseif ( strpos($user_agent, 'DDIPOCKET') !== false ){
            $carrier = CARRIER_PHS;
        }

        return $carrier;
    }

    function is_imode()   { return $this->carrier() == CARRIER_DOCOMO; }
    function is_softbank(){ return $this->carrier() == CARRIER_SOFTBANK || $this->is_iphone(); }
    function is_ezweb()   { return $this->carrier() == CARRIER_KDDI; }
    function is_emobile() { return $this->carrier() == CARRIER_EMOBILE; }
    function is_iphone()  { return $this->carrier() == CARRIER_IPHONE; }
    function is_mobile(){
        return  $this->is_imode()    ||
                $this->is_softbank() ||
                $this->is_ezweb()    ||
                $this->is_emobile()  ||
                $this->is_iphone();
    }

    // beforeFilterで使う
    // if ( isset($this->Mobile) && $this->Mobile->is_mobile() ) {
    //     $this->Mobile->beforeFilter();
    // }
    function beforeFilter () {
        $this->c->data = $this->inputFilter( $this->c->data );
    }

    // afterFilterで使う
    // $this->Mobile->afterFilter();
    function afterFilter () {
        $_data = $this->c->output;

        if ( $this->is_ezweb() ) {
            // KDDI
            $_data = str_replace("<html>", "<?xml version=\"1.0\" encoding=\"Shift_JIS\"?><!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\"><html xmlns=\"http://www.w3.org/1999/xhtml\">", $_data);
            $_data = str_replace("font-size:small", "font-size:12px", $_data);
        }
        elseif ( $this->is_imode() ) {
            // DoCoMo
            $_data = str_replace("<html>", "<?xml version=\"1.0\" encoding=\"Shift_JIS\"?><!DOCTYPE html PUBLIC \"-//i-mode group (ja)//DTD XHTML i-XHTML(Locale/Ver.=ja/2.0) 1.0//EN\" \"i-xhtml_4ja_10.dtd\"><html xmlns=\"http://www.w3.org/1999/xhtml\">", $_data);
            $_data = str_replace("istyle=\"1\"", "style=\"-wap-input-format:&quot;*&lt;ja:h&gt;&quot;\"", $_data);
            $_data = str_replace("istyle=\"3\"", "style=\"-wap-input-format:&quot;*&lt;ja:en&gt;&quot;\"", $_data);
            $_data = str_replace("istyle=\"4\"", "style=\"-wap-input-format:&quot;*&lt;ja:n&gt;&quot;\"", $_data);
            $_data = preg_replace("/<img src=\"(.+?)\.(gif)\"/", '<img src="\\1.png"', $_data);
        }
        elseif( $this->is_softbank() ) {
            // SoftBank
            $_data = str_replace("istyle=\"1\"", "mode=\"hiragana\"", $_data);
            $_data = str_replace("istyle=\"3\"", "mode=\"alphabet\"", $_data);
            $_data = str_replace("istyle=\"4\"", "mode=\"numeric\"", $_data);
        }

        if ( $this->is_mobile() ) {
            $_data = $this->convertMobile($_data);

            $_data = mb_convert_kana($_data, "kVrns", 'UTF-8');
            header ("Content-type: application/xhtml+xml; charset=Shift_JIS");
            $this->c->output = mb_convert_encoding($_data, "SJIS-win", "UTF-8");
        }
        else {
            $this->c->output = $this->convertPC($_data);
        }
    }

    function convertPC($text) {
        return $this->convertCharacter($text);
    }
    function convertMobile($text) {
        $text = $this->changeEmoji($text);
        return $this->convertCharacter($text);
    }

    function changeEmoji($text) {
        include_once(VENDORS.'emoji'.DS. $this->carrier() .'.php');
        if ( preg_match_all("/%%(MJ[A-Z0-9]*)%%/", $text, $key, PREG_SET_ORDER) ) {
            foreach ($key as $val) {
                $text = str_replace($val[0], $CONFIG[$val[1]], $text);
            }
        }
        return $text;
    }

    function convertCharacter($text) {
        $pattern  = '/\xEE[\x80-\xBF][\x80-\xBF]|\xEF[\x81-\x83][\x80-\xBF]/';
        $callback = array($this, '_convertCharacter');
        return preg_replace_callback($pattern, $callback, $text);
    }
    function _convertCharacter($matches) {
        if (isset($this->_translationTable) === false) {
            $this->_initTranslationTable();
        }

        $utf8 = $matches[0];
        if (isset($this->_translationTable[$utf8]) === true) {
            $sjis = $this->_translationTable[$utf8];

            if ( !$this->is_mobile() ) {
                list($width, $height) = $this->_getImageSize($sjis);
                $sjis = '<img class="emoji"'
                    . ' src="' . $this->_options['img_emoji_url'] . bin2hex($sjis) . '.gif"'
                    . ' alt=""'
                    . ' width="'  . $width  . '"'
                    . ' height="' . $height . '" />';
            }
            return $sjis;
        } else {
            if ( $this->is_mobile() ) {
                return $utf8;
            }
            else {
                // TODO:今後随時対応予定
                return '[絵]';
            }
        }
    }
    function _initTranslationTable() {
        $aliases = array(
            CARRIER_UNKNOWN   => 'pc',
            CARRIER_DOCOMO    => 'docomo',
            CARRIER_KDDI      => 'au',
            CARRIER_SOFTBANK  => 'softbank',
            CARRIER_EMOBILE   => 'docomo',
            CARRIER_IPHONE    => 'pc',
            CARRIER_PHS       => 'docomo',
        );

        $carrier = 'pc';
        if (isset($aliases[$this->carrier()])) {
            $carrier = $aliases[$this->carrier()];
        }
        $this->_translationTable = include VENDORS.'emoji'.DS. ucfirst($carrier) .'.php';
    }

    function _getImageSize($sjis) {
        $high = ord($sjis[0]);

        if ($high < 0xF0) {
            // SoftBank
            $width  = 15;
            $height = 15;
        } else if ($high === 0xF8 || $high === 0xF9) {
            // NTT docomo
            $width  = 12;
            $height = 12;
        } else {
            // au
            if ($sjis === "\xF7\xAB") {
                // blankquarter
                $width = 4;
            } else if ($sjis === "\xF7\xAA") {
                // blankhalf
                $width = 7;
            } else {
                $width = 14;
            }
            $height = 15;
        }

        return array($width, $height);
    }


    function get_utn(){
        $uid = false;
        if( $this->is_imode() ) {
            if(isset($_SERVER['HTTP_X_DCMGUID'])){
                $uid = $_SERVER['HTTP_X_DCMGUID'];
            }
        }
        elseif( $this->is_ezweb() ) {
            if(isset($_SERVER['HTTP_X_UP_SUBNO'])){
                $uid = $_SERVER['HTTP_X_UP_SUBNO'];
            }
        }
        elseif( $this->is_softbank() && !$this->is_iphone() ) {
            if(isset($_SERVER['HTTP_X_JPHONE_UID'])){
                $uid = $_SERVER['HTTP_X_JPHONE_UID'];
            }
        }
        elseif( $this->is_emobile() ) {
            if(isset($_SERVER['HTTP_X_EM_UID'])){
                $uid = $_SERVER['HTTP_X_EM_UID'];
            }
        }
        return $uid;
    }

}
