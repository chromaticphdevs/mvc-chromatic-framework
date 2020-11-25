<?php

    function getApi($url)
    {
        $apiDatas = file_get_contents($url);

        if(is_null($apiDatas))
            return false;

        return json_decode($apiDatas);
    }
    /*MOVE TO CORE FUNCTIONS*/

    function view($view , $data = [])
    {
        $GLOBALS['data'] = $data;

        $view = convertDotToDS($view);

        extract($data);

        if(file_exists(VIEWS.DS.$view.'.php'))
        {
            require_once VIEWS.DS.$view.'.php';
        }else{
            die("View {$view} does not exists");
        }
    }
    /*#####################*/

    function flash_err($message)
    {
      if(is_null($message))
        $message = "SNAP! something went wrong please send this to the webmasters";
        Flash::set($message , 'danger');
    }

    function writeLog($file , $log)
    {
        $path = BASE_DIR.DS.'public'.DS.'writeable';

        if(!is_dir($path)){
            mkdir($path);
        }

        $fileName = $path.DS.$file;

        $myfile = fopen($fileName, "a") or die("Unable to open file!");

        $log = stringWrap($log , 'p');

        fwrite($myfile, $log);

        fclose($myfile);
    }


    function readWrittenLog($file)
    {
      $path = BASE_DIR.DS.'public'.DS.'writeable';

      $fileName = $path.DS.$file;

      if(!is_dir($path)){
          mkdir($path);
      }

      if(!file_exists($fileName))
        return false;
        
      $myfile = file_get_contents($fileName);
      return $myfile;
    }

    function ee($data)
    {
        header('Content-Type: application/json');
        echo json_encode($data);
    }

    function api_response($data , $status = true)
    {
        return [
            'data' => $data,
            'status' => $status
        ];
    }


    function convertDotToDS($path)
    {
        return str_replace('.' , DS , $path);
    }

    function require_multiple($PATH , array $files)
    {
        foreach($files as $file) {
            require_once($PATH.DS.$file.'.php');
        }
    }

    function return_require($PATH , $file)
    {
        $source = $PATH.DS.$file.'.php';
        if(file_exists($source))
            return require_once $source;
    }


    function amountHTML($amount)
	{
		$amountHTML = number_format($amount , 2);

		if($amount < 0) {
			return "<span style='color:red;'> <strong> {$amountHTML} </strong> </span>";
		}else{
			return "<span style='color:green'> <strong> {$amountHTML} </strong> </span>";
		}
    }

    function order_status_html($status)
    {
        switch(strtolower($status))
        {
            case 'pending':

            case 'delivered':
                return <<<EOF
                    <span class='badge badge-primary'> {$status} </span>
                EOF;
            break;

            case 'finished':
                return <<<EOF
                    <span class='badge badge-success'> {$status} </span>
                EOF;
            break;

            case 'cancelled':
                return <<<EOF
                    <span class='badge badge-danger'> {$status} </span>
                EOF;
            break;


        }
    }

    function crop_string($string , $length = 20)
    {
        if(strlen($string) > $length)
        {
            return substr($string, 0 , $length) . ' ...';
        }return $string;
    }

    function api_call($method, $url, $data = false)
    {
        $curl = curl_init();

        switch (strtoupper($method))
        {
            case "POST":
                curl_setopt($curl, CURLOPT_POST, 1);

                if ($data)
                    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
                break;
            case "PUT":
                curl_setopt($curl, CURLOPT_PUT, 1);
                break;
            default:
                if ($data)
                    $url = sprintf("%s?%s", $url, http_build_query($data));
        }

        // Optional Authentication:
        curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($curl, CURLOPT_USERPWD, "username:password");

        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

        $result = curl_exec($curl);

        return $result;
        curl_close($curl);
    }
    function base_url($args = '')
    {
      return URL.DS.$args;
    }

    function load(array $pathOrClass , $path = null)
    {
      if(is_null($path)) {
        foreach($pathOrClass as $key => $row) {
          require_once $row.'.php';
        }
      }else{
        foreach($pathOrClass as $key => $row) {
          require_once $path.DS.$row.'.php';
        }
      }
    }



    function model($model)
    {
        $model = ucfirst($model);

        if(file_exists(MODELS.DS.$model.'.php')){

            require_once MODELS.DS.$model.'.php';

            return new $model;
        }
        else{

            die($model . 'MODEL NOT FOUND');
        }
    }


    function get_required_js()
    {
      ?>
        <script type="text/javascript" src="<?php echo _path_public('js/core.js')?>"></script>
      <?php
    }

    function convertToCash($amount , $currency = null)
    {   
        $cashAmount = number_format($amount , 2);

        if(is_null($currency))
            return "PHP {$cashAmount}";
        return "{$currency} {$cashAmount}";
    }

    function auth()
    {
        return Auth::get();
    }


    function otp($data , $client)
    {
        $apiData = array_merge($data , [
            'alias'        => 'PERA-E',
            'companyName'  => 'PERA-E.com'
        ]);      
        
        return api_call('get' , $client , $data);
    }


    function checkTerms()
    {
        $cookie = Cookie::get('terms');

        if(empty($cookie)){
            return redirect('terms');
        }
    }



    function authRequired()
    {
        if(empty(Auth::get()))
            return redirect(BASECONTROLLER);
    }


    /**
     * CORE 
     */

    function LOAD_LIBRARY($path)
    {
        require_once LIBS.DS.$path;
    }


    function whoIs()
    {
       $auth = Auth::get();
       
       if(empty($auth))
        return $auth;
       return (array) $auth;
    }

    function allowedOrigin()
    {
        $allowedOrigins = [
            'http://breakthrough-e.com'
        ];
        $host = parse_url($_SERVER['HTTP_REFERER'], PHP_URL_HOST);

        dump([
            $allowedOrigins,
            $host
        ]);
        // if(substr($host, 0 - strlen($allowed_host)) == $allowed_host) {
        // // some code
        // } else {
        // // redirection
        // }
    }