<?php

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2011-2016 Felix Nagel <info@felixnagel.com>
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

use TYPO3\CMS\Frontend\Plugin\AbstractPlugin;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

/**
 * Plugin 'pluploadfe' for the 'pluploadfe' extension.
 *
 * @author    Felix Nagel <info@felixnagel.com>
 * @package    TYPO3
 * @subpackage    tx_pluploadfe
 */
class tx_pluploadfe_pi1 extends AbstractPlugin {

	/**
	 * @var string
	 */
	public $prefixId = 'tx_pluploadfe_pi1';

	/**
	 * @var string
	 */
	public $scriptRelPath = 'pi1/class.tx_pluploadfe_pi1.php';

	/**
	 * @var string
	 */
	public $extKey = 'pluploadfe';

	/**
	 * @var boolean
	 */
	public $pi_checkCHash = TRUE;

	/**
	 * @var int
	 */
	protected $configUid;

	/**
	 * @var int
	 */
	protected $uid;

	/**
	 * @var string
	 */
	protected $templateHtml;

	/**
	 * @var array
	 */
	protected $config;

	/**
	 * The main method of the PlugIn
	 *
	 * @param string $content : The plugin content
	 * @param array $conf : The plugin configuration
	 * @return string    The content that is displayed on the website
	 */
	public function main($content, $conf) {
		$this->conf = $conf;
		$this->pi_setPiVarDefaults();
		$this->pi_loadLL();

		// set (localized) UID
		$localizedUid = $this->cObj->data['_LOCALIZED_UID'];
		if (strlen($this->conf['uid']) > 0) {
			$this->uid = $this->conf['uid'];
		} else {
			$this->uid = intval(($localizedUid) ? $localizedUid : $this->cObj->data['uid']);
		}

		// set config record uid
		if (strlen($this->conf['configUid']) > 0) {
			$this->configUid = $this->conf['configUid'];
		} else {
			$this->configUid = intval($this->cObj->data['tx_pluploadfe_config']);
		}

		$this->getUploadConfig();
		$this->getTemplateFile();

		if ($this->checkConfig()) {
			$this->renderCode();
			$content = $this->getHtml();
		} else {
			$content = '<div style="border: 3px solid red; padding: 1em;">
			<strong>TYPO3 EXT:plupload Error</strong><br />Invalid configuration.</div>';
		}

		return $this->pi_wrapInBaseClass($content);
	}

	/**
	 * Checks config
	 *
	 * @return void
	 */
	protected function getUploadConfig() {
		$select = 'extensions';
		$table = 'tx_pluploadfe_config';
		$where = 'uid = ' . $this->configUid;
		$where .= $GLOBALS['TSFE']->sys_page->enableFields($table);

		$this->config = $this->getDatabase()->exec_SELECTgetSingleRow($select, $table, $where);
	}

	/**
	 * Checks config
	 *
	 * @return boolean
	 */
	protected function checkConfig() {
		$flag = FALSE;

		if (strlen($this->uid) > 0 &&
			strlen($this->templateHtml) > 0 &&
			intval($this->configUid) > 0 &&
			is_array($this->config) &&
			strlen($this->config['extensions']) > 0
		) {
			$flag = TRUE;
		} else {
			$this->handleError('Invalid configuration');
		}

		return $flag;
	}

	/**
	 * Function to parse the template
	 *
	 * @return void
	 */
	protected function renderCode() {
		// Extract subparts from the template
		$templateMain = $this->cObj->getSubpart($this->templateHtml, '###TEMPLATE_CODE###');

		// fill marker array
		$markerArray = $this->getDefaultMarker();
		$markerArray['###UPLOAD_FILE###'] = GeneralUtility::getIndpEnv('TYPO3_SITE_URL') .
			'index.php?eID=pluploadfe&configUid=' . $this->configUid;

		// replace markers in the template
		$content = $this->cObj->substituteMarkerArray($templateMain, $markerArray);

		$GLOBALS['TSFE']->getPageRenderer()->addJsFooterInlineCode(
			$this->prefixId . '_' . $this->uid, $content
		);
	}

	/**
	 * Function to parse the template
	 *
	 * @return string
	 */
	protected function getHtml() {
		// Extract subparts from the template
		$templateMain = $this->cObj->getSubpart($this->templateHtml, '###TEMPLATE_CONTENT###');

		// fill marker array
		$markerArray = $this->getDefaultMarker();
		$markerArray['###INFO_1###'] = $this->pi_getLL('info_1');
		$markerArray['###INFO_2###'] = $this->pi_getLL('info_2');

		// replace markers in the template
		$content = $this->cObj->substituteMarkerArray($templateMain, $markerArray);

		return $content;
	}

	/**
	 * Function to render the default marker
	 *
	 * @return array
	 */
	protected function getDefaultMarker() {
		$markerArray = array();
		$extensionsArray = GeneralUtility::trimExplode(',', $this->config['extensions'], TRUE);
		$maxFileSizeInBytes = GeneralUtility::getMaxUploadFileSize() * 1024;

		$markerArray['###UID###'] = $this->uid;
		$markerArray['###LANGUAGE###'] = $GLOBALS['TSFE']->config['config']['language'];
		$markerArray['###EXTDIR_PATH###'] = ExtensionManagementUtility::siteRelPath($this->extKey);
		$markerArray['###FILE_EXTENSIONS###'] = implode(',', $extensionsArray);
		$markerArray['###FILE_MAX_SIZE###'] = $maxFileSizeInBytes;

		return $markerArray;
	}

	/**
	 * Function to fetch the template file
	 *
	 * @return void
	 */
	protected function getTemplateFile() {
		$templateFile = (strlen(trim($this->conf['templateFile'])) > 0) ?
			trim($this->conf['templateFile']) : 'EXT:pluploadfe/res/template.html';

		// Get the template
		$this->templateHtml = $this->cObj->fileResource($templateFile);

		if (!$this->templateHtml) {
			$this->handleError('Error while fetching the template file: ' . $templateFile);
		}
	}

	/**
	 * Handles error output for frontend and TYPO3 logging
	 *
	 * @param string$msg Message to output
	 *
	 * @return void
	 */
	protected function handleError($msg) {
		// error
		GeneralUtility::sysLog($msg, $this->extKey, 3);

		// write dev log if enabled
		if ($GLOBALS['TYPO3_CONF_VARS']['SYS']['enable_DLOG']) {
			// fatal error
			GeneralUtility::devLog($msg, $this->extKey, 3);
		}
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

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/pluploadfe/pi1/class.tx_pluploadfe_pi1.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/pluploadfe/pi1/class.tx_pluploadfe_pi1.php']);
}
