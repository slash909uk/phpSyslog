<?php
// local, remote and papertrail compatible syslogclass
class Syslog{

  public static $hostname   = false;
  public static $port       = 514;
  public static $hostToLog  = "localhost";
  public static $embedPid   = true;
  public static $facility   = LOG_USER;

  public static function mapFacility(){
	  switch(self::$facility){
	  case LOG_USER: return 1;
	  case LOG_CRON: return 9;
	  case LOG_DAEMON: return 3;
	  case LOG_LOCAL0: return 16;
	  case LOG_LOCAL1: return 17;
	  case LOG_LOCAL2: return 18;
	  case LOG_LOCAL3: return 19;
	  case LOG_LOCAL4: return 20;
	  case LOG_LOCAL5: return 21;
	  case LOG_LOCAL6: return 22;
	  case LOG_LOCAL7: return 23;
	  // no others supported, reutrn USER
	  default: return 1;
	  }
  }

  public static function mapLevel($level){
	  switch($level){
	  case LOG_EMERGENCY: return 0;
	  case LOG_ALERT: return 1;
	  case LOG_CRITICAL: return 2;
	  case LOG_ERROR: return 3;
	  case LOG_WARN: return 4;
	  case LOG_NOTICE: return 5;
	  case LOG_INFO: return 6;
	  case LOG_DEBUG: return 7;
	  // no others supported, reutrn NOTICE
	  default: return 5;
	  }
  }

  public static function send( $message, $level = LOG_NOTICE, $component = "unknown" ){
    if( self::$embedPid ) $message = "[".getmypid()."] ".$message;
    if( self::$hostname == false ) return syslog( $level, $message );
    $sock = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
    $pri = (self::mapFacility()*8)+self::mapLevel($level); // multiplying the Facility number by 8 + adding the numeric level
    $sock = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
    foreach(explode("\n", $message) as $line) {
      $syslog_message = "<{$pri}>" . date('M d H:i:s ') . self::$hostToLog . ' ' . $component . ': ' . $message;
      socket_sendto($sock, $syslog_message, strlen($syslog_message), 0, self::$hostname, self::$port );
    }
    socket_close($sock);    
  }
}
?>
