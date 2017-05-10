<?php
namespace Papion\MembersClient;

use Papion\MembersClient\Interfaces\iTokenProvider;

class Client
{
    protected $baseUrl;
    protected $platform;
    protected $tokenProvider;


    /**
     * Client constructor.
     * @param $baseUrl
     * @param $tokenProvider
     *
     * @throws \InvalidArgumentException
     */
    function __construct($baseUrl, $tokenProvider)
    {
        $this->baseUrl  = rtrim( (string) $baseUrl, '/' );
        if(isInstanceOf($tokenProvider, iTokenProvider::class)){
            $this->tokenProvider = $tokenProvider;
        }else{
            throw new \InvalidArgumentException();
        }
    }

    public function specificTask(){

    }

    protected function platform()
    {
        if (! $this->platform ){
            $this->platform = new PlatformRest;
        }
        $this->platform->setServerUrl( $this->baseUrl );
        return $this->platform;
    }

    /**
     * @param ApiCommand $command
     * @return mixed
     */
    protected function call(ApiCommand $command)
    {
        $recall = 1;
        recall:
        if ($command->getNeedsToken()) {
            $token = $this->tokenProvider->getToken();
            $command->setToken($token);
        }
        $platform = $this->platform();
        $response = $platform->send($command);
        if ($ex = $response->hasException()) {
            if ( $ex instanceof exTokenMismatch && $recall > 0 ) {
                // Token revoked or mismatch
                // Refresh Token
                $this->tokenProvider->exchangeToken();
                $recall--;
                goto recall;
            }
            throw $ex;
        }
        return $response;
    }

}