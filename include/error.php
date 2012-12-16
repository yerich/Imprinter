<?php
/**
 * This error handling class handles all of the errors that PHP might throw at it.
 * It is designed to have no have no dependencies.
 * 
 * @package imprinter
 * @author rye
 */

class Error {
	/**
	 * Prints out the current list of errors and messages. Messages and errors are stored as serialized arrays 
	 * in the $_SESSION superglobal and persist in the session until cleared.
	 * @param $clear optional If true, clears the list of errors and messages [default: true]
	 * @return null
	 */
	static function printMessages($clear = true) {
		$messages = unserialize($_SESSION['message']);
		$errors = unserialize($_SESSION['error']);
		$notices = unserialize($_SESSION['notice']);
		if(is_array($messages)) {	//Print out the boxes
			echo "<div class=\"message messagebox\">";
			echo implode("<br />", $messages);
			echo "</div>";
		}
		if(is_array($notices)) {
			echo "<div class=\"notice messagebox\">";
			echo implode("<br />", $notices);
			echo "</div>";
		}
		if(is_array($errors)) {
			echo "<div class=\"error messagebox\">";
			echo implode("<br />", $errors);
			echo "</div>";
		}
		
		if($clear == true) {	//Clear the variables if told to do so
			unset($_SESSION['message']);
			unset($_SESSION['error']);
			unset($_SESSION['notice']);
		}
	}

	/**
	 * Logs a user message (green box, used for sucessful actions)
	 * @param string $messageString
	 */
	static function userMessage($messageString) {
		$messages = unserialize($_SESSION['message']);
		$messages[] = $messageString;
		$_SESSION['message'] = serialize($messages);
	}
	
	/**
	 * Logs a user notice (blue box, used for notifications)
	 * @param string $errorString
	 */
	static function userNotice($noticeString) {
		$notices = unserialize($_SESSION['notice']);
		$notices[] = $noticeString;
		$_SESSION['notice'] = serialize($notices);
	}

	/**
	 * Logs a user error (red box, used for errors)
	 * @param string $errorString
	 */
	static function userError($errorString) {
		$errors = unserialize($_SESSION['error']);
		$errors[] = $errorString;
		$_SESSION['error'] = serialize($errors);
	}
	
	/**
	 * Call this function to communicate to a user that a page cannot be found.
	 */
	static function error404() {
	    header('HTTP/1.1 404 Page Not Found');
	    printBasicLayout("Page Not Found", "<p>We can't find the page that you're looking for. You might have followed a broken link or ".
	       "typed in an incorrect URL, or the page may have been removed. Sorry about that.</p><p><a href=\"/\">Return to homepage</a></p>".
           "<p style='color: gray'>Error 404 Page Not Found</p>");
        DIE();
	}
	
	/**
	 * Returns the stack trace in a user-readable format.
	 */
	static function bt() {
	    $retstr = "Debugging Backtrace:";
	    $c = debug_backtrace();
        foreach($c as $b => $a) {
            $retstr .= "<br /><b>". basename( $a['file'] ). 
                "</b> &nbsp; <font color=\"red\">{$a['line']}</font> &nbsp; <font color=\"green\">{$a['function']} ()</font> &nbsp; -- ". dirname( $a['file'] ). "/";
        }
	   return $retstr;
    }
	
	/**
	 * This function is the callback for php's built-in error handler. Calling it will
	 * clear the output buffer and cause an error page to be displayed instead.
	 * @param int $errorNum
	 * @param string $errorString
	 * @param string $errorFile
	 * @param string $errorLine
	 * @return false
	 */
	static function phpError($errorNum, $errorString, $errorFile, $errorLine) {
		switch ($errorNum) {	//Check which type of error it is and print a message out accordingly
			case 8 :
				break;
		
			default:	//TODO: More message types
				ob_clean();	//Clean the output buffer - the page is useless now that we have some sort of error
				
				
    			$errorstr = "<p>Our server has encountered an error while trying to serve your request.</p>"
    				."<p><code>$errorString (error $errorNum) on line $errorLine of $errorFile</code></p>"
    				.Error::bt()
    				."<p>We've logged the error, and it has been reported to our web administrator. Sorry about that. "
    				."<br /><a href=\"/\">Return to homepage</a>";
                    
                //mail(ADMIN_EMAIL, "[Imprinter] Error $errorNum: ".substr($errorString, 0, 50), 
                //    "<p>An error was encountered. The full error message follows:</p><br /><br /><br />".$errorstr, 
                //    "From: server_noreply@".SHORT_URL."\r\nMIME-Version: 1.0\r\nContent-Type: text/html; charset=ISO-8859-1\r\n");
                
                if(!DEBUG_MODE)  {
                    $errorstr = "<p>Our server has encountered an error while trying to serve your request.</p>"
                    ."<p>We've logged the error, and it has been reported to our web administrator. Sorry about that. "
                    ."<br /><a href=\"/\">Return to homepage</a>";
                }
                printBasicLayout("Error", $errorstr);
                
                
				header('HTTP/1.1 500 Internal Server Error');
				ob_end_flush();
				die();
		}
		
		return false;	//Return false is required by php for error-handling functions.
	}
}
