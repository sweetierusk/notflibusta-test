<?php

namespace app\models;

use Yii;
use yii\base\BaseObject;
use yii\web\IdentityInterface;

class UserIdentity extends BaseObject implements IdentityInterface
{
    public $id;
    public $username;
    public $email;
    public $authKey;
    public $accessToken;

    public static function findIdentity($id)
    {
        return null;
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        return null;
    }

    public static function findByUsername($username)
    {
        $demoUsers = [
            'admin' => ['id' => 1, 'username' => 'admin', 'email' => 'admin@example.com'],
            'user' => ['id' => 2, 'username' => 'user', 'email' => 'user@example.com'],
            'test' => ['id' => 3, 'username' => 'test', 'email' => 'test@example.com'],
        ];

        if (isset($demoUsers[$username])) {
            $identity = new static();
            $identity->id = $demoUsers[$username]['id'];
            $identity->username = $demoUsers[$username]['username'];
            $identity->email = $demoUsers[$username]['email'];
            $identity->authKey = 'demo-auth-key-' . $username;
            $identity->accessToken = 'demo-access-token-' . $username;
            return $identity;
        }

        return null;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getAuthKey()
    {
        return $this->authKey;
    }

    public function validateAuthKey($authKey)
    {
        return $this->authKey === $authKey;
    }

    public function validatePassword($password)
    {
        return false;
    }
}