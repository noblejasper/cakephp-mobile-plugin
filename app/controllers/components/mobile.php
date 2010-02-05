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

    var $_carrier    = null;
    var $c = null;

    var $_options = array(
        'input_encoding'          => 'UTF-8',
        'output_encoding'         => 'SJIS',
        'img_emoji_url'           => "/img/emoticons/",
        'enable_ktai_session'     => true,
        'use_redirect_session_id' => false,
        'imode_session_name'      => 'csid',
        'session_save'            => 'php',
    );

    //--------------------------------------------------
    //Initialize ktai library
    //--------------------------------------------------
    function initialize(&$controller){
        $this->c = &$controller;

        if( isset($this->c->mobile) ){
            $this->_options     = array_merge( $this->_options, $controller->mobile );
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
        $this->c->set('is_ezweb', $this->is_ezweb());
        $this->c->set('is_softbank', $this->is_softbank());
        $this->c->set('is_imode', $this->is_imode());
    }

    function inputFilter($str) {
        if (is_array($str)) {
            return array_map( array($this,"inputFilter"), $str );
        }

        if ( $this->is_mobile() ) {
            $str = mb_convert_kana($str, 'KVrns', 'SJIS');
            $str = mb_convert_encoding($str, "UTF-8", "SJIS");
        }
        else {
            $str = mb_convert_kana($str, 'KVrns', 'UTF-8');
        }
        $str = trim($str);
        $str = h($str);
        return $str;
    }

    function carrier() {
        if( $this->_carrier === null ) {
            $this->_carrier = $this->analyze_user_agent();
        }
        return $this->_carrier;
    }

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

    function is_imode(){
        return $this->carrier() == CARRIER_DOCOMO;
    }
    function is_softbank(){
        return $this->carrier() == CARRIER_SOFTBANK || $this->is_iphone();
    }
    function is_ezweb(){
        return $this->carrier() == CARRIER_KDDI;
    }
    function is_emobile(){
        return $this->carrier() == CARRIER_EMOBILE;
    }
    function is_iphone(){
        return $this->carrier() == CARRIER_IPHONE;
    }
    function is_mobile(){
        return  $this->is_imode()    ||
                $this->is_softbank() ||
                $this->is_ezweb()    ||
                $this->is_emobile()  ||
                $this->is_iphone();
    }

    function afterFilter ($_data) {
        switch ( $this->carrier() ) {
            case CARRIER_KDDI:
                $_data = str_replace("<html>", "<?xml version=\"1.0\" encoding=\"Shift_JIS\"?>
<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">
<html xmlns=\"http://www.w3.org/1999/xhtml\">", $_data);
            break;

            case CARRIER_DOCOMO:
                $_data = str_replace("<html>", "<?xml version=\"1.0\" encoding=\"Shift_JIS\"?>
<!DOCTYPE html PUBLIC \"-//i-mode group (ja)//DTD XHTML i-XHTML(Locale/Ver.=ja/2.0) 1.0//EN\" \"i-xhtml_4ja_10.dtd\">
<html xmlns=\"http://www.w3.org/1999/xhtml\">", $_data);
                $_data = str_replace("istyle=\"1\"", "style=\"-wap-input-format:&quot;*&lt;ja:h&gt;&quot;\"", $_data);
                $_data = str_replace("istyle=\"3\"", "style=\"-wap-input-format:&quot;*&lt;ja:en&gt;&quot;\"", $_data);
                $_data = str_replace("istyle=\"4\"", "style=\"-wap-input-format:&quot;*&lt;ja:n&gt;&quot;\"", $_data);
            break;

            case CARRIER_SOFTBANK:
                $_data = str_replace("istyle=\"1\"", "mode=\"hiragana\"", $_data);
                $_data = str_replace("istyle=\"3\"", "mode=\"alphabet\"", $_data);
                $_data = str_replace("istyle=\"4\"", "mode=\"numeric\"", $_data);
            break;

            default:
                return $_data;
            break;
        }

        //絵文字変換
        include_once(VENDORS.'emoji'.DS. $this->carrier() .'.php');
        if ( preg_match_all("/%%(MJ[A-Z0-9]*)%%/", $_data, $key, PREG_SET_ORDER) ) {
            foreach ($key as $val) {
                $_data = str_replace($val[0], $CONFIG[$val[1]], $_data);
            }
        }

        //画像変換
        if ($this->is_imode()) {
            $_data = preg_replace("/<img src=\"(.+?)\.(gif)\"/", '<img src="\\1.png"', $_data);
        }
        if ($this->is_ezweb()) {
            $_data = str_replace("font-size:small", "font-size:12px", $_data);
        }

        $_data = mb_convert_kana($_data, "kVrns", 'UTF-8');
        header ("Content-type: application/xhtml+xml; charset=Shift_JIS");
        return mb_convert_encoding($_data, "SJIS", "UTF-8");
    }

}
