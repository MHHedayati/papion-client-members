<?php
namespace App\Classes\members_client\src;

use App\Classes\members_client\src\Interfaces\iTokenProvider;

class Client
{
    protected $baseUrl;
    protected $platform;
    protected $tokenProvider;

    function isInstanceOf($object, $interface){
        $object_methods = get_class_methods($object);
        $interface_reflection = new \ReflectionClass($interface);
        $interface_methods = $interface_reflection->getMethods();
        foreach ($interface_methods as $method){
            if(!in_array($method->name, $object_methods)){
                return false;
            }
        }
        return true;
    }

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
        if($this->isInstanceOf($tokenProvider, iTokenProvider::class)){
            $this->tokenProvider = $tokenProvider;
        }else{
            throw new \InvalidArgumentException();
        }
    }

    /**
     * returns settings related to user with $user_id, within the given namespace
     * (** returns whole settings if namespace is null)
     * @param string $user_id
     * @param string $namespace
     * @return array
     * @throws \Exception
     */
    public function getUserSettings($user_id, $namespace){
        $command = new ApiCommand("user/$user_id/settings/$namespace", 'GET', 1);
        $command->addHeader('Accept','application/json');
        try{
            $response = $this->call($command);
            return $response;
        }catch (\Exception $e){
            throw $e;
        }
    }

    /**
     * returns basic profile of input users
     * @param array $user_ids: flat array of strings
     * @return array
     * @throws \Exception
     */
    public function getUsersBasicProfiles($user_ids){
        $command = new ApiCommand("users/profile/basic", "POST", 1);
        $command->setData(['users' => $user_ids]);
        try{
            $response = $this->call($command);
            return $response;
        }catch (\Exception $e){
            throw $e;
        }
    }

    /**
     * creates an instance of the platform
     * @return PlatformRest
     */
    protected function platform()
    {
        if (! $this->platform ){
            $this->platform = new PlatformREST($this->baseUrl);
        }
        $this->platform->setServerUrl( $this->baseUrl );
        return $this->platform;
    }

    /**
     * executes a given command, if the command needs a federate token, generates one
     * @param ApiCommand $command
     * @return array
     * @throws \Exception
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
        /** @var \Exception $ex */
        $ex = $response['exception'];
        if ($ex) {
            if ( $ex->getCode() == 403 && $recall > 0 ) {
                // Token revoked or mismatch
                // Refresh Token
                $this->tokenProvider->exchangeToken();
                $recall--;
                goto recall;
            }
            throw $ex;
        }
        return (array)(json_decode($response['response'])->result);
    }

}