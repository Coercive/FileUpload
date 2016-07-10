Coercive FileUpload Utility
===========================

FileUpload enables you to manage file shipments : moving, copying , deleting temporary files , retrieving information ...

Get
---
```
composer require coercive/fileupload
```

Usage
-----

**IMAGE**
```php
use Coercive\Utility\FileUpload;

# EXAMPLE IMAGE FILE
$oImageUpload = new ImageUpload([
	ImageUpload::OPTIONS_NAME => 'img_file',
	ImageUpload::OPTIONS_ALLOWED_EXTENSIONS => ['jpeg', 'jpg', 'gif', 'png']
]);

# ERRORS
if($oImageUpload->getErrors()) { exit; }

# SAVE : add extension auto
$oImageUpload->save('/example/path/' . $sImgName_auto_extension);
if($oImageUpload->getErrors()) { exit; }

# Where is my file ?
$sMyFile = $oImageUpload->getDestPath();
```

**FILE**
```php
use Coercive\Utility\FileUpload;

# EXAMPLE FILE
$oFileUpload = new FileUpload([
	FileUpload::OPTIONS_NAME => 'file',
	FileUpload::OPTIONS_ALLOWED_EXTENSIONS => ['pdf']
]);

# ERRORS
if($oFileUpload->getErrors()) { exit; }

# SAVE
$oFileUpload->save('/example/path/' . $sFileName . '.pdf');
if($oFileUpload->getErrors()) { exit; }
```

**HELP**
```php
use Coercive\Utility\FileUpload;

# Need Something ?
$oFileUpload
    ->getDestPath();
...
    ->getFilePathInfo();
...
    ->getMaxFileSize();
...
    ->getFileSize();
...
    ->getFileError();
...
    ->getFileType();
...
    ->getFilePath();
...
    ->getFileExtension();
...
    ->getFileName();
...
    ->getErrors();
    
# You can delete temp file by using :
$oFileUpload
    ->deleteTempFile();

```

**OPTIONS**
```php
# FileUpload / ImageUpload : Constructor Options
array(
    FileUpload::OPTIONS_NAME => '', # (string) input file name
    FileUpload::OPTIONS_ALLOWED_EXTENSIONS => [], # (array) of strings example : ['jpg', 'gif']
    FileUpload::OPTIONS_DISALLOWED_EXTENSIONS => [], # (array) of strings
    FileUpload::OPTIONS_MAX_SIZE => self::DEFAULT_MAX_SIZE, # (int) default : 10485760 (10 Mo)
);
```