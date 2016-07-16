<?php
namespace Coercive\Utility\FileUpload;

use \Exception;

/**
 * AbstractFileUpload
 * PHP Version 	5
 *
 * @version		1.1
 * @package 	Coercive\Utility\FileUpload
 * @link		@link https://github.com/Coercive/FileUpload
 *
 * @author  	Anthony Moral <contact@coercive.fr>
 * @copyright   2016 - 2017 Anthony Moral
 * @license 	http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 */
abstract class AbstractFileUpload {

	/**
	 * MOVE FILE
	 *
	 * move upload file from temp to new destination directory
	 *
	 * @param string $sDestPath
	 * @return $this
	 */
	abstract public function move($sDestPath);

	/**
	 * COPY FILE
	 *
	 * copie file to a new destination dir from temp or moved directory
	 *
	 * @param string $sDestPath
	 * @return $this
	 */
	abstract public function copy($sDestPath);

	/**
	 * SAVE FILE
	 *
	 * save file to a new destination dir from temp or moved directory
	 *
	 * @param string $sDestPath
	 * @return $this
	 */
	abstract public function save($sDestPath);

	# SYSTEM
	const DEFAULT_MAX_SIZE = 10485760; # 10Mo
	const DEFAULT_CHMOD_DIR = 0644;
	const DEFAULT_CHMOD_FILE = 0644;

	# HELP CHMOD
	const CHMOD_FULL = 0777;
	const CHMOD_OWNER_FULL = 0700;
	const CHMOD_OWNER_READ = 0400;
	const CHMOD_OWNER_WRITE = 0200;
	const CHMOD_OWNER_EXEC = 0100;
	const CHMOD_OWNER_FULL_GROUP_READ_EXEC = 0750;
	const CHMOD_OWNER_FULL_GROUP_READ_EXEC_GLOBAL_READ = 0754;
	const CHMOD_OWNER_FULL_GROUP_READ_EXEC_GLOBAL_READ_EXEC = 0755;
	const CHMOD_OWNER_READ_WRITE = 0600;
	const CHMOD_OWNER_READ_WRITE_GROUP_READ = 0640;
	const CHMOD_OWNER_READ_WRITE_GROUP_READ_GLOBAL_READ = 0644;

	# PROPERTIES
	const OPTIONS_NAME 					= 'name';
	const OPTIONS_ALLOWED_EXTENSIONS 	= 'allowed_extensions';
	const OPTIONS_DISALLOWED_EXTENSIONS = 'disallowed_extensions';
	const OPTIONS_MAX_SIZE 				= 'max-size';
	const OPTIONS_CHMOD_DIR 			= 'chmod-dir';
	const OPTIONS_CHMOD_FILE			= 'chmod-file';

	/** @var array OPTIONS */
	private $_aOptions = [];

	/** @var array List of Errors */
	private $_aErrors = [];

	/** @var string File Name */
	private $_sInputName = [];

	/** @var int Max File Size */
	private $_iMaxFileSize = 0;

	/** @var array Allowed File Extension */
	private $_aAllowedExtensions = [];

	/** @var array Disallowed File Extension */
	private $_aDisallowedExtensions = [];

	/** @var string FILE */
	private $_sFileName = '';
	private $_sFileExtension = '';
	private $_sFilePath = '';
	private $_sFileType = '';
	private $_iFileError = 0;
	private $_iFileSize = 0;
	private $_aFilePathInfo = [];

	/** @var string DEST PATH */
	private $_sDestPath = '';

	/** @var int CHMOD DIR */
	private $_iChmodDir;

	/** @var int CHMOD FILE */
	private $_iChmodFile;

	/**
	 * EXCEPTION
	 *
	 * @param string $sMessage
	 * @param int $sLine
	 * @param string $sMethod
	 * @throws Exception
	 */
	static protected function _exception($sMessage, $sLine = __LINE__, $sMethod = __METHOD__) {
		throw new Exception("$sMessage \nMethod :  $sMethod \nLine : $sLine");
	}

	/**
	 * ERROR
	 *
	 * @param string $sMessage
	 */
	protected function _setError($sMessage) {
		$this->_aErrors[] = $sMessage;
	}

