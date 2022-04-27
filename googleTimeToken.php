<?php

require_once 'inc/session_utility.php';

/**
 * @return int[]|null
 *
 * @psalm-return array{0: int, 1: int}|null
 */
function getGoogleTimeToken(): ?array
{
    $val = get_first_value('select LWTValue as value from _lwtgeneral where LWTKey = "GoogleTimeToken"');
    $arr = empty($val)?array('0'):explode('.', ($val));
    if(intval($arr[0]) < (floor(time()/3600) - 100)) {//Token renewed after 100 hours
        if(is_callable('curl_init')) {
            $curl = curl_init("https://translate.google.com");
            curl_setopt($curl, CURLOPT_HTTPHEADER, array("User-Agent: Mozilla/5.0 (Windows NT 6.1; WOW64; rv:40.0) Gecko/20100101 Firefox/40.1"));
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            $g = (string) curl_exec($curl);
            curl_close($curl);
        } else {
            $ctx = stream_context_create(array("http"=>array("method"=>"GET","header"=>"User-Agent: Mozilla/5.0 (Windows NT 6.1; WOW64; rv:40.0) Gecko/20100101 Firefox/40.1\r\n")));
            $g = (string) file_get_contents("https://translate.google.com", false, $ctx);
        }
        preg_match_all(
            "/TKK=eval[^0-9]+3d([0-9-]+)[^0-9]+3d([0-9-]+)[^0-9]+([0-9]+)[^0-9]/u", 
            $g, $ma
        );
        if(isset($ma[1][0]) && isset($ma[2][0]) && isset($ma[3][0])) {
            $tok = strval($ma[3][0]) . "." . strval(intval($ma[1][0]) + intval($ma[2][0]));
            do_mysqli_query('INSERT INTO _lwtgeneral (LWTKey, LWTValue) VALUES ("GoogleTimeToken", "' . $tok . '") ON DUPLICATE KEY UPDATE LWTValue = "' . $tok . '"');
            return array(intval($ma[3][0]),intval(intval($ma[1][0]) + intval($ma[2][0])));
        }
        else { 
            return null; 
        }
    }
    else { 
        return array(intval($arr[0]),intval($arr[1])); 
    }
}
