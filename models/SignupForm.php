<?php

namespace app\models;

use Yii;
use yii\base\Model;

class SignupForm extends Model
{
    public $username;
    public $password;
    public $password_repeat;

    public function rules()
    {
        return [
            ['username', 'trim'],
            ['username', 'required', 'message' => 'Введите логин'],
            ['username', 'string', 'min' => 3, 'max' => 64, 'tooShort' => 'Логин должен содержать минимум 3 символа', 'tooLong' => 'Логин не должен превышать 64 символа'],
            ['username', 'unique', 'targetClass' => '\app\models\User', 'message' => 'Этот логин уже занят'],

            ['password', 'required', 'message' => 'Введите пароль'],
            ['password', 'string', 'min' => 6, 'tooShort' => 'Пароль должен содержать минимум 6 символов'],

            ['password_repeat', 'required', 'message' => 'Подтвердите пароль'],
            ['password_repeat', 'compare', 'compareAttribute' => 'password', 'message' => 'Пароли не совпадают'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'username' => 'Логин',
            'password' => 'Пароль',
            'password_repeat' => 'Повторите пароль',
        ];
    }

    public function signup()
    {
        if (!$this->validate()) {
            return false;
        }

        $user = new User();
        $user->username = $this->username;
        $user->setPassword($this->password);

        return $user->save();
    }
}