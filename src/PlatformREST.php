<?php
/**
 * Created by IntelliJ IDEA.
 * User: serpico
 * Date: 5/10/17
 * Time: 1:36 PM
 */

namespace App\Classes\members_client\src;


use App\Classes\members_client\src\Exceptions\exHTTPResponse;

class PlatformREST
{
    private $serverURL;

    function __construct($serverURL)
    {
        $this->serverURL = $serverURL;
    }

    /**
     * @return mixed
     */
    public function getServerURL()
    {
        return $this->serverURL;
    }

    /**
     * @param mixed $serverURL
     */
    public function setServerURL($serverURL)
    {
        $this->serverURL = $serverURL;
    }

    /**
     * @param ApiCommand $command
     * @return array
     * @throws \Exception
     */
    public function send(ApiCommand $command){
        /**
         * checking for cURL library
         * */
        if (! extension_loaded('curl') ){
            throw new \Exception('cURL library is not loaded');
        }
        /**
         * cURL options start
         * */
        $handle = curl_init();
        $headersArray = [];
        foreach ($command->getHeaders() as $key => $value){
            array_push($headersArray, $key . ": " .$value);
        }
        curl_setopt($handle, CURLOPT_HTTPHEADER, $headersArray);
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($handle, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, false);
        switch ($command->getMethod()){
            case "GET":
                $this->_sendGet($command, $handle);
                break;
            case "POST":
                $this->_sendPost($command, $handle);
                break;
            case "PUT":
                $this->_sendPut($command, $handle);
                break;
            case "PATCH":
                $this->_sendPatch($command, $handle);
                break;
            case "DELETE":
                $this->_sendDelete($command, $handle);
                break;
        }
        /**
         * cURL options set
         * */

        /**
         * executing cURL
         * */
        $response = curl_exec($handle);
        $responseCode = curl_getinfo($handle, CURLINFO_HTTP_CODE);
        $contentType = curl_getinfo($handle, CURLINFO_CONTENT_TYPE);

        /**
         * checking for connection errors
         * */
        if ($curl_errno = curl_errno($handle)) {
            // Connection Error
            $curl_error = curl_error($handle);
            throw new exConnection($curl_error, $curl_errno);
        }
        curl_close($handle);
        $exception = null;
        if (! ($responseCode >= 200 && $responseCode < 300) ) {
            $message = $response;
            if ($responseCode >= 300 && $responseCode < 400){
                $message = 'Response Redirected To Another Uri.';
            }
            $exception = new exHttpResponse($message, $responseCode);
        }
        return array(
            'response' => $response,
            'responseCode' => $responseCode,
            'contentType' => $contentType,
            'exception' => $exception
        );
    }

    function _sendGet(ApiCommand $command, &$handle){
        $urlEncodeData = "";
        if ($params = $command->getQueryParams()){
            $urlEncodeData = http_build_query($params);
        }
        curl_setopt($handle, CURLOPT_URL, $this->serverURL . "/" . $command->getEndpoint() . $urlEncodeData);
    }

    function _sendPost(ApiCommand $command, &$handle){
        curl_setopt($handle, CURLOPT_URL, $this->serverURL . "/" . $command->getEndpoint());
        curl_setopt($handle, CURLOPT_POST, true);
        $data = $command->getData();
        foreach ($data  as $k => $d) {
            if (is_array($d)) {
                foreach ($d as $i => $v){
                    $data[$k.'['.$i.']'] = $v;
                }
                unset($data[$k]);
            }
        }
        curl_setopt($handle, CURLOPT_POSTFIELDS, $data);
    }

    function _sendPut(ApiCommand $command, &$handle){
        curl_setopt($handle, CURLOPT_URL, $this->serverURL . "/" . $command->getEndpoint());
        curl_setopt($handle, CURLOPT_PUT, true);
        $data = $command->getData();
        foreach ($data  as $k => $d) {
            if (is_array($d)) {
                foreach ($d as $i => $v){
                    $data[$k.'['.$i.']'] = $v;
                }
                unset($data[$k]);
            }
        }
        curl_setopt($handle, CURLOPT_POSTFIELDS, $data);
    }

    function _sendPatch(ApiCommand $command, &$handle){
        curl_setopt($handle, CURLOPT_URL, $this->serverURL . "/" . $command->getEndpoint());
        curl_setopt($handle, CURLOPT_CUSTOMREQUEST, 'PATCH');
        $data = $command->getData();
        foreach ($data  as $k => $d) {
            if (is_array($d)) {
                foreach ($d as $i => $v){
                    $data[$k.'['.$i.']'] = $v;
                }
                unset($data[$k]);
            }
        }
        curl_setopt($handle, CURLOPT_POSTFIELDS, $data);
    }

    function _sendDelete(ApiCommand $command, &$handle){
        curl_setopt($handle, CURLOPT_URL, $this->serverURL . "/" . $command->getEndpoint());
        curl_setopt($handle, CURLOPT_CUSTOMREQUEST, 'DELETE');
    }
}