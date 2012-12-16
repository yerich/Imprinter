<?php
/**
 * File Upload Class - This class represents the files that have
 * been uploaded onto the server on a given request. It also
 * has a Factory class that will generate the class from the files
 * that have been uploaded in the current request.
 * 
 * @author rye
 * @package Imprinter
 */

class UploadedFiles {
	protected $files = array(); 	//The files which have been uploaded
	protected $rejects = array();	//Array of Rejected Files
	protected $saved = array();		//Array of Saved Files
	
	/**
	 * Filter Files by their Extensions and Size
	 * 
	 * @param array $extensions Empty array for all extensions
	 * @param int $maxsize The Maximum size of the file, in bytes. 0 for any size
	 * @return null
	 */
	function filterFiles(array $extensions, $maxsize = 0) {
		$maxsize = intval($maxsize);
		foreach($this->files as $key => $value) {
			//Filter by extension
			if(is_array($extensions) && count($extensions) != 0) {
				$ext = pathinfo($value['name'], PATHINFO_EXTENSION);
				if(!in_array(strtolower($ext), $extensions)) {	//File not in list of valid extensions
					$this->rejects[$key] = $value;
					$this->rejects[$key]['reason'] = "This filetype is unsupported.";
					unset($this->files[$key]);
				}
			}
			
			//Filter by maxsize
			if($maxsize > 0) {
				if($value['size'] > $maxsize) {
					$this->rejects[$key] = $value;
					$this->rejects[$key]['reason'] = "This file is too large.";
					unset($this->files[$key]);
				}
			}
			
			//Check for errors
			if($value['error'] > 0) {
				$this->rejects[$key] = $value;
				$this->rejects[$key]['reason'] = $value['error'];
				unset($this->files[$key]);
			}
		}
	}
	
	/**
	 * Saves uploaded files to the server in a given directory. Remember
	 * to use filterFiles before doing this.
	 * @param string $dir The upload directory, relative to the server index root.
	 */
	function saveUploadedFiles($dir) {
		if(!$dir)
			return false;
		
		foreach($this->files as $key => $value) {
			$value['origname'] = $value['name'];
			$value['name'] = strtolower($value['name']);
			$saveName = WEB_ROOT."/$dir/".$value['name'];
			$path_parts = pathinfo($saveName);
			$i = 2;
			while(file_exists($saveName)) {	//Check for the same name - rename the file if it exists
				$newName = $path_parts['dirname']."/".$path_parts['filename'].$i.".".$path_parts['extension'];
				if(!file_exists($newName)) {
					$saveName = $newName;
				}
				$i++;
				if($i > 100000)	//Anti-infinite loop
					return false;
			}
			
			if (move_uploaded_file($value["tmp_name"], $saveName)) {	//Move the file
				$this->saved[$key] = $value;
				$this->saved[$key]['location'] = $saveName;
				chmod($saveName, 0777);
				$path_parts = pathinfo($saveName);
				$this->saved[$key]['url'] = "/$dir".$path_parts['filename'].".".$path_parts['extension'];
				$this->saved[$key]['name'] = $path_parts['filename'];
			}
		}
	}
	
	/**
	 * Returns the array of rejected files
	 * @return array
	 */
	function getRejects() {
		return $this->rejects;
	}
	
	/**
	 * Returns the array of saved files
	 * @return array
	 */
	function getSaved() {
		return $this->saved;
	}
	
	/**
	 * Adds a file to the aray of files
	 * @param array $file - the file to add
	 * @return null
	 */
	function addFile(array $file) {
		$this->files[] = $file;
	}
}

class UploadedFilesFactory {
	/**
	 * Gets the files uploaded in the document request in the form data
	 * @return UploadedFiles
	 */
	static function getUploadedFiles() {
		$retval = new UploadedFiles;
		foreach($_FILES as $key => $value) {
			$retval->addFile($value);
		}
		return $retval;
	}
}
