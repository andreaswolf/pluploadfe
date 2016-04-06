<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2011-2016 Felix Nagel <info@felixnagel.com>
 *  (c) 2016 Daniel Wagner
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

if (!defined('PATH_typo3conf')) {
	die ();
}

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\Utility\EidUtility;

/**
 * This class uploads files
 *
 * @todo translate error messages
 */
class tx_pluploadfe_upload {

	/**
	 * @var array
	 */
	private $imageTypes = array(
		'gif',
		'jpeg',
		'jpg',
		'png',
		'swf',
		'psd',
		'bmp',
		'tiff',
		'tif',
		'jpc',
		'jp2',
		'jpx',
		'jb2',
		'swc',
		'iff',
		'wbmp',
		'xbm',
		'ico'
	);

	/**
	 * @var array
	 */
	private $mimeTypes = array(
		'3dmf' => array('x-world/x-3dmf'),
		'3dm' => array('x-world/x-3dmf'),
		'7z' => array('application/x-7z-compressed', 'application/zip'),
		'avi' => array('video/x-msvideo'),
		'ai' => array('application/postscript'),
		'bin' => array('application/octet-stream', 'application/x-macbinary'),
		'bmp' => array('image/bmp'),
		'cab' => array('application/x-shockwave-flash'),
		'c' => array('text/plain'),
		'c++' => array('text/plain'),
		'class' => array('application/java'),
		'css' => array('text/css'),
		'csv' => array('text/comma-separated-values'),
		'cdr' => array('application/cdr'),
		'doc' => array('application/msword'),
		'dot' => array('application/msword'),
		'docx' => array('application/vnd.openxmlformats-officedocument.wordprocessingml.document'),
		'dotx' => array('application/vnd.openxmlformats-officedocument.wordprocessingml.template'),
		'dwg' => array('application/acad'),
		'eps' => array('application/postscript'),
		'exe' => array('application/octet-stream'),
		'gif' => array('image/gif'),
		'gz' => array('application/gzip'),
		'gtar' => array('application/x-gtar'),
		'f4v' => array('video/mp4'),
		'flv' => array('video/x-flv'),
		'fh4' => array('image/x-freehand'),
		'fh5' => array('image/x-freehand'),
		'fhc' => array('image/x-freehand'),
		'help' => array('application/x-helpfile'),
		'hlp' => array('application/x-helpfile'),
		'html' => array('text/html'),
		'htm' => array('text/html'),
		'ico' => array('image/x-icon'),
		'imap' => array('application/x-httpd-imap'),
		'inf' => array('application/inf'),
		'jpe' => array('image/jpeg'),
		'jpeg' => array('image/jpeg'),
		'jpg' => array('image/jpeg'),
		'js' => array('application/x-javascript'),
		'java' => array('text/x-java-source'),
		'latex' => array('application/x-latex'),
		'log' => array('text/plain'),
		'm3u' => array('audio/x-mpequrl'),
		'midi' => array('audio/midi'),
		'mid' => array('audio/midi'),
		'mov' => array('video/quicktime'),
		'mp3' => array('audio/mpeg'),
		'm4v' => array('video/mp4'),
		'mp4' => array('video/mp4', 'audio/mp4', 'audio/m4a'),
		'mpeg' => array('video/mpeg'),
		'mpg' => array('video/mpeg'),
		'mp2' => array('video/mpeg'),
		'ogg' => array('video/ogg', 'application/ogg', 'audio/ogg'),
		'ogm' => array('video/ogg'),
		'ogv' => array('video/ogg'),
		'odt' => array('application/vnd.oasis.opendocument.text', 'application/x-vnd.oasis.opendocument.text'),
		'odp' => array('application/vnd.oasis.opendocument.presentation'),
		'ods' => array('application/vnd.oasis.opendocument.spreadsheet'),
		'phtml' => array('application/x-httpd-php'),
		'php' => array('application/x-httpd-php'),
		'pdf' => array('application/pdf'),
		'pgp' => array('application/pgp'),
		'png' => array('image/png'),
		'pps' => array('application/mspowerpoint', 'application/vnd.ms-powerpoint'),
		'ppt' => array('application/mspowerpoint', 'application/vnd.ms-powerpoint'),
		'pptx' => array('application/vnd.openxmlformats-officedocument.presentationml.presentation'),
		'ppz' => array('application/mspowerpoint'),
		'pot' => array('application/mspowerpoint'),
		'ps' => array('application/postscript'),
		'qt' => array('video/quicktime'),
		'qd3d' => array('x-world/x-3dmf'),
		'qd3' => array('x-world/x-3dmf'),
		'qxd' => array('application/x-quark-express'),
		'rar' => array('application/x-rar-compressed'),
		'ra' => array('audio/x-realaudio'),
		'ram' => array('audio/x-pn-realaudio'),
		'rm' => array('audio/x-pn-realaudio'),
		'rtf' => array('text/rtf'),
		'spr' => array('application/x-sprite'),
		'sprite' => array('application/x-sprite'),
		'stream' => array('audio/x-qt-stream'),
		'swf' => array('application/x-shockwave-flash'),
		'svg' => array('text/xml-svg'),
		'sgml' => array('text/x-sgml'),
		'sgm' => array('text/x-sgml'),
		'tar' => array('application/x-tar'),
		'tiff' => array('image/tiff'),
		'tif' => array('image/tiff'),
		'tgz' => array('application/x-compressed'),
		'tex' => array('application/x-tex'),
		'txt' => array('text/plain'),
		'vob' => array('video/x-mpg'),
		'wav' => array('audio/x-wav'),
		'webm' => array('video/webm'),
		'wrl' => array('model/vrml', 'x-world/x-vrml'),
		'xla' => array('application/msexcel', 'application/vnd.ms-excel'),
		'xlt' => array('application/msexcel', 'application/vnd.ms-excel'),
		'xls' => array('application/msexcel', 'application/vnd.ms-excel'),
		'xlsx' => array('application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'),
		'xltx' => array('application/vnd.openxmlformats-officedocument.spreadsheetml.template'),
		'xlc' => array('application/vnd.ms-excel'),
		'xml' => array('text/xml'),
		'zip' => array('application/x-zip-compressed', 'application/x-zip', 'application/zip'),
	);

