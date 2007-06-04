<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
} 
// end security check
/**
 * The class importexportutils that manages 
 * the function regularly used in the IMS import export module
 * @package importexportutils
 * @category context
 * @copyright 2007, University of the Western Cape & AVOIR Project
 * @license GNU GPL
 * @version
 * @author Jarrett Jordaan
 * The process for import IMS specification content is:
 * 
 * 
 */

class importexportutils extends dbTable
{
	/**
	 * @var object $objConfig
	*/
	public $objConfig;

    	/**
     	 * The DSN to the database to import FROM
     	 * @var mixed Data Source Name of the data that you wish to import
     	 */
	public $dsn;

	/**
	 * Table name of the tables that we need to connect to
	 * @var string
	 */
	public $_tableName;

	/**
	 * The database (remote) connection object
     	 * @var object
     	 */
    	public $objDb;

    	/**
     	 * Language Object
	 * @var object
	 */
	public $objLanguage;

	/**
	 * The constructor
	 */
	public function init()
	{
		//Load Configuration class
		$this->objConf = &$this->getObject('altconfig','config');
        	//language object
        	$this->objLanguage = $this->getObject('language', 'language');
		$this->objDBContext = & $this->newObject('dbcontext', 'context');
	}


	/**
	 * Scans a specified folder and returns all file Locations
	 * @param string $dir - Location of folder to scan
	 * @param int $bool - 0 to return filenames and 1 to return file Locations
	 * @return array $file_list
	*/
	public function list_dir_files($dir, $bool) 
	{
		$file_list = '';
		$stack[] = $dir;
		while ($stack) 
		{
			$current_dir = array_pop($stack);
			if ($dh = opendir($current_dir)) 
			{
				while (($file = readdir($dh)) !== false) 
				{
                   			if ($file !== '.' AND $file !== '..') 
					{
						$current_file = "{$current_dir}/{$file}";
						if (is_file($current_file)) 
						{
							if($bool == 0)
        	                   				$file_list[] = $file;
                	          			else
                        	   				$file_list[] = "{$current_dir}/{$file}";
                       				}
						elseif (is_dir($current_file)) 
						{
                           				$stack[] = $current_file;
                       				}
                   			}
               			}
           		}
       		}
	return $file_list;
   	}

	/**
	 * Scans a specified folder and returns all folder Locations
	 * @param string $dir - Location of folder to scan
	 * @param int $bool - 0 to return folder names and 1 to return folder Locations
	 * @return array $file_list
	*/
	public function list_dir($dir, $bool) 
	{
		$dir_list = '';
		$stack[] = $dir;
		while ($stack) 
		{
           		$current_dir = array_pop($stack);
           		if ($dh = opendir($current_dir)) 
			{
               			while (($file = readdir($dh))) 
				{
                 			if ($file !== '.' AND $file !== '..' AND is_dir("{$current_dir}/$file")) 
					{
						$current_file = "{$current_dir}/{$file}";
						if($bool == "0")
							$dir_list[$file]=$file;
						else
							$dir_list[$current_file]="$current_file";
						$stack[] = $current_file;
        		           	}
               			}
			closedir($dh);
			}
		}
		return $dir_list;
	}

    	/**
     	 * Pseudo constructor method. We have not yet used the standard init() 
	 * function here, or extended dbTable, as we are not really
	 * interested in connecting to the local db with this object.
 	 * @param The name of the server to connect to (predefined) $server
	 * @return string, set DSN
	 * @access public
	 */
    	public function setup($server) 
    	{
        	switch ($server) 
		{
            		case 'localhost':
                		$this->dsn = 'mysql://root:@localhost/nextgen';

                		return $this->dsn;
                	break;

            		case 'chisimba':
                		$this->dsn = 'mysql://root:@localhost/chisimba';

                		return $this->dsn;
                	break;

			case 'fsiu':
                		$this->dsn = 'mysql://reader:reader@172.16.203.173/fsiu';

		                return $this->dsn;
                	break;

            		case 'elearn':
                		$this->dsn = 'mysql://reader:reader@172.16.203.210/nextgen';

		                return $this->dsn;
                	break;
                
            		case 'santec':
                		$this->dsn = 'mysql://reader:reader@172.16.203.173/santec';
                		
				return $this->dsn;
                	break;
                
            		case 'freecourseware':
            			$this->dsn = 'mysql://next:n3xt@172.16.203.178/ocw';

		            	return $this->dsn;
            		break;

			case '5ive':
            			$this->dsn = 'mysql://root:0h5h1t.pear@196.21.45.41/chisimba';

		            	return $this->dsn;
            		break;

			case 'pear':
		            	$this->dsn = 'mysql://root:0h5h1t.pear@196.21.45.41/chisimbademo';
	
	            		return $this->dsn;
            		break;

			case 'dfx':
            			$this->dsn = 'mysql://root:0h5h1t.pear@196.21.45.41/dfx';

		            	return $this->dsn;
			break;

        	}
    	}

