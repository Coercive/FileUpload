<?php
namespace Coercive\Utility\FileUpload;

/**
 * FileUpload
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
class FileUpload extends AbstractFileUpload {

	/**
	 * @inheritdoc
	 * @see Util\FileUpload\AbstractFileUpload::move
	 */
	public function move($sDestPath) {

		# Move
		$this->moveUploadedFile($sDestPath);

		# Maintain chainability
		return $this;

	}

	/**
	 * @inheritdoc
	 * @see Util\FileUpload\AbstractFileUpload::copy
	 */
	public function copy($sDestPath) {

		# Move
		$this->copyFile($this->getFilePath(), $sDestPath);

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