	/**
	 * @var boolean
	 */
	private $chunkedUpload = FALSE;

	/**
	 * @var string
	 */
	private $fileExtension = '';

	/**
	 * @var \TYPO3\CMS\Frontend\Authentication\FrontendUserAuthentication
	 */
	private $feUserObj = NULL;

	/**
	 * @var array
	 */
	private $config = array();

	/**
	 * @var string
	 */
	private $uploadPath = '';

	/**
	 * @var \TYPO3\CMS\Core\Resource\Folder
	 */
	protected $uploadFolder;


	/**
	 * Handles incoming upload requests
	 *
	 * @return    void
	 */
	public function main() {
		$this->setHeaderData();

		// get configuration record
		$this->config = $this->getUploadConfig();
		$this->processConfig();
		$this->checkUploadConfig();

		// check for valid FE user
		if ($this->config['feuser_required']) {
			if ($this->getFeUser()->user['username'] == '') {
				$this->sendErrorResponse('TYPO3 user session expired.', 100, \TYPO3\CMS\Core\Utility\HttpUtility::HTTP_STATUS_403);
			}
		}

		// One file or chunked?
		$this->chunkedUpload = (isset($_REQUEST['chunks']) && intval($_REQUEST['chunks']) > 1);

		// check file extension
		$this->checkFileExtension();

		// get upload path
		$this->uploadPath = $this->getUploadDir(
			$this->config['upload_path'],
			$this->getUserDirectory(),
			$this->config['obscure_dir']
		);
		$this->makeSureUploadTargetExists();

		$this->uploadFile();
	}

	/**
	 * Get FE user object
	 *
	 * @return \TYPO3\CMS\Frontend\Authentication\FrontendUserAuthentication
	 */
	protected function getFeUser() {
		if ($this->feUserObj === NULL) {
			$this->feUserObj = EidUtility::initFeUser();
		}

		return $this->feUserObj;
	}

	/**
	 * Get sub directory based upon user data
	 *
	 * @return string
	 */
	protected function getUserDirectory() {
		$record = $this->getFeUser()->user;
		$field = $this->config['feuser_field'];

		switch ($field) {
			case 'realName':
			case 'username':
				$directory = $record[$field];
				break;

			case 'uid':
			case 'pid':
				$directory = (string) $record[$field];
				break;

			case 'lastlogin':
				try {
					$date = new \DateTime('@' . $record[$field]);
					$date->setTimezone(new \DateTimeZone(date_default_timezone_get()));
					$directory = strftime('%Y%m%d-%H', $date->format('U'));
				} catch (\Exception $exception) {
					$directory = 'checkTimezone';
				}
				break;

			default:
				$directory = '';
		}

		return preg_replace('/[^0-9a-zA-Z\-\.]/', '_', $directory);
	}

