<?php

class Tool {
    
	/**
	 * Fast variable dumping.
	 */
	public static function dump($var) {
		echo "<pre>";
		var_dump($var);
		echo "</pre>";
	}
	
	/**
	 * This function is intended for common use of error logging.
	 * 
	 * @param $message: The String message you want to log.
	 * @param $level (default: info): The log level (one of debug,info,notice,warning,error,critical,alert,emergency)
	 * @param $logger (default: general): The logger to be used.
	 */
	 public static function log($message,$level='info',$loggerName="general") {
		self::dump($message); die;
	 }
	 
	 /**
	  * This function checks if the last PDO operation on the database was successful. If not, the error will be reported to the Database error log.
	  * 
	  * @param $pdo: The PDO Database connection
	  * 
	  * @return boolean: True if the last operation was successful, false otherwise.
	  */
	 public static function dbAssert(PDO $pdo) {
	 	if($pdo->errorCode()!=0) {
	 		$info = $pdo->errorInfo();
	 		self::log("Database Error ".$info[0]." (".$info[1]." [driver-spec]): ".$info[2],'error',"db");
			return false;
	 	}
		return true;
	 }
         
         /**
          * Print error, if a Database error occurred.
          * 
          * @param mixed $rvalue: The return value of PDOStatement->execute() function.
          * @param PDO $db
          * @return boolean
          */
         public static function dbStmtAssert($rvalue, PDOStatement $stmt) {
             
             if($rvalue===false) {
                
                $info = $stmt->errorInfo();
                $e = new Exception();
                self::log("Database statement created error:\nCode: ".$info[0]." DB-Code: ".$info[1]." Message: ".$info[2]."\n\n".$e,"warning",'db');
                 
                return false;
             }
             return true;
         }
        
        public static function timeSQLtoPHP($time) {
            return strtotime($time);
        }
        
        public static function timePHPtoSQL($time) {
            return date('Y-m-d H:i:s',intval($time));
        }
        
        public static function slugify($text)
        { 
          // replace non letter or digits by -
          $text = preg_replace('~[^\\pL\d]+~u', '-', $text);

          // trim
          $text = trim($text, '-');

          // transliterate
          $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

          // lowercase
          $text = strtolower($text);

          // remove unwanted characters
          $text = preg_replace('~[^-\w]+~', '', $text);

          if (empty($text))
          {
            return 'n-a';
          }

          return $text;
        }
	
}