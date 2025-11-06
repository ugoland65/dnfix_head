<?php

class Helper
{   
    public static function postData($url,$data,$header = '')
    {
        try
        {
            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, 1);

            if($header == '')
            {
                $header = array('Content-Type: application/json');
            }
            
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);

            if (is_array($data))
            {
                $data = json_encode($data);
            }
            
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
            
            $response = curl_exec($ch);
            curl_close($ch);

            return $response;
        }
        catch(\Exception $e)
        {
            return '';
        }
    }

    public static function getData($url,$header = '')
    {
        try
        {
            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, $url);
            
            if($header == '')
            {
                $header = array('Content-Type: application/json');
            }
            
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);

            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
            
            $response = curl_exec($ch);
            curl_close($ch);

            return $response;
        }
        catch(\Exception $e)
        {
            return '';
        }
    }
}