	/**
	 * Set HTTP headers for no cache etc
	 *
	 * @return void
	 */
	protected function setHeaderData() {
		header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
		header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
		header('Cache-Control: no-store, no-cache, must-revalidate');
		header('Cache-Control: post-check=0, pre-check=0', FALSE);
		header('Pragma: no-cache');
		header('Content-Type: application/json');
	}

	/**
	 * Set HTTP headers for no cache etc
	 *
	 * @param $message
	 * @param int $code
	 * @param string $status The HTTP status header
	 */
	protected function sendErrorResponse($message, $code = 100, $status = \TYPO3\CMS\Core\Utility\HttpUtility::HTTP_STATUS_500) {
		$output = array(
			'jsonrpc' => '2.0',
			'error' => array(
				'code' => $code,
				'message' => $message
			),
			'id' => ''
		);

		echo json_encode($output);
		\TYPO3\CMS\Core\Utility\HttpUtility::setResponseCodeAndExit($status);
	}

	/**
	 * Gets the plugin configuration
	 *
	 * @return void
	 */
	protected function checkUploadConfig() {
		if (!count($this->config)) {
			$this->sendErrorResponse('Configuration record not found or invalid.', 100, \TYPO3\CMS\Core\Utility\HttpUtility::HTTP_STATUS_500);
		}

		if (!strlen($this->config['extensions'])) {
			$this->sendErrorResponse('Missing allowed file extension configuration.', 100, \TYPO3\CMS\Core\Utility\HttpUtility::HTTP_STATUS_500);
		}

		if (!$this->checkPath($this->config['upload_path'])) {
			$this->sendErrorResponse('Upload directory not valid.', 100, \TYPO3\CMS\Core\Utility\HttpUtility::HTTP_STATUS_500);
		}
	}

	/**
	 * Gets the plugin configuration
	 *
	 * @return array
	 */
	protected function getUploadConfig() {
		$configUid = intval(GeneralUtility::_GP('configUid'));

		// config id given?
		if (!$configUid) {
			$this->sendErrorResponse('No config record ID given.', 100, \TYPO3\CMS\Core\Utility\HttpUtility::HTTP_STATUS_400);
		}

		$select = 'upload_path, extensions, feuser_required, feuser_field, save_session, obscure_dir, check_mime';
		$table = 'tx_pluploadfe_config';
		$where = 'uid = ' . $configUid;
		$where .= ' AND deleted = 0';
		$where .= ' AND hidden = 0';
		$where .= ' AND starttime <= ' . $GLOBALS['SIM_ACCESS_TIME'];
		$where .= ' AND ( endtime = 0 OR endtime > ' . $GLOBALS['SIM_ACCESS_TIME'] . ')';

		$config = $this->getDatabase()->exec_SELECTgetSingleRow($select, $table, $where);

		return $config;
	}


	/**
	 * Process the configuration
	 *
	 * @return array
	 */
	protected function processConfig() {
		// Make sure FAL references work
		$resourceFactory = \TYPO3\CMS\Core\Resource\ResourceFactory::getInstance();
		$this->uploadFolder = $resourceFactory->retrieveFileOrFolderObject($this->config['upload_path']);

		// Make sure no user based path is added when there is no user available
		if (!$this->config['feuser_required']) {
			$this->config['feuser_field'] = '';
		}
	}

	/**
	 * Check if path is allowed and valid
	 *
	 * @param $path
	 *
	 * @return bool
	 */
	protected function checkPath($path) {
		return $this->uploadFolder instanceof \TYPO3\CMS\Core\Resource\Folder;
	}

	/**
	 * Checks file extension
	 * Script ends here when bad filename is given
	 *
	 * @todo Check for extension via config file
	 *
	 * @return void
	 */
	protected function checkFileExtension() {
		$fileName = $this->getFileName();
		$this->fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
		$extensions = GeneralUtility::trimExplode(',', $this->config['extensions'], TRUE);

		// check if file extension is allowed (configuration record)
		if (!in_array($this->fileExtension, $extensions)) {
			$this->sendErrorResponse('File extension is not allowed.', 100, \TYPO3\CMS\Core\Utility\HttpUtility::HTTP_STATUS_400);
		}

		// check if file extension is allowed on this TYPO3 installation
		if (!GeneralUtility::verifyFilenameAgainstDenyPattern($fileName)) {
			$this->sendErrorResponse('File extension is not allowed on this TYPO3 installation.', 100, \TYPO3\CMS\Core\Utility\HttpUtility::HTTP_STATUS_400);
		}
	}