	/**
	 * Return a list of currntly available servers
	 */
	public function getServers()
	{
		$serverlist[0] = "localhost/nextgen";
		$serverlist[1] = "reader@172.16.203.173/fsiu";
		$serverlist[2] = "reader@172.16.203.210/nextgen";
		$serverlist[3] = "reader@172.16.203.173/santec";
		$serverlist[4] = "n3xt@172.16.203.178/ocw";
		$serverlist[5] = "pear@196.21.45.41/chisimba";
		$serverlist[6] = "pear@196.21.45.41/chisimbademo";
		$serverlist[7] = "pear@196.21.45.41/dfx";

		return $serverlist;
	}

	/**
     	 * Build and instantiate the database object for the remote
	 *
	 * @param void
	 * @return object
	 * @access private
	 */
    	public function _dbObject() 
    	{
        	require_once 'MDB2.php';
        	//MDB2 has a factory method, so lets use it now...
        	$this->objDb = &MDB2::factory($this->dsn);
        	//Check for errors on the factory method
        	if (PEAR::isError($this->objDb)) 
		{
            		throw new customException($this->objLanguage->languageText("mod_ims_import_noconn", "contextadmin"));
        	}
        	//set the options
        	$this->objDb->setOption('portability', MDB2_PORTABILITY_FIX_CASE);
        	//load the date and iterator MDB2 Modules.
        	MDB2::loadFile('Date');
        	MDB2::loadFile('Iterator');
        	//Check for errors
        	if (PEAR::isError($this->objDb)) 
		{
            		throw new customException($this->objLanguage->languageText("mod_ims_import_noconn", "contextadmin"));
        	}

        return $this->objDb;
    	}

	/**
     	 * Method to query an arbitrarary remote table
     	 * @param string $table
     	 * @param string $filter can be full SQL Query
     	 * @return resultset
     	 * @access public
     	 */
	public function queryTable($table, $filter) 
	{
        	$this->_tableName = $table;
        	$res = $this->objDb->query($filter);
        	//set the return mode to return an associative array

        	return $res->fetchAll(MDB2_FETCHMODE_ASSOC);
    	}

	/**
	 *
	 */
	public function importDBData($dsn, $table, $query)
	{
		$this->_tableName = $table;
		$dsn1 = $this->setup($dsn);
		$this->objDb = $this->_dbObject();
		$result = $this->objDb->query($query);
        	if(PEAR::isError($result)) 
		{
            		throw new customException($result->getMessage());
            		exit;
        	}
		$resultarray = $result->fetchAll(MDB2_FETCHMODE_ASSOC);

		return $resultarray;
	}

	/**
	 * Use information to create the course
	 *
	 * @param array $newCourse - 1 dimensional array storing course data
	 * @return TRUE
	*/
	public function createCourseInChisimba($newCourse)
	{
		$createContext = $this->objDBContext->createContext($newCourse);
		//var_dump($createContext);
		if(!isset($createContext) || $createContext === FALSE)
		{
			return "courseWriteError";
		}
		$saveAboutEdit = $this->objDBContext->saveAboutEdit($newCourse);
		//var_dump($saveAboutEdit);
		if(!isset($saveAboutEdit) || $saveAboutEdit === FALSE)
		{
			return "courseWriteError";
		}

		return TRUE;
	}

