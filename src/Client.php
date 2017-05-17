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

    /**
     * returns a list of user avatars
     *
     * @param $user_id
     * @param $user_token
     * @return array
     * @throws \Exception
     */
    public function getUserAvatars($user_id, $user_token){
        $command = new ApiCommand("user/$user_id/avatars", "GET", 0);
        $command->setToken($user_token);
        try{
            $response = $this->call($command);
            return $response;
        }catch (\Exception $e){
            throw $e;
        }
    }

    public function getUserProfilePicture($user_id, $user_token){
        $command = new ApiCommand("user/$user_id/avatar", "GET", 0);
        $command->setToken($user_token);
        try{
            $response = $this->call($command);
            return $response;
        }catch (\Exception $e){
            throw $e;
        }
    }

    /**
     * posts a new image as user's profile picture, using hash from object storage
     *
     * @param $image_hash
     * @param $content_type
     * @param $user_token
     * @return array
     * @throws \Exception
     */
    public function uploadProfilePicture($image_hash, $content_type, $user_token){
        $command = new ApiCommand("me/profile/avatar", "POST", 0);
        $data = array(
            'binData' =>array(
                'hash' => $image_hash,
                'content_type' => $content_type
            )
        );
        $command->setData($data);
        $command->setToken($user_token);
        try{
            $response = $this->call($command);
            return $response;
        }catch (\Exception $e){
            throw $e;
        }
    }

    /**
     * remove an image form user's avatars
     *
     * @param $image_hash
     * @param $user_token
     * @return array
     * @throws \Exception
     */
    public function deleteAvatar($image_hash, $user_token){
        $command = new ApiCommand("me/profile/avatar/$image_hash", "DELETE", 0);
        $command->setToken($user_token);
        try{
            $response = $this->call($command);
            return $response;
        }catch (\Exception $e){
            throw $e;
        }
    }

    /**
     * returns followers of a user
     *
     * @param $user_id
     * @param $user_token
     * @return array
     * @throws \Exception
     */
    public function getUserFollowers($user_id, $user_token){
        $command = new ApiCommand("user/$user_id/followers", "GET", 0);
        $command->setToken($user_token);
        try{
            $response = $this->call($command);
            return $response;
        }catch (\Exception $e){
            throw $e;
        }
    }

    /**
     * returns followings of a user
     *
     * @param $user_id
     * @param $user_token
     * @return array
     * @throws \Exception
     */
    public function getUserFollowings($user_id, $user_token){
        $command = new ApiCommand("user/$user_id/followings", "GET", 0);
        $command->setToken($user_token);
        try{
            $response = $this->call($command);
            return $response;
        }catch (\Exception $e){
            throw $e;
        }
    }

    /**
     * gets user's follow requests
     *
     * @param $user_token
     * @return array
     * @throws \Exception
     */
    public function getFollowRequests($user_token){
        $command = new ApiCommand("me/follow_requests", "GET", 0);
        $command->setToken($user_token);
        try{
            $response = $this->call($command);
            return $response;
        }catch (\Exception $e){
            throw $e;
        }
    }

    /**
     * accepts or rejects a request
     *
     * @param $request_id
     * @param $user_token
     * @param boolean $accept
     * @return array
     * @throws \Exception
     */
    public function changeRequestStatus($request_id, $user_token, $accept){
        $command = new ApiCommand("me/follow_requests", "PUT", 0);
        $command->setToken($user_token);
        $status = $accept ? 1:0;
        $command->setData(['status' => $status]);
        try{
            $response = $this->call($command);
            return $response;
        }catch (\Exception $e){
            throw $e;
        }
    }

    /**
     * sets a relationship between the owner of the token and selected user
     *
     * @param $user_token
     * @param $target_user_id
     * @param string $operation; possible values: follow, report, block, unblock, unfollow, kick
     * @return array
     * @throws \Exception
     */
    public function modifyRelationship($user_token, $target_user_id, $operation){
        $command = new ApiCommand("user/$target_user_id/relation", "POST", 0);
        $command->setToken($user_token);
        $command->setData(['operation' => $operation]);
        try{
            $response = $this->call($command);
            return $response;
        }catch (\Exception $e){
            throw $e;
        }
    }

    /**
     * determines relationship of token's owener and user with $user_id
     *
     * @param $user_token
     * @param $user_id
     * @return array
     * @throws \Exception
     */
    public function getRelationWithUser($user_token, $user_id){
        $command = new ApiCommand("user/$user_id/relation", "GET", 0);
        $command->setToken($user_token);
        try{
            $response = $this->call($command);
            return $response;
        }catch (\Exception $e){
            throw $e;
        }
    }

    /**
     * gets a list of users blocked users
     *
     * @param $user_token
     * @return array
     * @throws \Exception
     */
    public function getUserBlocksList($user_token){
        $command = new ApiCommand("me/blocked", "GET", 0);
        $command->setToken($user_token);
        try{
            $response = $this->call($command);
            return $response;
        }catch (\Exception $e){
            throw $e;
        }
    }

    /**
     * returns a user's profile page info, should be called with a user_token
     * due to privacy checking
     *
     * @param $user_id
     * @param $user_token
     * @return array
     * @throws \Exception
     */
    public function getUserProfilePage($user_id, $user_token){
        $command = new ApiCommand("user/$user_id", "GET", 0);
        $command->setToken($user_token);
        try{
            $response = $this->call($command);
            return $response;
        }catch (\Exception $e){
            throw $e;
        }
    }

    /**
     * updates user's profile, included fields will be updated while other fields will be left unchanged
     * input array example:
     * $data = ['display_name' => 'hessam', 'is_private' => 0, 'gender' => 'female']
     *
     * @param $user_token
     * @param $data
     * @return array
     * @throws \Exception
     */
    public function updateProfile($user_token, $data){
        $command = new ApiCommand("me/profile", "POST", 0);
        $command->setToken($user_token);
        $command->setData($data);
        try{
            $response = $this->call($command);
            return $response;
        }catch (\Exception $e){
            throw $e;
        }
    }

    /**
     * returns a user's basic info, should be called with a user_token
     *
     * @param $user_id
     * @param $user_token
     * @return array
     * @throws \Exception
     */
    public function getUserBasicProfile($user_id, $user_token){
        $command = new ApiCommand("user/$user_id/profile/basic", "GET", 0);
        $command->setToken($user_token);
        try{
            $response = $this->call($command);
            return $response;
        }catch (\Exception $e){
            throw $e;
        }
    }

    /**
     * @param $user_id
     * @param $user_token
     * @return array
     * @throws \Exception
     */
    public function getUserPersonalInfo($user_id, $user_token){
        $command = new ApiCommand("user/$user_id/profile/personal", "GET", 0);
        $command->setToken($user_token);
        try{
            $response = $this->call($command);
            return $response;
        }catch (\Exception $e){
            throw $e;
        }
    }

    /**
     * updates user's cover image
     *
     * @param $user_token
     * @param $image_hash
     * @param $content_type
     * @return array
     * @throws \Exception
     */
    public function updateCover($user_token, $image_hash, $content_type){
        $command = new ApiCommand("me/cover", "POST", 0);
        $data = array(
            'binData' =>array(
                'hash' => $image_hash,
                'content_type' => $content_type
            )
        );
        $command->setData($data);
        $command->setToken($user_token);
        try{
            $response = $this->call($command);
            return $response;
        }catch (\Exception $e){
            throw $e;
        }
    }

    public function deleteCover($user_token){
        $command = new ApiCommand("me/cover", "DELETE", 0);
        $command->setToken($user_token);
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
     * signals a change of username to members service
     *
     * @param $user_id
     * @param $username
     * @return array
     * @throws \Exception
     */
    public function changeUsername($user_id, $username){
        $command = new ApiCommand("user/$user_id/username", "PATCH", 1);
        $command->setData(['username' => $username]);
        try{
            $response = $this->call($command);
            return $response;
        }catch (\Exception $e){
            throw $e;
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
        try{
            $response = $this->call($command);
            return $response;
        }catch (\Exception $e){
            throw $e;
        }
    }

    /**
     * updates user's settings within the given namespace
     * $settings example: ['key1' => 'val1', 'key2' => 'val2']
     *
     * @param $user_token
     * @param $namespace
     * @param $settings
     * @return array
     * @throws \Exception
     */
    public function updateSettings($user_token, $namespace, $settings){
        $command = new ApiCommand("me/settings/$namespace", 'PUT', 0);
        $command->setToken($user_token);
        $command->setData($settings);
        try{
            $response = $this->call($command);
            return $response;
        }catch (\Exception $e){
            throw $e;
        }
    }

    /**
     * registers a new user,
     * example of $identifiers array:
     * ['email' => 'hessamhedayati@gmail.com', 'mobile' => ['country' => '+98', 'number' => '9376879924']]
     *
     * @param string $username
     * @param string $display_name
     * @param string $password
     * @param array $identifiers
     * @return array
     * @throws \Exception
     */
    public function register($username, $display_name, $password, $identifiers){
        $command = new ApiCommand("register", 'POST', 0);
        $data = array([
            'display_name' => $display_name,
            'password' => $password,
            'username' => $username,
            'email' => $identifiers['email'],
            'mobile' => $identifiers['mobile']
        ]);
        $command->setData($data);
        try{
            $response = $this->call($command);
            return $response;
        }catch (\Exception $e){
            throw $e;
        }
    }

    /**
     * searches in users for the input term
     *
     * @param $user_token
     * @param $term
     * @return array
     * @throws \Exception
     */
    public function searchInUsers($user_token, $term){
        $command = new ApiCommand("users/search", 'GET', 0);
        $command->setQueryParams($term);
        $command->setToken($user_token);
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