	/**
	 * SET FILE
	 *
	 * @return void
	 */
	private function _setFile() {

		# SET CONTAINER
		if(empty($_FILES[$this->_sInputName])) { $this->_setError('Empty File Info'); return; }
		$aFile = $_FILES[$this->_sInputName];

		# GET $_FILES
		$this->_sFileName 	= filter_var($aFile['name'], FILTER_SANITIZE_SPECIAL_CHARS);
		$this->_sFileType 	= filter_var($aFile['type'], FILTER_SANITIZE_SPECIAL_CHARS);
		$this->_sFilePath 	= filter_var($aFile['tmp_name'], FILTER_SANITIZE_SPECIAL_CHARS);
		$this->_iFileError 	= filter_var($aFile['error'], FILTER_VALIDATE_INT);
		$this->_iFileSize 	= filter_var($aFile['size'], FILTER_VALIDATE_INT);

		# EXTENSION
		preg_match('`\.(?P<extension>[a-z]{3,4})$`i', $this->_sFileName, $aExtension);
		if(!empty($aExtension['extension'])) { $this->_sFileExtension =  $aExtension['extension']; }

		# ERROR
		if(!is_uploaded_file($this->_sFilePath)) { $this->_setError('The file does not appear to have been normally upload'); return; }
		if(!file_exists($this->_sFilePath)) { $this->_setError('File not exist'); return; }
		if(!$this->_iFileSize) { $this->_setError('File size empty'); return; }
		if($this->_iFileError) { $this->_setError('File Upload Error'); return; }

		# PATH INFO
		$this->_aFilePathInfo = pathinfo($this->_sFilePath);

	}

	/**
	 * CHECK EXTENSION
	 *
	 * @return void
	 */
	private function _checkExtension() {

		# ALLOWED
		if($this->_aAllowedExtensions && !in_array($this->_sFileExtension, $this->_aAllowedExtensions)) {
			$this->_setError('Not in allowed extensions'); return;
		}

		# DISALLOWED
		if($this->_aDisallowedExtensions && in_array($this->_sFileExtension, $this->_aDisallowedExtensions)) {
			$this->_setError('In disallowed extensions'); return;
		}

	}

	/**
	 * CHECK MAX SIZE
	 *
	 * @return void
	 */
	private function _checkMaxSize() {

		# EMPTY ? No limit
		if(!$this->_aOptions[self::OPTIONS_MAX_SIZE]) { return; }

		# TOO MUCH
		if($this->_iFileSize > $this->_aOptions[self::OPTIONS_MAX_SIZE]) {
			$this->_setError('The file is too large'); return;
		}

	}