	/**
	 * Retrieves all course data specific to chosen context from old database.
	 * Makes query to tbl_context in old database
	 * 
	 * @param $contextcode selected course
	 * @return TRUE - Successful execution
	*/
	function getKNGCourse($contextcode)
	{
		//Access old database
		//Set database
		$dsn = "localhost";
		//Set table
		$table = "tbl_context";
		//Set query
		$query = "SELECT * from tbl_context where contextcode = '$contextcode'";
		//Execute query on specified database and table
		$courseData = $this->importDBData($dsn, $table, $query);
		if(!isset($courseData))
		{
			return  "courseReadError";
		}
		$this->switchDatabase();
		
		return $courseData;
	}

	/**
	 * Retrieves all sub-page data specific to chosen context from old database.
	 * Makes query to tbl_context_nodes in old database
	 * 
	 * @param string $courseId - selected course id
	 * @return array $subPages - list of all subpages in context
	*/
	function getKNGCourseContent($courseId)
	{
		//Access old database
		//Set database
		$dsn = "localhost";
		//Set table
		$table = "tbl_context_nodes";
		//Set query
		$query = "SELECT * from tbl_context_nodes where tbl_context_parentnodes_id = '$courseId'";
		//Execute query on specified database and table
		$courseContent = $this->importDBData($dsn, $table, $query);
		$subPages = array();
		$i = 0;
		foreach($courseContent as $aNode)
		{
			$pageId = $aNode['id'];
			//Retrieve each sub-page
			//Set database
			$dsn = "localhost";
			//Set table
			$table = "tbl_context_page_content";
			//Set query
			$query = "SELECT * from tbl_context_page_content where id = '$pageId'";
			//Execute query on specified database and table
			$subPages[$i] = $this->importDBData($dsn, $table, $query);
			$i++;
		}
		if(!isset($courseContent))
		{
			return  "courseReadError";
		}
		$this->switchDatabase();

		return $subPages;
	}

	/**
	 * Retrieve chisimba images from database
	 *
	 * @param array $contextcode
	 * @return TRUE
	*/
	public function getChisimbaImages($contextcode)
	{
		//course images data table
		parent::init('tbl_files');


		return TRUE;
	}

	/**
	 * Retrieve chisimba html pages from database
	 *
	 * @param array $contextcode
	 * @return TRUE
	*/
	public function getChisimbaHtmls($contextcode)
	{
		//course images data table
		parent::init('tbl_context_page_content');


		return TRUE;
	}

	/**
	 * Retrieve KNG html pages from database
	 *
	 * @param array $contextcode
	 * @return TRUE
	*/
	public function getKNGHtmls($id)
	{


		return TRUE;
	}

/*
 ------------ lixlpixel recursive PHP functions -------------
 recursive_remove_directory( directory to delete, empty )
 expects path to directory and optional TRUE / FALSE to empty
 of course PHP has to have the rights to delete the directory
 you specify and all files and folders inside the directory
 ------------------------------------------------------------

 to use this function to totally remove a directory, write:
 recursive_remove_directory('path/to/directory/to/delete');

 to use this function to empty a directory, write:
 recursive_remove_directory('path/to/full_directory',TRUE);
*/
function recursive_remove_directory($directory, $empty=FALSE)
{
	// if the path has a slash at the end we remove it here
	if(substr($directory,-1) == '/')
	{
		$directory = substr($directory,0,-1);
	}

	// if the path is not valid or is not a directory ...
	if(!file_exists($directory) || !is_dir($directory))
	{
		// ... we return false and exit the function
		return FALSE;

	// ... if the path is not readable
	}elseif(!is_readable($directory))
	{
		// ... we return false and exit the function
		return FALSE;

	// ... else if the path is readable
	}else{

		// we open the directory
		$handle = opendir($directory);

		// and scan through the items inside
		while (FALSE !== ($item = readdir($handle)))
		{
			// if the filepointer is not the current directory
			// or the parent directory
			if($item != '.' && $item != '..')
			{
				// we build the new path to delete
				$path = $directory.'/'.$item;

				// if the new path is a directory
				if(is_dir($path)) 
				{
					// we call this function with the new path
					$this->recursive_remove_directory($path);

				// if the new path is a file
				}else{
					// we remove the file
					unlink($path);
				}
			}
		}
		// close the directory
		closedir($handle);

		// if the option to empty is not set to true
		if($empty == FALSE)
		{
			// try to delete the now empty directory
			if(!rmdir($directory))
			{
				// return false if not possible
				return FALSE;
			}
		}
		// return success
		return TRUE;
	}
}

