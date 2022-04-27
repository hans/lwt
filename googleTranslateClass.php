<?php

/**************************************************************
Parameters - GoogleTranslate::staticTranslate($text,$sl,$tl[,$time_token = NULL[,$domain = NULL]]):
$text -> word to translate
$sl -> source language code (i.e. en,de,fr,...)
$tl -> target language code (i.e. en,de,fr,...)
    all supported language codes can be found here: https://cloud.google.com/translate/v2/using_rest#language-params
$time_token (optional) -> array() from https://translate.google.com
    if $time_token is empty, array(408254,585515986) is used
$domain (optional) -> connect to Google Domain (i.e. 'com' for  https://translate.google.com)
    if $domain is empty, a random domain will be used (the default value can be altered by changing DEFAULT_DOMAIN)
    Possible values:
        ('com.ar', 'at', 'com.au', 'be', 'com.br', 'ca', 'cat', 'ch', 'cl', 'cn', 'cz', 'de', 'dk', 'es', 'fi', 'fr', 'gr', 'com.hk', 'hr', 'hu', 'co.id', 'ie', 'co.il', 'im', 'co.in', 'it', 'jm', 'co.jp', 'co.kr', 'com.mx', 'nl', 'no', 'pl', 'pt', 'ru', 'se', 'com.sg', 'co.th', 'com.tw', 'co.uk', 'com', 'za')

Returns an array of Translations

 * **************************************************************

Usage:
require_once( 'googleTranslateClass.php' );

$translations = GoogleTranslate::staticTranslate('Hello','en','de');
if(!$translations) echo 'Error: No translation found!';
else
    foreach($translations as $transl){
        echo $transl, '<br />';
    }
 ***************************************************************/

