<?php
/**
 * Common Functions
 *
 * Loads the base classes and executes the request.
 *
 * @package		Helpers
 * @subpackage	File Helper Functions
 * @category	Core Functions
 * @author		Analitica Innovare Dev Team
 */

if ( ! function_exists('read_file'))
{
	/**
	 * Read File
	 *
	 * Opens the file specified in the path and returns it as a string.
	 *
	 * @todo	Remove in version 3.1+.
	 * @deprecated	3.0.0	It is now just an alias for PHP's native file_get_contents().
	 * @param	string	$file	Path to file
	 * @return	string	File contents
	 */
	function read_file($file)
	{
		return @file_get_contents($file);
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('write_file'))
{
	/**
	 * Write File
	 *
	 * Writes data to the file specified in the path.
	 * Creates a new file if non-existent.
	 *
	 * @param	string	$path	File path
	 * @param	string	$data	Data to write
	 * @param	string	$mode	fopen() mode (default: 'wb')
	 * @return	bool
	 */
	function write_file($path, $data, $mode = 'wb')
	{
		if ( ! $fp = @fopen($path, $mode))
		{
			return FALSE;
		}

		flock($fp, LOCK_EX);

		for ($result = $written = 0, $length = strlen($data); $written < $length; $written += $result)
		{
			if (($result = fwrite($fp, substr($data, $written))) === FALSE)
			{
				break;
			}
		}

		flock($fp, LOCK_UN);
		fclose($fp);

		return is_int($result);
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('delete_files'))
{
	/**
	 * Delete Files
	 *
	 * Deletes all files contained in the supplied directory path.
	 * Files must be writable or owned by the system in order to be deleted.
	 * If the second parameter is set to TRUE, any directories contained
	 * within the supplied base directory will be nuked as well.
	 *
	 * @param	string	$path		File path
	 * @param	bool	$del_dir	Whether to delete any directories found in the path
	 * @param	bool	$htdocs		Whether to skip deleting .htaccess and index page files
	 * @param	int	$_level		Current directory depth level (default: 0; internal use only)
	 * @return	bool
	 */
	function delete_files($path, $del_dir = FALSE, $htdocs = FALSE, $_level = 0)
	{
		// Trim the trailing slash
		$path = rtrim($path, '/\\');

		if ( ! $current_dir = @opendir($path))
		{
			return FALSE;
		}

		while (FALSE !== ($filename = @readdir($current_dir)))
		{
			if ($filename !== '.' && $filename !== '..')
			{
				$filepath = $path.DIRECTORY_SEPARATOR.$filename;

				if (is_dir($filepath) && $filename[0] !== '.' && ! is_link($filepath))
				{
					delete_files($filepath, $del_dir, $htdocs, $_level + 1);
					rmdir($filepath);
				}
				elseif ($htdocs !== TRUE OR ! preg_match('/^(\.htaccess|index\.(html|htm|php)|web\.config)$/i', $filename))
				{
					@unlink($filepath);
				}
			}
		}

		closedir($current_dir);

		return ($del_dir === TRUE && $_level > 0)
			? @rmdir($path)
			: TRUE;
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('get_filenames'))
{
	/**
	 * Get Filenames
	 *
	 * Reads the specified directory and builds an array containing the filenames.
	 * Any sub-folders contained within the specified path are read as well.
	 *
	 * @param	string	path to source
	 * @param	bool	whether to include the path as part of the filename
	 * @param	bool	internal variable to determine recursion status - do not use in calls
	 * @return	array
	 */
	function get_filenames($source_dir, $include_path = FALSE, $_recursion = FALSE)
	{
		static $_filedata = array();

		if ($fp = @opendir($source_dir))
		{
			// reset the array and make sure $source_dir has a trailing slash on the initial call
			if ($_recursion === FALSE)
			{
				$_filedata = array();
				$source_dir = rtrim(realpath($source_dir), DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR;
			}

			while (FALSE !== ($file = readdir($fp)))
			{
				if (is_dir($source_dir.$file) && $file[0] !== '.')
				{
					get_filenames($source_dir.$file.DIRECTORY_SEPARATOR, $include_path, TRUE);
				}
				elseif ($file[0] !== '.')
				{
					$_filedata[] = ($include_path === TRUE) ? $source_dir.$file : $file;
				}
			}

			closedir($fp);
			return $_filedata;
		}

		return FALSE;
	}
}

// --------------------------------------------------------------------

if ( ! function_exists('get_dir_file_info'))
{
	/**
	 * Get Directory File Information
	 *
	 * Reads the specified directory and builds an array containing the filenames,
	 * filesize, dates, and permissions
	 *
	 * Any sub-folders contained within the specified path are read as well.
	 *
	 * @param	string	path to source
	 * @param	bool	Look only at the top level directory specified?
	 * @param	bool	internal variable to determine recursion status - do not use in calls
	 * @return	array
	 */
	function get_dir_file_info($source_dir, $top_level_only = TRUE, $_recursion = FALSE)
	{
		static $_filedata = array();
		$relative_path = $source_dir;

		if ($fp = @opendir($source_dir))
		{
			// reset the array and make sure $source_dir has a trailing slash on the initial call
			if ($_recursion === FALSE)
			{
				$_filedata = array();
				$source_dir = rtrim(realpath($source_dir), DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR;
			}

			// Used to be foreach (scandir($source_dir, 1) as $file), but scandir() is simply not as fast
			while (FALSE !== ($file = readdir($fp)))
			{
				if (is_dir($source_dir.$file) && $file[0] !== '.' && $top_level_only === FALSE)
				{
					get_dir_file_info($source_dir.$file.DIRECTORY_SEPARATOR, $top_level_only, TRUE);
				}
				elseif ($file[0] !== '.')
				{
					$_filedata[$file] = get_file_info($source_dir.$file);
					$_filedata[$file]['relative_path'] = $relative_path;
				}
			}

			closedir($fp);
			return $_filedata;
		}

		return FALSE;
	}
}

// --------------------------------------------------------------------

if ( ! function_exists('get_file_info'))
{
	/**
	 * Get File Info
	 *
	 * Given a file and path, returns the name, path, size, date modified
	 * Second parameter allows you to explicitly declare what information you want returned
	 * Options are: name, server_path, size, date, readable, writable, executable, fileperms
	 * Returns FALSE if the file cannot be found.
	 *
	 * @param	string	path to file
	 * @param	mixed	array or comma separated string of information returned
	 * @return	array
	 */
	function get_file_info($file, $returned_values = array('name', 'server_path', 'size', 'date', 'clean_date', 'extension'))
	{
		if ( ! file_exists($file))
		{
			return FALSE;
		}

		if (is_string($returned_values))
		{
			$returned_values = explode(',', $returned_values);
		}

		foreach ($returned_values as $key)
		{
			switch ($key)
			{
				case 'name':
					$fileinfo['name'] = basename($file);
					break;
				case 'extension':
					$extension = strtolower(substr(strrchr($file, '.'), 1));
					$fileinfo['extension'] = $extension;
				case 'server_path':
					$fileinfo['server_path'] = $file;
					break;
				case 'size':
					$fileinfo['size'] = filesize($file);
					break;
				case 'date':
					$fileinfo['date'] = filemtime($file);
					break; 
				case 'clean_date':
					$timestamp = filemtime($file);
					$fileinfo['clean_date'] = date("l, F d Y H:iA", strftime($timestamp));
					break;
				case 'readable':
					$fileinfo['readable'] = is_readable($file);
					break;
				case 'writable':
					$fileinfo['writable'] = is_really_writable($file);
					break;
				case 'executable':
					$fileinfo['executable'] = is_executable($file);
					break;
				case 'fileperms':
					$fileinfo['fileperms'] = fileperms($file);
					break;
			}
		}

		return $fileinfo;
	}
}

// --------------------------------------------------------------------

if ( ! function_exists('get_mime_by_extension'))
{
	/**
	 * Get Mime by Extension
	 *
	 * Translates a file extension into a mime type based on config/mimes.php.
	 * Returns FALSE if it can't determine the type, or open the mime config file
	 *
	 * Note: this is NOT an accurate way of determining file mime types, and is here strictly as a convenience
	 * It should NOT be trusted, and should certainly NOT be used for security
	 *
	 * @param	string	$filename	File name
	 * @return	string
	 */
	function get_mime_by_extension($filename)
	{
		static $mimes;

		if ( ! is_array($mimes))
		{
			$mimes = get_mimes();

			if (empty($mimes))
			{
				return FALSE;
			}
		}

		$extension = strtolower(substr(strrchr($filename, '.'), 1));

		if (isset($mimes[$extension]))
		{
			return is_array($mimes[$extension])
				? current($mimes[$extension]) // Multiple mime types, just give the first one
				: $mimes[$extension];
		}

		return FALSE;
	}
}

// --------------------------------------------------------------------

if ( ! function_exists('symbolic_permissions'))
{
	/**
	 * Symbolic Permissions
	 *
	 * Takes a numeric value representing a file's permissions and returns
	 * standard symbolic notation representing that value
	 *
	 * @param	int	$perms	Permissions
	 * @return	string
	 */
	function symbolic_permissions($perms)
	{
		if (($perms & 0xC000) === 0xC000)
		{
			$symbolic = 's'; // Socket
		}
		elseif (($perms & 0xA000) === 0xA000)
		{
			$symbolic = 'l'; // Symbolic Link
		}
		elseif (($perms & 0x8000) === 0x8000)
		{
			$symbolic = '-'; // Regular
		}
		elseif (($perms & 0x6000) === 0x6000)
		{
			$symbolic = 'b'; // Block special
		}
		elseif (($perms & 0x4000) === 0x4000)
		{
			$symbolic = 'd'; // Directory
		}
		elseif (($perms & 0x2000) === 0x2000)
		{
			$symbolic = 'c'; // Character special
		}
		elseif (($perms & 0x1000) === 0x1000)
		{
			$symbolic = 'p'; // FIFO pipe
		}
		else
		{
			$symbolic = 'u'; // Unknown
		}

		// Owner
		$symbolic .= (($perms & 0x0100) ? 'r' : '-')
			.(($perms & 0x0080) ? 'w' : '-')
			.(($perms & 0x0040) ? (($perms & 0x0800) ? 's' : 'x' ) : (($perms & 0x0800) ? 'S' : '-'));

		// Group
		$symbolic .= (($perms & 0x0020) ? 'r' : '-')
			.(($perms & 0x0010) ? 'w' : '-')
			.(($perms & 0x0008) ? (($perms & 0x0400) ? 's' : 'x' ) : (($perms & 0x0400) ? 'S' : '-'));

		// World
		$symbolic .= (($perms & 0x0004) ? 'r' : '-')
			.(($perms & 0x0002) ? 'w' : '-')
			.(($perms & 0x0001) ? (($perms & 0x0200) ? 't' : 'x' ) : (($perms & 0x0200) ? 'T' : '-'));

		return $symbolic;
	}
}

// --------------------------------------------------------------------

if ( ! function_exists('octal_permissions'))
{
	/**
	 * Octal Permissions
	 *
	 * Takes a numeric value representing a file's permissions and returns
	 * a three character string representing the file's octal permissions
	 *
	 * @param	int	$perms	Permissions
	 * @return	string
	 */
	function octal_permissions($perms)
	{
		return substr(sprintf('%o', $perms), -3);
	}
}

function validate_image($theValue) {

	if(!empty($theValue)):
		$allowed = array("jpg","jpeg","gif","png");
		$extension = pathinfo($theValue, PATHINFO_EXTENSION);
		
		if(in_array(strtolower($extension), $allowed)):
			//this is a valid image
			return true;
		endif;
	endif;

}


function get_file_mime($file_ext, $array_item) {
	foreach(config_item('used_mime_types') as $mime) {
		if($file_ext == $mime[0]) {
			return $mime[$array_item];
			break;
		}
	}
}


function show_content($title = null, $file_name, $report_content, $orientation = "L", $force = false) {

    // base url
    $appName = config_item("site_name");

    // create a new object
    require "./system/libraries/pdf/tcpdf_include.php";

    // create new PDF document
    $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

	// set document information
	$pdf->SetCreator(PDF_CREATOR);
	$pdf->SetAuthor($appName);
	$pdf->SetTitle("{$appName} {$title}");
	$pdf->SetSubject('Calendar');
	$pdf->SetKeywords('myschoolgh, attendance, timetable, manager');

	$pdf->SetHeaderData(NULL);
	// set header and footer fonts
	$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
	$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

	// set default monospaced font
	$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

	// set margins
	$pdf->SetMargins(3, 3, 3);
	$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
	$pdf->SetFooterMargin(4);

	// set auto page breaks
	$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

	// set image scale factor
	$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

	// set some language-dependent strings (optional)
	if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
		require_once(dirname(__FILE__).'/lang/eng.php');
		$pdf->setLanguageArray($l);
	}

	// set font
	$pdf->SetFont('dejavusans', '', 10);

	// if the content is not an array
	if(!is_array($report_content)) {
		// add the new page
		$pdf->AddPage($orientation, "A4");
		// write the content of the page
		$pdf->writeHTML($report_content["report"] ?? $report_content, false, false, true, false, '');
	} else {
		// loop through the pages
		foreach($report_content as $content) {
			// add the new page
			$pdf->AddPage($orientation, "A4");
			// write the content of the page
			$pdf->writeHTML($content["report"] ?? $content, false, false, true, false, '');
		}
	}

	// output the HTML content
    $pdf->Output($file_name);
}

// create a function
function dompdf_show_content($title = null, $file_name, $report_content, $orientation = "landscape", $reportObj) {
		
	// instantiate and use the dompdf class
	// $dompdf = new Dompdf();
	$dompdf = (object) [];

	// get the content
	$pages_content = "<link rel=\"stylesheet\" type=\"text/css\" href=\"assets/css/dompdf.css\">";

	// if the content is not an array
	if(!is_array($report_content)) {
		// add the new page
		$pages_content .= $report_content["report"];
	} else {
		// set the counter
		$counter = count($report_content);
		$count = 0;
		// loop through the pages
		foreach($report_content as $key => $data) {
			$count++;
			// add the new page
			$pages_content .= $data["report"];
			if($count !== $counter) {
				$pages_content  .= "\n<div class=\"page_break\"></div>";
			}
		}
	}

	// load the html content
	$dompdf->loadHtml($pages_content);

	// (Optional) Setup the paper size and orientation
	$dompdf->setPaper("A4", "landscape");

	// Render the HTML as PDF
	$dompdf->render();

	// if the user wants to auto download
	$download_file = (bool) isset($_GET["auto_download"]);

	// Output the generated PDF to Browser
	$dompdf->stream("terminal_report.pdf", ["compress" => 1, "Attachment" => $download_file]);

}