	/**
	 * FileUpload constructor.
	 *
	 * @param array $aOptions
	 */
	public function __construct(array $aOptions) {

		# REQUIRED
		if(empty($aOptions[self::OPTIONS_NAME])) { $this->_setError('File Name is required'); return; }

		# OPTIONS
		$this->_aOptions = array_replace_recursive([
			self::OPTIONS_NAME => '',
			self::OPTIONS_ALLOWED_EXTENSIONS => [],
			self::OPTIONS_DISALLOWED_EXTENSIONS => [],
			self::OPTIONS_MAX_SIZE => self::DEFAULT_MAX_SIZE,
			self::OPTIONS_CHMOD_DIR => self::DEFAULT_CHMOD_DIR,
			self::OPTIONS_CHMOD_FILE => self::DEFAULT_CHMOD_FILE,
		], $aOptions);

		# PREPARE
		$this->_iChmodDir = (int) $this->_aOptions[self::OPTIONS_CHMOD_DIR];
		$this->_iChmodFile = (int) $this->_aOptions[self::OPTIONS_CHMOD_FILE];
		$this->_sInputName 	= filter_var($this->_aOptions[self::OPTIONS_NAME], FILTER_SANITIZE_SPECIAL_CHARS);
		$this->_iMaxFileSize = filter_var($this->_aOptions[self::OPTIONS_MAX_SIZE], FILTER_VALIDATE_INT);
		$this->_aAllowedExtensions = filter_var_array($this->_aOptions[self::OPTIONS_ALLOWED_EXTENSIONS], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
		$this->_aDisallowedExtensions = filter_var_array($this->_aOptions[self::OPTIONS_DISALLOWED_EXTENSIONS], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
		$this->_setFile();
		$this->_checkExtension();
		$this->_checkMaxSize();

	}

	/**
	 * MAKE DIRECTORY
	 *
	 * @param string $sPath
	 * @return bool
	 */
	protected function makeDir($sPath) {

		# DON'T PROCESS IF ERROR : SKIP
		if($this->getErrors()) { return false; }

		# HANDLE CRASH
		try {
			return is_dir($sPath) || mkdir($sPath, octdec($this->_iChmodDir), true);
		} catch (Exception $e) {
			return false;
		}
	}

	/**
	 * CHMOD FILE
	 *
	 * @param string $sFilePath
	 * @return bool
	 */
	protected function chmodFile($sFilePath) {

		# DON'T PROCESS IF ERROR : SKIP
		if($this->getErrors() || !is_file($sFilePath)) { return false; }

		# HANDLE CRASH
		try {
			return chmod($sFilePath, octdec($this->_iChmodFile));
		} catch (Exception $e) {
			return false;
		}
	}

	/**
	 * MOVE UPLOADED FILE
	 *
	 * @param string $sDestPath
	 * @return bool
	 */
	protected function moveUploadedFile($sDestPath) {

		# SKIP
		if($this->_aErrors) { $this->_setError('Cant move file : amont errors'); return false; }

		# SKIP
		if(!file_exists($this->_sFilePath)) { $this->_setError('Cant move file : not exist or already moved'); return false; }

		# NOT DIRECTORY : create
		if(!preg_match('`^(?P<path>.*)\/.*$`', $sDestPath, $aMatches)) { $this->_setError('Destpath match error'); return false; }
		if(!$this->makeDir($aMatches['path'])) { $this->_setError('Failure when creating directory'); return false; }

		# NOT PERMISSION TO WRITTE
		if (!is_writable($aMatches['path'])) { $this->_setError('Upload directory is not writable'); return false; }

		# MOVE
		$bStatus = move_uploaded_file($this->_sFilePath, $sDestPath);
		if(!$bStatus) { $this->_setError('Error when moving the file'); return false; }

		# CHMOD
		$bChmod = $this->chmodFile($sDestPath);
		if(!$bChmod) { $this->_setError('Error when chmod the file'); return false;	}

		# SET DESTPATH
		$this->_sDestPath = $sDestPath;
		return true;
	}

	/**
	 * COPY FILE
	 *
	 * @param string $sSrcPath
	 * @param string $sDestPath
	 * @return bool
	 */
	protected function copyFile($sSrcPath, $sDestPath) {

		# SKIP
		if($this->_aErrors) { $this->_setError('Cant copy file : amont errors'); return false; }

		# SKIP
		if(!file_exists($sSrcPath)) { $this->_setError('Cant copy file : not exist or moved'); return false; }

		# NOT DIRECTORY : create
		if(!preg_match('`^(?P<path>.*)\/.*$`', $sDestPath, $aMatches)) { $this->_setError('Destpath match error'); return false; }
		if(!$this->makeDir($aMatches['path'])) { $this->_setError('Failure when creating directory'); return false; }

		# NOT PERMISSION TO WRITTE
		if (!is_writable($aMatches['path'])) { $this->_setError('Dest directory is not writable'); return false; }

		# MOVE
		$bStatus = copy($sSrcPath, $sDestPath);
		if(!$bStatus) { $this->_setError('Error when moving the file'); return false; }

		# CHMOD
		$bChmod = $this->chmodFile($sDestPath);
		if(!$bChmod) { $this->_setError('Error when chmod the file'); return false;	}

		# SET DESTPATH
		$this->_sDestPath = $sDestPath;
		return true;
	}

	/**
	 * DELETE FILE
	 *
	 * delete file from temp directory
	 *
	 * @return bool
	 */
	public function deleteTempFile() {

		return $this->_sFilePath && file_exists($this->_sFilePath) ? unlink($this->_sFilePath) : false;

	}

	/**
	 * GETTER ERROR
	 *
	 * @return array
	 */
	public function getErrors() {
		return (array) $this->_aErrors;
	}

	/**
	 * GETTER FILE NAME
	 *
	 * @return string
	 */
	public function getFileName() {
		return (string) $this->_sFileName;
	}

	/**
	 * GETTER FILE EXTENSION
	 *
	 * @return string
	 */
	public function getFileExtension() {
		return (string) $this->_sFileExtension;
	}

	/**
	 * GET FILE PATH
	 *
	 * @return string
	 */
	public function getFilePath() {
		return (string) $this->_sFilePath;
	}

	/**
	 * GET FILE TYPE
	 *
	 * @return string
	 */
	public function getFileType() {
		return (string) $this->_sFileType;
	}

	/**
	 * GET FILE ERROR
	 *
	 * @return int
	 */
	public function getFileError() {
		return (int) $this->_iFileError;
	}

	/**
	 * GET FILE SIZE
	 *
	 * @return int
	 */
	public function getFileSize() {
		return (int) $this->_iFileSize;
	}

	/**
	 * GET MAX FILE SIZE
	 *
	 * @return int
	 */
	public function getMaxFileSize() {
		return (int) $this->_iMaxFileSize;
	}

	/**
	 * GET FILE PATH INFO
	 *
	 * @return array
	 */
	public function getFilePathInfo() {
		return (array) $this->_aFilePathInfo;
	}

	/**
	 * GET DEST PATH
	 *
	 * @return string
	 */
	public function getDestPath() {
		return $this->_sDestPath;
	}

	/**
	 * GET CHMOD DIR
	 *
	 * @return int
	 */
	public function getChmodDir() {
		return (int) $this->_iChmodDir;
	}

	/**
	 * GET CHMOD FILE
	 *
	 * @return int
	 */
	public function getChmodFile() {
		return (int) $this->_iChmodDir;
	}

}