<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;

/**
 * @property string $phone
 * @property int $created_at
 * @property int $updated_at
 *
 * @property Subscribe[] $subscribes
 */
class SubscriberPhone extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%subscriber_phone}}';
    }

    public function behaviors()
    {
        return [
            TimestampBehavior::class,
        ];
    }

    public function rules()
    {
        return [
            [['phone'], 'required'],
            [['phone'], 'string', 'max' => 20],
            [['phone'], 'match', 'pattern' => '/^\+?[0-9\s\-\(\)]+$/'],
            [['phone'], 'unique'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'phone' => 'Номер телефона',
            'created_at' => 'Дата создания',
            'updated_at' => 'Дата обновления',
        ];
    }

    public function getSubscribes()
    {
        return $this->hasMany(Subscribe::class, ['phone' => 'phone']);
    }

    /**
     * Получает или создаёт подписчика по номеру телефона
     */
    public static function findOrCreate($phone)
    {
        $phone = preg_replace('/\D/', '', $phone);

        $model = static::findOne(['phone' => $phone]);
        if (!$model) {
            $model = new static(['phone' => $phone]);
            if (!$model->save()) {
                Yii::error('Не удалось создать подписчика: ' . print_r($model->errors, true));
                return null;
            }
        }

        return $model;
    }
}