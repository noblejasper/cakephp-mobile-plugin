<?
//交通
$CONFIG['MJTRAIN'] = "\x1B\$G>\x0F";

//電球
$CONFIG['MJHIRAMEKI'] = "\x1B\$E/\x0F";

//カメラ
$CONFIG['MJCAMERA'] = "\x1B\$G(\x0F";

//新着
$CONFIG['MJNEW'] = "\x1B\$F2\x0F";

//?
$CONFIG['MJHATENA'] = "\x1B\$G@\x0F";

//ズーム
$CONFIG['MJZOOM'] = "\x1B\$E4\x0F";

//無料
$CONFIG['MJFREE'] = "\x1B\$F6\x0F";

//時計
$CONFIG['MJCLOCK'] = "\x1B\$GF\x0F";

//ホーム
$CONFIG['MJHOME'] = "\x1B\$GV\x0F";

//ビル
$CONFIG['MJCOMPANY'] = "\x1B\$GX\x0F";

//ペン
$CONFIG['MJPENCIL'] = "\x1B\$E\"\x0F";

//注意
$CONFIG['MJATTENTION'] = "\x1B\$Fr\x0F";

//メール
$CONFIG['MJMAIL'] = "\x1B\$E#\x0F";

//右・左
$CONFIG['MJLEFT'] = "\x1B\$FU\x0F";
$CONFIG['MJRIGHT'] = "\x1B\$FT\x0F";

//数字
$CONFIG['MJ0']			= "\x1B\$FE\x0F";
$CONFIG['MJ1']			= "\x1B\$F<\x0F";
$CONFIG['MJ2']			= "\x1B\$F=\x0F";
$CONFIG['MJ3']			= "\x1B\$F>\x0F";
$CONFIG['MJ4']			= "\x1B\$F?\x0F";
$CONFIG['MJ5']			= "\x1B\$F@\x0F";
$CONFIG['MJ6']			= "\x1B\$FA\x0F";
$CONFIG['MJ7']			= "\x1B\$FB\x0F";
$CONFIG['MJ8']			= "\x1B\$FC\x0F";
$CONFIG['MJ9']			= "\x1B\$FD\x0F";
$CONFIG['MJ10']		= "\x1B\$FE\x0F";

//天気
$CONFIG['MJSUN'] = "\x1B\$Gj\x0F";
$CONFIG['MJCLOUD'] = "\x1B\$Gi\x0F";
$CONFIG['MJRAIN'] = "\x1B\$Gk\x0F";
$CONFIG['MJSNOWMAN'] = "\x1B\$Gh\x0F";
$CONFIG['MJTHUNDER'] = "\x1B\$E]\x0F";

//クリップ
$CONFIG['MJCLIP'] = pack('H10', '1B2445500F');

//人影
$CONFIG['MJUSER'] = pack('H10', '1B2447210F');

//タグ(チケット)
$CONFIG['MJTAG'] = pack('H10', '1B2445450F');

//メモ
$CONFIG['MJMEMO'] = "\x1B\$E]\x0F";

//位置情報
$CONFIG['MJGEO'] = pack('H10', '1B24456B0F');

$CONFIG['MJID'] = 'ID';
$CONFIG['MJPASSWORD'] = pack('H10', '1B2445640F');
$CONFIG['MJRETURN'] = pack('H10', '1B2446550F');

?>