class GoogleTranslate
{
    public $lastResult = "";
    private $langFrom;
    private $langTo;
    const DEFAULT_DOMAIN = null;//change the domain here / NULL <> random domain
    private static $gglDomain;
    private static $headers;
    //&dt=ex&dt=ld&dt=md&dt=qca&dt=rw&dt=rm&dt=ss
    private static $urlFormat = "http://translate.google.%s/translate_a/single?client=t&q=%s&hl=en&sl=%s&tl=%s&dt=t&dt=at&dt=bd&ie=UTF-8&oe=UTF-8&oc=1&otf=2&ssel=0&tsel=3&tk=%s";
    private static final function setHeaders()
    {
        self::$headers = array(
        'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
        'Accept-Language: en-US,en',
        'Connection: keep-alive',
        'Cookie: OGPC=4061130-1:',
        'DNT: 1',
        'Host: translate.google.' . self::$gglDomain,
        'Referer: https://translate.google.' . self::$gglDomain .'/',
        'User-Agent: Mozilla/5.0 (Windows NT 6.1; WOW64; rv:40.0) Gecko/20100101 Firefox/40.1'
        );
    }
    private static final function generateToken($str,$tok) 
    {
        $t = $c = isset($tok)?$tok[0]:408254;//todo floor(time()/3600);
        $x = hexdec(80000000);
        $z = 0xffffffff;
        $y = PHP_INT_SIZE==8?0xffffffff00000000:0x00000000;
        $d = array();
        $strlen = mb_strlen($str, "UTF-8");
        while ($strlen) {
            $charString = mb_substr($str, 0, 1, "UTF-8");
            $size = strlen($charString);
            for($i = 0; $i < $size; $i++){
                $d[] = ord($charString[$i]);
            }
            $str = mb_substr($str, 1, $strlen, "UTF-8");
            $strlen = mb_strlen($str, "UTF-8");
        }
        foreach ($d as $b) {
            $c += $b;
            $b = $c << 10;
            if($b & $x) { $b |= $y; 
            }
            else { $b &= $z; 
            }
            $c += $b;
            $b = (($c >> 6) & (0x03ffffff));
            $c ^= $b;
            if($c & $x) { $c |= $y; 
            }
            else { $c &= $z; 
            }
        }
        $b = $c << 3;
        if($b & $x) { $b |= $y; 
        }
        else { $b &= $z; 
        }
        $c += $b;
        $b = (($c >> 11) & (0x001fffff));
        $c ^= $b;
        $b = $c << 15;
        if($b & $x) { $b |= $y; 
        }
        else { $b &= $z; 
        }
        $c += $b;
        $c ^= isset($tok)?$tok[1]:585515986;//todo create from time() / TKK ggltrns
        $c &= $z;
        if(0 > $c) {
            $c = (($x ^ $c));
            if(5000000 > $c) { $c += 483648; 
            }
            else { $c -= 516352; 
            }
        }
        $c %= 1000000;
        return $c . '.' . ($t ^ $c);
    }
    public static final function getDomain($domain) 
    {
        $loc = array('com.ar', 'at', 'com.au', 'be', 'com.br', 'ca', 'cat', 'ch', 'cl', 'cn', 'cz', 'de', 'dk', 'es', 'fi', 'fr', 'gr', 'com.hk', 'hr', 'hu', 'co.id', 'ie', 'co.il', 'im', 'co.in', 'it', 'jm', 'co.jp', 'co.kr', 'com.mx', 'nl', 'no', 'pl', 'pt', 'ru', 'se', 'com.sg', 'co.th', 'com.tw', 'co.uk', 'com', 'za');
        if(empty($domain) || !in_array($domain, $loc, true)) {
            return $loc[mt_rand(0, count($loc) - 1)];
        }
        return $domain;
    }
    public function array_iunique($array) 
    {
        return array_intersect_key(
            $array,
            array_unique(array_map("StrToLower", $array))
        );
    }
    public function setLangFrom($lang) 
    {
        $this->langFrom = $lang;
        return $this;
    }
    public function setLangTo($lang) 
    {
        $this->langTo = $lang;
        return $this;
    }
    public static function setDomain($domain) 
    {
        self::$gglDomain = self::getDomain($domain);
        self::setHeaders();
    }
    public function __construct($from, $to, $domain=null) 
    {
        $this->setLangFrom($from)->setLangTo($to);
    }
    public static final function makeCurl($url, $cookieSet = false) 
    {
        if(is_callable('curl_init')) {
            if (!$cookieSet) {
                $cookie = tempnam(sys_get_temp_dir(), "CURLCOOKIE");
                $curl = curl_init($url);
                curl_setopt($curl, CURLOPT_COOKIEJAR, $cookie);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($curl, CURLOPT_HTTPHEADER, self::$headers);
                curl_setopt($curl, CURLOPT_ENCODING, "gzip");
                $output = curl_exec($curl);
                unset($curl);
                unlink($cookie);
                return $output;
            }
            $curl = curl_init($url);
            curl_setopt($curl, CURLOPT_COOKIEFILE, $cookie);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_HTTPHEADER, self::$headers);
            curl_setopt($curl, CURLOPT_ENCODING, "gzip");
            $output = curl_exec($curl);
            unset($curl);
        }
        else{
            $ctx = stream_context_create(array("http"=>array("method"=>"GET","header"=>implode("\r\n", self::$headers) . "\r\n")));
            $output = file_get_contents($url, false, $ctx);
        }
        return $output;
    }
    public function translate($string) 
    {
        return $this->lastResult = self::staticTranslate($string, $this->langFrom, $this->langTo);
    }
    public static function staticTranslate($string, $from, $to, $time_token = null, $domain = self::DEFAULT_DOMAIN) 
    {
        self::setDomain($domain);
        $url = sprintf(self::$urlFormat, self::$gglDomain, rawurlencode($string), $from, $to, self::generateToken($string, $time_token));
        $result = preg_replace('!([[,])(?=,)!', '$1[]', self::makeCurl($url));
        $resultArray = json_decode($result, true);
        $finalResult = [];
        if (!empty($resultArray[0])) {
            foreach ($resultArray[0] as $results) {
                $finalResult[] = $results[0];
            }
            if (!empty($resultArray[1])) {
                foreach ($resultArray[1] as $v) {
                    foreach ($v[1] as $results) {
                        $finalResult[] = $results;
                    }
                }
            }
            if (!empty($resultArray[5])) {
                foreach ($resultArray[5] as $v) {
                    if($v[0]==$string) {
                        foreach ($v[2] as $results) {
                            $finalResult[] = $results[0];
                        }
                    }
                }
            }
            return self::array_iunique($finalResult);
        }
        return false;
    }
}

?>