	/**
	 *
	*/
	function writeKNGHtmls($contextcode, $folder = NULL)
	{
		
		
		// return success
		return TRUE;
	}

	/**
	 * Writes all images specific to context to usrfiles directory of new system (Chisimba)
	 * or to a specified folder
	 *
	 * @param string $contextcode - selected course
	 * @param string $folder - specified folder
	 * @return $imageNamesInKNG - File names of images
	*/
	function writeKNGImages($contextcode, $folder = NULL)
	{
		$contextcodeInChisimba = strtolower(str_replace(' ','_',$contextcode));
		$contextcodeInChisimba = strtolower(str_replace('$','_',$contextcode));
		//Course images
		//Get basepath
		//Path = opt/lampp/htdocs/chisimba_framework/app/usrfiles/
		$basePath = $this->objConf->getcontentBasePath();
		//Path = opt/lampp/htdocs/chisimba_framework/app/usrfiles/content/$contextcode
		$basePathNew = $basePath."content/".$contextcodeInChisimba;
		//Path = opt/lampp/htdocs/chisimba_framework/app/usrfiles/$contextcode/images
		$basePathToImages = $basePathNew."/images";
		//Path = opt/lampp/htdocs/nextgen/usrfiles/context
		$basePathKNG = "/opt/lampp/htdocs/nextgen/usrfiles/content/".$contextcode;
		//Get all directories in old system context
		$dirsInKNG = $this->list_dir($basePathKNG, 0);
		//Get all files starting in old system context
		$filesInKNG = $this->list_dir_files($basePathKNG, 0);
		//Get all files starting in root directory
		$pathToFilesInKNG = $this->list_dir_files($basePathKNG, 1);
		//Get Image Relative Locations
		$pathToImagesInKNG = $this->list_dir_files($basePathKNG."/".$dirsInKNG['images'], 1);
		//Get Image names
		$imageNamesInKNG = $this->list_dir_files($basePathKNG."/".$dirsInKNG['images'], 0);
		//Exporting KNG package to IMS specification Package
		if(isset($folder))
		{
			//Write Images to specified folder
			for($i=0;$i<count($pathToImagesInKNG);$i++)
			{
				$contentsOfFile = file_get_contents($pathToImagesInKNG[$i]);
				$newImageLocation = $folder."/".$imageNamesInKNG[$i];
				$fp = fopen($newImageLocation, 'w');
				chmod($newImageLocation, 0777);
				fwrite($fp,$contentsOfFile);
				fclose($fp);
			}
		}
		//Importing KNG package into Chisimba
		else
		{
			//Write Images to usrfiles folder in Chisimba
			for($i=0;$i<count($pathToImagesInKNG);$i++)
			{
				$contentsOfFile = file_get_contents($pathToImagesInKNG[$i]);
				$newImageLocation = $basePathToImages."/".$imageNamesInKNG[$i];
				$fp = fopen($newImageLocation, 'w');
				fwrite($fp,$contentsOfFile);
				fclose($fp);
			}
		}

		return $imageNamesInKNG;
	}

	/**
	 * NEED TO CHANGE DATABASE
	 * Dummy call to reset database to Chisimba
	 * Dont know of another way todo it
	*/
	function switchDatabase()
	{
		//Access old database
		//Set database
		$dsn = "localhost";
		//Set table
		$table = "tbl_context";
		//Set query
		$query = "SELECT * from tbl_context";
		//Execute query on specified database and table
		$dummyData = $this->importDBData("chisimba", $table, $query);
	}

	/**
	 * Gets all subpages of a course according to the level the pages are on
	 *
	 * @param string $courseId - selected course id
	 * @param string $level - depth of pages
	 * 
	*/
	function getAllPages($courseId, $level)
	{
		$server = "localhost";
		$course = $this->getParam('course');
        	//set the table
        	$tableName = "tbl_context_nodes";
        	//set up the query
        	$sql = "SELECT * from tbl_context_nodes WHERE tbl_context_parentnodes_id = '$courseId' AND pagelevel = '$level'";
		$dbData = $this->importDBData($server, $tableName, $sql);
		$this->switchDatabase();

		return $dbData;
	}

