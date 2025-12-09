<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;

/**
 * @property int $id
 * @property string $phone
 * @property int $author_id
 * @property int $created_at
 * @property int $updated_at
 *
 * @property SubscriberPhone $subscriberPhone
 * @property Authors $author
 */
class Subscribe extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%subscribes}}';
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
            [['phone', 'author_id'], 'required'],
            [['author_id'], 'integer'],
            [['phone'], 'string', 'max' => 20],
            [['phone'], 'match', 'pattern' => '/^\+?[0-9\s\-\(\)]+$/'],
            [['author_id'], 'exist', 'skipOnError' => true,
                'targetClass' => Authors::class, 'targetAttribute' => ['author_id' => 'id']],
            [['phone'], 'exist', 'skipOnError' => true,
                'targetClass' => SubscriberPhone::class, 'targetAttribute' => ['phone' => 'phone']],

            // Уникальная проверка - номер не может подписаться дважды на одного автора
            [['phone', 'author_id'], 'unique', 'targetAttribute' => ['phone', 'author_id'],
                'message' => 'Этот номер уже подписан на данного автора'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'phone' => 'Номер телефона',
            'author_id' => 'Автор',
            'created_at' => 'Дата подписки',
            'updated_at' => 'Дата обновления',
        ];
    }

    public function getSubscriberPhone()
    {
        return $this->hasOne(SubscriberPhone::class, ['phone' => 'phone']);
    }

    public function getAuthor()
    {
        return $this->hasOne(Authors::class, ['id' => 'author_id']);
    }

    /**
     * Создаёт подписку
     */
    public static function createSubscription($authorId, $phone)
    {
        // Нормализуем номер телефона
        $phone = preg_replace('/\D/', '', $phone);

        if (strlen($phone) < 10) {
            return ['success' => false, 'message' => 'Некорректный номер телефона'];
        }

        // Проверяем существование автора
        $author = Authors::findOne($authorId);
        if (!$author) {
            return ['success' => false, 'message' => 'Автор не найден'];
        }

        // Создаём или находим подписчика
        $subscriber = SubscriberPhone::findOrCreate($phone);
        if (!$subscriber) {
            return ['success' => false, 'message' => 'Не удалось создать подписчика'];
        }

        // Проверяем, не подписан ли уже
        $existing = static::find()
            ->where(['phone' => $phone, 'author_id' => $authorId])
            ->exists();

        if ($existing) {
            return ['success' => false, 'message' => 'Вы уже подписаны на этого автора'];
        }

        // Создаём подписку
        $subscribe = new static([
            'phone' => $phone,
            'author_id' => $authorId,
        ]);

        if ($subscribe->save()) {
            return [
                'success' => true,
                'message' => 'Вы успешно подписались на обновления автора',
                'subscription_id' => $subscribe->id
            ];
        } else {
            Yii::error('Ошибка при сохранении подписки: ' . print_r($subscribe->errors, true));
            return ['success' => false, 'message' => 'Не удалось создать подписку'];
        }
    }

    /**
     * Получает список телефонов подписчиков автора
     */
    public static function getSubscriberPhonesByAuthor($authorId)
    {
        return static::find()
            ->select('phone')
            ->where(['author_id' => $authorId])
            ->column();
    }

    /**
     * Получает список авторов, на которых подписан телефон
     */
    public static function getAuthorsByPhone($phone)
    {
        return static::find()
            ->select('author_id')
            ->where(['phone' => $phone])
            ->column();
    }
}