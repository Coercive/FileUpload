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

**MOD**
```php

# EXAMPLE MOD
$oFileUpload = new FileUpload([
	FileUpload::OPTIONS_NAME => 'file',
	FileUpload::OPTIONS_CHMOD_DIR => 0777, # IF DIRECTORY NOT EXIST
	FileUpload::OPTIONS_CHMOD_FILE => 0777 # WHEN USE MOVE OR COPY
]);

# YOU CAN USE INTERNAL HELPER
$oFileUpload = new FileUpload([
	[ ... ]
	FileUpload::OPTIONS_CHMOD_DIR => FileUpload::CHMOD_OWNER_WRITE,
	FileUpload::OPTIONS_CHMOD_FILE => FileUpload::CHMOD_OWNER_READ,
	[ ... ]
]);

FileUpload::CHMOD_FULL; # 0777
FileUpload::CHMOD_OWNER_FULL; # 0700
FileUpload::CHMOD_OWNER_READ; # 0400
FileUpload::CHMOD_OWNER_WRITE; # 0200
FileUpload::CHMOD_OWNER_EXEC; # 0100
FileUpload::CHMOD_OWNER_FULL_GROUP_READ_EXEC; # 0750
FileUpload::CHMOD_OWNER_FULL_GROUP_READ_EXEC_GLOBAL_READ; # 0754
FileUpload::CHMOD_OWNER_FULL_GROUP_READ_EXEC_GLOBAL_READ_EXEC; # 0755
FileUpload::CHMOD_OWNER_READ_WRITE; # 0600
FileUpload::CHMOD_OWNER_READ_WRITE_GROUP_READ; # 0640
FileUpload::CHMOD_OWNER_READ_WRITE_GROUP_READ_GLOBAL_READ; # 0644

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
...
    ->getChmodDir();
...
    ->getChmodFile();
    
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
    FileUpload::OPTIONS_CHMOD_DIR => self::DEFAULT_CHMOD_DIR, # (int) default : 0644
    FileUpload::OPTIONS_CHMOD_FILE => self::DEFAULT_CHMOD_FILE # (int) default : 0644
);
```