	/**
	 * Main template file for the import module
	 * Displays data and forms
	 * Written by James Scoble using code written by Wesley Nitsckie
	 * Modified by Jarrett L. Jordaan
	 */
	public function uploadTemplate()
	{
		//print "<div align='center'>";

		//Load needed display classes
		$this->loadClass('textinput', 'htmlelements');
		$this->loadClass('dropdown','htmlelements');
		$this->loadClass('label', 'htmlelements');
		$this->loadClass('button', 'htmlelements');

		//Creating the form
		$form=&$this->newObject('form','htmlelements');
    		$form->extra=' enctype="multipart/form-data" ';
    		$form->name='uploadziplocal';
    		$paramArray = array('action' => 'uploadIMS');
    		$form->setAction($this->uri($paramArray,'contextadmin'));

		//Creating the form
		$form2=&$this->newObject('form','htmlelements');
    		$form2->extra=' enctype="multipart/form-data" ';
    		$form2->name='uploadzipserver';
    		$paramArray1 = array('action' => 'uploadKNG');
    		$form2->setAction($this->uri($paramArray1,'contextadmin'));

		//Creating the form
		$form3=&$this->newObject('form','htmlelements');
    		$form3->extra=' enctype="multipart/form-data" ';
    		$form3->name='dudd';
    		$paramArray = array('action' => 'dudd');
    		$form3->setAction($this->uri($paramArray,'dudd'));

    		//File input
    		$fileInput=&$this->newObject('textinput','htmlelements');
    		$fileInput->fldType='file';
    		$fileInput->label="Upload only .zip files";
    		$fileInput->name='upload';
    		$fileInput->size=60;

    		//Submit button
    		$objElement = new button('CSV');
    		$objElement->setToSubmit();
    		$objElement->setValue($this->objLanguage->languageText("word_upload"));

		//Button
		$inpButton = $this->newObject('button','htmlelements');
		$inpButton->cssClass = 'f-submit';
		$inpButton->setValue('Import');
		$inpButton->setToSubmit();

    		//Heading
    		$objHeading1=&$this->newObject('htmlheading','htmlelements');
    		$objHeading1->str=$this->objLanguage->languageText("mod_ims_uploadheading","contextadmin");
    		$objHeading1->type=3;

    		//Heading
    		$objHeading2=&$this->newObject('htmlheading','htmlelements');
    		$objHeading2->str=$this->objLanguage->languageText("mod_ims_uploadserver","contextadmin");
    		$objHeading2->type=3;

		//Label - username
		$usernameLabel = new label("Username","username");
		//Text input - username
		$usernameTinput = new textinput("username","");
		//Label - password
		$passwordLabel = new label("Password","password");
		//Text input - password
		$passwordTinput = new textinput("password","");
		//Label - server
		$explainLabel = new label("Upload Course From Remote Server","");
		//Label - server
		$serverLabel = new label("Select Server","");
		//Dropdown - server selection
		$serverDropDown = new dropdown('server');
		//Populate Dropdown - server
		$dbData = $this->getServers();
		foreach($dbData as $dataOld)
		{
			$serverDropDown->addOption($dataOld);
		}
		//Label - server
		$courseLabel = new label("Select Course","");
		//Dropdown - course selection
		$courseDropDown = new dropdown('dropdownchoice');
		$server = $this->getParam('server');
		$server = "localhost";
		$course = $this->getParam('course');
        	//set the table
        	$tableName = "tbl_context";
        	//set up the query
        	$sql = "SELECT * from tbl_context";
		$dbData = $this->importDBData($server, $tableName, $sql);
		//Populate Dropdown course
		foreach($dbData as $dataOld)
		{
			$courseDropDown->addOption($dataOld['contextcode']);
		}
	    	//add the objects to the form
    		$form->setDisplayType(1);
		$form->addToForm($objHeading1);
    		$form->addToForm($fileInput);
    		$form->addToForm($objElement);
		//print $form->show()."\n"; 
    		//add the objects to the form2
    		$form2->setDisplayType(1);
		$form2->addToForm($objHeading2);
		$form2->addToForm($explainLabel);
		//$form2->addToForm("<br />");
		$form2->addToForm($usernameLabel);
		$form2->addToForm($usernameTinput);
		//$form2->addToForm("<br />");
		$form2->addToForm($passwordLabel);
		$form2->addToForm($passwordTinput);
		$form2->addToForm($serverLabel);
		$form2->addToForm($serverDropDown);
		$form2->addToForm($courseLabel);
		$form2->addToForm($courseDropDown);
		$form2->addToForm($inpButton);
		//print $form2->show()."\n"; 
		$this->switchDatabase();
    		//print"</div>\n";

		$form3->addToForm($form);
		$form3->addToForm($form2);

		return $form3;
	}

