<?php
    /**
     * USE HELPERS
     */

    function get_upload_asset()
    {
        return BASE_DIR.DS.'public'.DS.'assets';
    }

    function get_upload_base()
    {
        return BASE_DIR.DS.'public'.DS.'uploads';
    }

    /**
     * @param fileName $_FILES global name
     * dir path
     */
    function upload_multiple($fileName , $uploadPath = null)
    {
        if(is_null($uploadPath))
            $uploadPath = get_upload_base();

        /**
         * Upload Process
         *  */
        $arrNames    = [];
        $arrNamesOld = [];
        $hasWarnings = [];

        /*CHECK IF FILE UPLOAD IS EMPTY*/

        if($_FILES[$fileName]['name'][0] == '')
        {
            return [
                'status' => 'failed' ,
                'result' => [
                    'arrNames' => [],
                    'arrNamesOld' => [],
                    'names' => '',
                    'namesold' => ''
                ]
            ];
        }

        foreach($_FILES[$fileName]['name'] as $name => $value)
        {
			$uploadedName = $_FILES[$fileName]['name'][$name];

            $file_ext = explode('.' , $uploadedName);
            
            $file_ext = strtolower($file_ext[1]);

			$allowed_ext = array('jpeg' , 'jpg' , 'png' , 'bitmap','csv' , 'xls' ,'xlsx' , 'csv' ,'pdf','docx');

            
			if(in_array($file_ext , $allowed_ext))
			{
				$new_name = md5(rand()).'.'.$file_ext;
				$sourcePath = $_FILES[$fileName]['tmp_name'][$name];
				$targetPath = $uploadPath.DS.$new_name;


				if(!file_exists($uploadPath)){
					mkdir($uploadPath);
				}

				if(move_uploaded_file($sourcePath, $targetPath)){

					array_push($arrNames, $new_name);
					array_push($arrNamesOld, $uploadedName);
				}
			}else{
				$hasWarnings[] = "file '{$uploadedName}' not been uploaded invalid extension '{$file_ext}'";
			}
        }

        if(!empty($hasWarnings))
        {
            return [
                'status' => 'failed' ,
                'result' => [
                    'err' => $hasWarnings,
                    'images'  => $arrNamesOld
                ]
            ];
        }else {
            return [
                'status' => 'success' ,
                'result' => [
                    'arrNames'    => $arrNames,
                    'arrNamesOld' => $arrNamesOld,
                    'names'       => arr_to_str($arrNames),
                    'namesold'    => arr_to_str($arrNamesOld)
                ]
            ];
        }
    }

    function upload_image($filename , $uploadPath , $fileName = null)
    {
        $uploaderImage = new UploaderImage();

        $uploaderImage->setImage($filename)
        ->setName($fileName)
        ->setPath($uploadPath)
        ->upload();

        if(!empty($uploaderImage->getErrors()))
            return [
                'status' => 'failed' ,
                'result' => [
                    'err'  => $uploaderImage->getErrors(),
                    'name' => $uploaderImage->getName()
                ]
            ];

        return [
            'status' => 'success',
            'result' => [
                'name' => $uploaderImage->getName() ,
                'oldname' => $uploaderImage->getNameOld(),
                'extension' => $uploaderImage->getExtension(),
                'path'   => $uploaderImage->getPath()
            ]
        ];
    }

    function upload_document($filename , $uploadPath)
    {
        $uploaderDocument = new UploaderDocument();

        $uploaderDocument->setDocument($filename)
        ->setPath($uploadPath)
        ->upload();

        if(!empty($uploaderDocument->getErrors()))
            return [
                'status' => 'failed' ,
                'result' => [
                    'err'  => $uploaderDocument->getErrors(),
                    'name' => $uploaderDocument->getName()
                ]
            ];

        return [
            'status' => 'success',
            'result' => [
                'name' => $uploaderDocument->getName() ,
                'oldname' => $uploaderDocument->getNameOld(),
                'extension' => $uploaderDocument->getExtension(),
                'path'   => $uploaderDocument->getPath()
            ]
        ];
    }


    /*
    *@param option
    *possible options
    *[dimension(array) , prefix(string), maxSize]
    */
    function upload_bullet_multiple($fileName, $uploadPath , $options = [])
    {   
        //load bullet proof
        LOAD_LIBRARY('bulletproof/vendor/autoload.php');
        LOAD_LIBRARY('bulletproof/vendor/samayo/bulletproof/src/utils/func.image-resize.php');

        $isOk = true;
        /**
         * Save errors here
         */
        $errors = [];
        /**
         * Minimum size of image
         */
        $minSize = 100;
        /**
         * Maximum size of image
         */
        $maxSize = 3000000; // three million
        /**
         * Allow upload size
         */
        $allowedUploadSize = 9000000; // nine million
        /**
         * NamePREFIX
         */
        $prefix  = '';
        /**
         * default dimensions
         * Width , Height
         */
        $dimension = [12000, 12000];
        /**
         * File names
         * that uploaded to the server
         */
        $uploadedFilesName = [];
        /**
         * File names
         * that uploaded to the server including the path
         */
        $uploadedFilesWithPath = [];
        /**
         * File old names
         */
        $oldNames = [];

        $fileUploaded = $_FILES[$fileName];

        for($i = 0; $i < count($fileUploaded['name']); $i++) 
        {
            //if errors is not empty cancel upload
            if(!empty($errors)){
                break;
            }
            $fileExt = explode('.',$fileUploaded['name'][$i]);
            $fileExt = end($fileExt);
            
            $randomName = uniqid($prefix, TRUE);
            
            $fileOldName = $fileUploaded['name'][$i];

            $fileArray = array(
                "name" => $fileUploaded['name'][$i],
                "type" => $fileUploaded['type'][$i],
                "tmp_name" => $fileUploaded['tmp_name'][$i],
                "error" => $fileUploaded['error'][$i],
                "size" => $fileUploaded['size'][$i],
            );

            $image = new Bulletproof\Image($fileArray);

            $image->setLocation($uploadPath);
            $image->setName($randomName);
            $image->setSize($minSize , $image->getSize());
            $image->setDimension( $image->getWidth() , $image->getHeight() );
            $upload = $image->upload();

            
            /**
             * Push name to old names
             */
            array_push($oldNames , $fileOldName);

            //check if upload is true
            if(!$upload) 
            {
                $imageError = $image->getError();

                $error = " {$fileUploaded['name'][$i]} " . $imageError;
                array_push($errors , $error);
            }else
            {
                $newFileName = strtolower($image->getName().'.'.$image->getMime());
                $uploadFullPath = $uploadPath.DS.$newFileName;
                array_push($uploadedFilesName, $newFileName);
                array_push($uploadedFilesWithPath , $uploadFullPath);
            }
        }
        //check if there is an error then delete uploads
        if(!empty($errors)){
            $isOk = false;
            foreach($uploadedFilesWithPath as $key => $row) {
                //delete uploads
                unlink($row);
            }
            //empty uploaded file names
            $uploadedFilesName = [];
        }
        
        return [
            'status' => $isOk,
            'errors' => $errors,
            'uploads' => $uploadedFilesName,
            'oldNames' => $oldNames
        ];
    }



    function _image_resize_path($path)
    {
        $image = new Image($filePath);
        $image->resize(500 , 300);

        $path = $image->outputJPEG(PATH_UPLOAD.DS.'system_images');

        $filePath  = $image->getOutputFilename();

        $fileName = explode('\\' , $filePath);

        if(is_array($fileName))
            return end($fileName);
        return '';
    }