	/**
	 * Gets the uploaded file name from request
	 *
	 * @return string
	 */
	protected function getFileName() {
		$filename = uniqid('file_');

		if (isset($_REQUEST['name'])) {
			$filename = $_REQUEST['name'];
		} elseif (!empty($_FILES)) {
			$filename = $_FILES['file']['name'];
		}

		return preg_replace('/[^\w\._]+/', '_', $filename);
	}

	protected function getUploadTempfile() {
		$uploadPath = $this->getSessionData('upload_path');
		if (!$uploadPath || !file_exists($uploadPath)) {
			// TODO if plupload can upload multiple files at the same time, this will fail. Check this.
			$uploadPath = GeneralUtility::tempnam('plupload');
			$this->saveDataInSession($uploadPath, 'upload_path');
		}
		return $uploadPath;
	}

	/**
	 * Checks and creates the upload directory
	 *
	 * @param \TYPO3\CMS\Core\Resource\Folder $folder
	 * @param string $subDirectory
	 * @param bool $obscure
	 *
	 * @return \TYPO3\CMS\Core\Resource\Folder
	 */
	protected function getUploadDir($folder, $subDirectory = '', $obscure = FALSE) {
		// subdirectory
		if ($subDirectory) {
			if (!$folder->hasFolder($subDirectory)) {
				$folder = $folder->createFolder($subDirectory);
			} else {
				$folder = $folder->getSubfolder($subDirectory);
			}
		}

		// obscure directory
		if ($obscure) {
			$randomFolderName = $this->getRandomDirName(20);
			if (!$folder->hasFolder($randomFolderName)) {
				$folder = $folder->createFolder($randomFolderName);
			} else {
				$folder = $folder->getSubfolder($randomFolderName);
			}
		}

		return $folder;
	}

	/**
	 * Checks if upload path exists
	 *
	 * @return void
	 */
	protected function makeSureUploadTargetExists() {
		if (file_exists($this->uploadPath)) {
			return;
		}

		// create target dir
		try {
			GeneralUtility::mkdir_deep(PATH_site, $this->uploadPath);
		} catch (\Exception $e) {
			$this->sendErrorResponse('Failed to create upload directory.', 100, \TYPO3\CMS\Core\Utility\HttpUtility::HTTP_STATUS_500);
		}
	}

	/**
	 * Handles file upload
	 *
	 * Copyright 2013, Moxiecode Systems AB
	 * Released under GPL License.
	 *
	 * License: http://www.plupload.com/license
	 * Contributing: http://www.plupload.com/contributing
	 *
	 * @return void
	 */
	protected function uploadFile() {
		// Get additional parameters
		$chunk = isset($_REQUEST['chunk']) ? intval($_REQUEST['chunk']) : 0;
		$chunks = isset($_REQUEST['chunks']) ? intval($_REQUEST['chunks']) : 0;

		// Use a temporary file path during upload, then add the file to FAL
		$uploadFilePath = $this->getUploadTempfile();

		// Open temp file
		if (!$out = @fopen($uploadFilePath, $chunks ? "ab" : "wb")) {
			$this->sendErrorResponse('Failed to open output stream.', 102);
		}

		if (!empty($_FILES)) {
			if ($_FILES["file"]["error"] || !is_uploaded_file($_FILES["file"]["tmp_name"])) {
				$this->sendErrorResponse('Failed to move uploaded file.', 103);
			}

			// Read binary input stream and append it to temp file
			if (!$in = @fopen($_FILES["file"]["tmp_name"], "rb")) {
				$this->sendErrorResponse('Failed to open input stream.', 101);
			}
		} else {
			if (!$in = @fopen("php://input", "rb")) {
				$this->sendErrorResponse('Failed to open input stream.', 101);
			}
		}

		while ($buff = fread($in, 4096)) {
			fwrite($out, $buff);
		}

		@fclose($out);
		@fclose($in);

		// Check if file has been uploaded
		if (!$chunks || $chunk == $chunks - 1) {
			// Move the file to its destination
			$uploadFolder = $this->getUploadDir($this->uploadFolder);
			$file = $this->uploadFolder->addFile($uploadFilePath, $this->getFileName(), 'changeName');
			// not required anymore: @unlink($uploadFilePath);

			// TODO make this available again
			//$this->processFile($file->getIdentifier());

			// remove temporary upload path from session
			$this->saveDataInSession(null, 'upload_path');
		}


		// Return JSON-RPC response if upload process is successfully finished
		die(json_encode(array(
			"jsonrpc" => "2.0",
			"result" => $file->getCombinedIdentifier(),
			"path" => $file->getPublicUrl(),
			"id" => $file->getUid()
		)));
	}