	/**
	 * Main template file for the export module
	 * Displays data and forms
	 *
	 */
	public function downloadTemplate()
	{
		//course data table
		parent::init('tbl_context');
		//print "<div align='center'>";

		//Load needed display classes
		$this->loadClass('textinput', 'htmlelements');
		$this->loadClass('dropdown','htmlelements');
		$this->loadClass('label', 'htmlelements');
		$this->loadClass('button', 'htmlelements');

		//Creating the form
		$form=&$this->newObject('form','htmlelements');
    		$form->extra=' enctype="multipart/form-data" ';
    		$form->name='exportziplocal';
    		$paramArray = array('action' => 'downloadChisimbaIMS');
    		$form->setAction($this->uri($paramArray,'contextadmin'));

		//Creating the form
		$form2=&$this->newObject('form','htmlelements');
    		$form2->extra=' enctype="multipart/form-data" ';
    		$form2->name='exportziplocal';
    		$paramArray = array('action' => 'downloadKNGIMS');
    		$form2->setAction($this->uri($paramArray,'contextadmin'));

		//Creating the form
		$form3=&$this->newObject('form','htmlelements');
    		$form3->extra=' enctype="multipart/form-data" ';
    		$form3->name='dudd';
    		$paramArray = array('action' => 'dudd');
    		$form3->setAction($this->uri($paramArray,'dudd'));

    		//Heading	
    		$objHeading1=&$this->newObject('htmlheading','htmlelements');
    		$objHeading1->str=$this->objLanguage->languageText("mod_new_downloadheading","contextadmin");
    		$objHeading1->type=3;

    		//Heading	
    		$objHeading2=&$this->newObject('htmlheading','htmlelements');
    		$objHeading2->str=$this->objLanguage->languageText("mod_kng_downloadheading","contextadmin");
    		$objHeading2->type=3;

		//Label - server
		$courseLabel = new label("Select Course","");
		//Dropdown - course selection
		$courseDropDown = new dropdown('dropdownchoice');
		//Retrieve all courses in Chisimba
		$courses = $this->getAll();
		//Populate Dropdown course
		foreach($courses as $course)
		{
			$courseDropDown->addOption($course['contextcode']);
		}
		//Button
		$inpButton = $this->newObject('button','htmlelements');
		$inpButton->cssClass = 'f-submit';
		$inpButton->setValue('Export');
		$inpButton->setToSubmit();

		//Label - server
		$courseLabel2 = new label("Select Course","");
		//Dropdown - course selection
		$courseDropDown2 = new dropdown('dropdownchoice');
		//Server selection
		$server = "localhost";
        	//set the table
        	$tableName = "tbl_context";
        	//set up the query
        	$sql = "SELECT * from tbl_context";
		$courses = $this->importDBData($server, $tableName, $sql);
		//Populate Dropdown course
		foreach($courses as $course)
		{
			$courseDropDown2->addOption($course['contextcode']);
		}
		//Button
		$inpButton2 = $this->newObject('button','htmlelements');
		$inpButton2->cssClass = 'f-submit';
		$inpButton2->setValue('Export');
		$inpButton2->setToSubmit();
		
		$form->addToForm($objHeading1);
		$form->addToForm($courseLabel);
		$form->addToForm($courseDropDown);
		$form->addToForm("<br />");
		$form->addToForm($inpButton);
		//print $form->show()."\n"; 
		$form2->addToForm($objHeading2);
		$form2->addToForm($courseLabel2);
		$form2->addToForm($courseDropDown2);
		$form2->addToForm("<br />");
		$form2->addToForm($inpButton2);
		//print $form2->show()."\n"; 
		$this->switchDatabase();
    		//print"</div>\n";
		$form3->addToForm($form);
		$form3->addToForm($form2);

		return $form3;
	}

}