<?php
    
    function strObscure($string , $startAt = 0)
    {
        $cutString = substr($string , $startAt);
        
        $strReplaceCount = strlen($cutString);

        $obscuredTexts = '';

        for($i = 0 ; $i < $strReplaceCount ; $i++)
        {
            $obscuredTexts .= '*';
        }

        return substr_replace($string , $obscuredTexts , $startAt);
    }
    
    /*check if tomatch is array*/
    function isEqual($subject , $toMatch)
    {
        $subject = strtolower(trim($subject));

        if(is_array($toMatch))
         return in_array($subject , array_map('strtolower', $toMatch));
        return $subject === strtolower(trim($toMatch));
    }
    
    function is_email($string)
    {
        if(! filter_var($string , FILTER_VALIDATE_EMAIL))
            return FALSE;
        return TRUE;
    }

    function str_to_mobile($string)
    {
        $mobile = preg_replace("/[^0-9]/", "", trim($string));

        return $mobile;
    }

    function str_to_email($string)
    {
        $email = preg_replace("/[^0-9a-zA-Z@._]/", "", trim($string));
        return $email;
    }

    function str_escape($value)
    {
        $search = array("\\",  "\x00", "\n",  "\r",  "'",  '"', "\x1a");
        $replace = array("\\\\","\\0","\\n", "\\r", "\'", '\"', "\\Z");

        return str_replace($search, $replace, $value);
    }


    function stringWrap($string , $element)
    {
      $open = "<{$element}>";
      $close = "</{$element}>";


      return "{$open}{$string}{$close}";
    }