	/**
	 * Process uploaded file
	 *
	 * @params string $filePath
	 *
	 * @return void
	 */
	protected function processFile($filePath) {
		if ($this->config['check_mime']) {
			// if mime type is not allowed: remove file
			if (!$this->checkMimeType($this->fileExtension, $filePath)) {
				@unlink($filePath);
				$this->sendErrorResponse('File mime type is not allowed.', 100, \TYPO3\CMS\Core\Utility\HttpUtility::HTTP_STATUS_400);
			}
		}

		GeneralUtility::fixPermissions($filePath);

		if ($this->config['save_session']) {
			$this->saveFileInSession($filePath);
		}
	}

	/**
	 * Store file in session
	 *
	 * @param string $filePath
	 * @param string $key
	 *
	 * @return void
	 */
	protected function saveFileInSession($filePath, $key = 'files') {
		$currentData = $this->getSessionData($key);

		if (!is_array($currentData)) {
			$currentData = array();
		}

		$currentData[] = $filePath;

		$this->saveDataInSession($currentData, $key);
	}

	/**
	 * Store session data
	 *
	 * @param mixed $data
	 * @param string $key
	 *
	 * @return void
	 */
	protected function saveDataInSession($data, $key = 'data') {
		$this->getFeUser()->setAndSaveSessionData('tx_pluploadfe_' . $key, $data);
	}

	/**
	 * Get session data
	 *
	 * @param string $key
	 *
	 * @return mixed
	 */
	protected function getSessionData($key = 'data') {
		return $this->getFeUser()->getSessionData('tx_pluploadfe_' . $key);
	}

	/**
	 * Generate random string
	 *
	 * @param int $length
	 * 
	 * @return string
	 */
	protected function getRandomDirName($length = 10) {
		$set = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIKLMNPQRSTUVWXYZ0123456789';
		$string = '';

		for ($i = 1; $i <= $length; $i++) {
			$string .= $set[mt_rand(0, (strlen($set) - 1))];
		}

		return $string;
	}

	/**
	 * Retrieves MIME type from given file
	 *
	 * @todo Make EM check for mime type getters
	 *
	 * @param string $filePath
	 * 
	 * @return array
	 */
	protected function getMimeType($filePath) {
		if (function_exists('finfo_open')) {
			$finfo = @finfo_open(FILEINFO_MIME);
			if ($finfo) {
				$tempMime = @finfo_file($finfo, $filePath);
				finfo_close($finfo);
				if ($tempMime) {
					return $tempMime;
				}
			}
		}

		if (function_exists('mime_content_type')) {
			return mime_content_type($filePath);
		}

		if (function_exists('exec') && function_exists('escapeshellarg')) {
			if (($tempMime = trim(@exec('file -bi ' . @escapeshellarg($filePath))))) {
				return $tempMime;
			}
		}

		if (function_exists('pathinfo')) {
			if (($pathinfo = @pathinfo($filePath))) {
				if (in_array($pathinfo['extension'], $this->imageTypes) && $size = getimagesize($filePath)) {
					return $size['mime'];
				}
			}
		}

		// return default which is totally insecure
		return $_FILES['file']['type'];
	}

	/**
	 * checks mime type
	 * we alredy checked if the file extension is allowed,
	 * so we need to check if the mime type is adequate
	 *
	 * @param string $sentExt
	 * @param string $filePath
	 *
	 * @return boolean
	 */
	protected function checkMimeType($sentExt, $filePath) {
		$flag = FALSE;

		if (array_key_exists($sentExt, $this->mimeTypes)) {
			$mimeType = explode(';', $this->getMimeType($filePath));

			// check if mime type fits the given file extension
			if (in_array($mimeType[0], $this->mimeTypes[$sentExt])) {
				$flag = TRUE;
			}
		} else {
			// fallback for unusual file types
			$flag = TRUE;
		}

		return $flag;
	}

	/**
	 * Get database connection
	 *
	 * @return \TYPO3\CMS\Core\Database\DatabaseConnection
	 */
	protected function getDatabase() {
		return $GLOBALS['TYPO3_DB'];
	}

}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/pluploadfe/lib/class.tx_plupload_upload.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/pluploadfe/lib/class.tx_plupload_upload.php']);
}

if (!(TYPO3_REQUESTTYPE & TYPO3_REQUESTTYPE_FE)) {
	die ();
} else {
	$upload = GeneralUtility::makeInstance('tx_pluploadfe_upload');
	$upload->main();
}
