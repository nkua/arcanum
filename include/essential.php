<?php



 function allowHosts($key) 
 {
	$api_keys = new Zend\Config\Config(require 'config/access.php');
	$REMOTE_ADDR = $_SERVER['REMOTE_ADDR'];
	$authed = false;


	foreach($api_keys->toArray() as $no => $acl) {		
           if(check_ip($REMOTE_ADDR, $acl['ip']) && $acl['key'] == $key) {
                $authed = true;
			}
	}


	if($authed == false){
			header('Content-Type: application/json');
			header('HTTP/1.0 403 Forbidden');
			echo '["Not allowed!!!"]';
			exit;
		}

//    return $authed;
     
  }
  
  
  
/*  
 public function allowHosts($hosts, $subnets) 
 {

     if( ( $this->ip_match($_SERVER['REMOTE_ADDR'], $hosts))   || ( $this->cidr_match($_SERVER['REMOTE_ADDR'], $subnets)) ) {
           return true;
     }
     else{
        header('Content-Type: application/json');
        header('HTTP/1.0 403 Forbidden');
        echo '["Who do you think you are!!!"]';
        exit;
     }  
    
  }

 public function ip_match($ip, $ip_arr)
{
   if(in_array($ip, $ip_arr))
        return true;
   
   return false;
}
  
  
public function cidr_match($ip, $cidr_arr)
{

    foreach($cidr_arr as $cidr) {

        list($subnet, $mask) = explode('/', $cidr);

        if ((ip2long($ip) & ~((1 << (32 - $mask)) - 1) ) == ip2long($subnet))
        {
            return true;
        }
    }

   return false;

}
*/


function check_ip($ip, $ip_or_subnet_allowed) {
        if(strstr($ip_or_subnet_allowed, '/')) {
            // check subnet
            return cidr_match($ip, $ip_or_subnet_allowed);
        } else {
            // simple ip address check
            if($ip == $ip_or_subnet_allowed) {
                return true;
            }
        }
        return false;
    }
    
function cidr_match($ip, $range) {
        list ($subnet, $bits) = explode('/', $range);
        $ip = ip2long($ip);
        $subnet = ip2long($subnet);
        $mask = -1 << (32 - $bits);
        $subnet &= $mask; # nb: in case the supplied subnet wasn't correctly aligned
        return ($ip & $mask) == $subnet;
    }

?>
