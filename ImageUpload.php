<?php
namespace Coercive\Utility\FileUpload;

/**
 * ImageUpload
 * PHP Version 	5
 *
 * @version		1
 * @package 	Coercive\Utility\FileUpload
 * @link		@link https://github.com/Coercive/FileUpload
 *
 * @author  	Anthony Moral <contact@coercive.fr>
 * @copyright   2016 - 2017 Anthony Moral
 * @license 	http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 */
class ImageUpload extends AbstractFileUpload {

	/**
	 * IS IMAGE FILE
	 *
	 * file exist and is an image
	 *
	 * @param string $sPath [optional]
	 * @return bool
	 */
	static public function isImageFile($sPath) {

		# FILE EXIST
		if(!$sPath || !file_exists($sPath) || !is_file($sPath)) { return false; }

		# DETECT SAFELY
		if (!@getimagesize($sPath)) { return false; }

		# It's an image !
		return true;

	}

	/**
	 * ImageUpload constructor.
	 *
	 * @param array $aOptions
	 *
	 * @see Util\FileUpload\AbstractFileUpload::__construct
	 */
	public function __construct(array $aOptions) {
		parent::__construct($aOptions);

		# Not image ?
		if(!self::isImageFile($this->getFilePath())) { $this->_setError('The file is not an image'); }
	}

	/**
	 * @inheritdoc
	 * @see Util\FileUpload\AbstractFileUpload::move
	 */
	public function move($sDestPath) {

		# Move
		$this->moveUploadedFile($sDestPath . '.' . $this->getFileExtension());

		# Maintain chainability
		return $this;

	}

	/**
	 * @inheritdoc
	 * @see Util\FileUpload\AbstractFileUpload::copy
	 */
	public function copy($sDestPath) {

		# Move
		$this->copyFile($this->getFilePath(), $sDestPath . '.' . $this->getFileExtension());

		# Maintain chainability
		return $this;

	}

	/**
	 * @inheritdoc
	 * @see Util\FileUpload\AbstractFileUpload::save
	 */
	public function save($sDestPath) {

		return $this->copy($sDestPath);

	}

}