<?php namespace common\models\entities;

use Yii;
use yii\web\IdentityInterface;
use Zaioll\YiiDoctrine\DataMapper;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use Doctrine\ORM\Mapping;

/**
 * @Mapping\Entity
 * @Mapping\Table(name="user")
 */
class User extends DataMapper implements IdentityInterface
{
    const STATUS_DELETED = 0;
    const STATUS_INACTIVE = 9;
    const STATUS_ACTIVE = 10;

    /**
     * @Mapping\Id
     * @Mapping\Column(type="bigint")
     * @Mapping\GeneratedValue(strategy="AUTO")
     *
     * @var int
     */
    protected $id;

    /**
     * @Mapping\Column(type="string", length=255, unique=true)
     * @var string
     */
    protected $username;

    /**
     * @Mapping\Column(type="string", name="auth_key", length=32)
     * @var string
     */
    protected $authKey;

    /**
     * @Mapping\Column(type="string", name="password_hash", length=255)
     * @var string
     */
    protected $passwordHash;

    /**
     * @Mapping\Column(type="string", name="password_reset_token", length=255, nullable=true)
     * @var string
     */
    protected $passwordResetToken;

    /**
     * @Mapping\Column(type="string", length=255, unique=true)
     * @var string
     */
    protected $email;


    /**
     * @Mapping\Column(type="smallint", options={"default": 10})
     * @var int
     */
    protected $status;

    /**
     * @Mapping\Column(type="integer", name="created_at")
     * @var int
     */
    protected $createdAt;

    /**
     * @Mapping\Column(type="integer", name="updated_at")
     * @var int
     */
    protected $updatedAt;

    /**
     * @Mapping\Column(type="string", length=255, name="verification_token")
     * @var string
     */
    protected $verificationToken;

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    DataMapper::EVENT_BEFORE_INSERT => ['createdAt', 'updatedAt'],
                    DataMapper::EVENT_BEFORE_UPDATE => ['updatedAt'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['status', 'default', 'value' => self::STATUS_INACTIVE],
            ['status', 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_INACTIVE, self::STATUS_DELETED]],
        ];
    }

    /**
     * @return string
     */
    public function getVerificationToken(): string
    {
        return $this->verificationToken;
    }

    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentity($id)
    {
        return self::getRepository()->findOneBy(['id' => $id, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return self::getRepository()->findOneBy(['username' => $username, 'status' => self::STATUS_ACTIVE]);
        //return static::findOne(['username' => $username, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * Finds user by password reset token
     *
     * @param string $token password reset token
     * @return static|null
     */
    public static function findByPasswordResetToken($token)
    {
        if (! static::isPasswordResetTokenValid($token)) {
            return null;
        }

        return self::getRepository()->findOneBy([
            'passwordResetToken' => $token,
            'status' => self::STATUS_ACTIVE,
        ]);
        /*
        return static::findOne([
            'password_reset_token' => $token,
            'status' => self::STATUS_ACTIVE,
        ]);
        */
    }

    /**
     * Finds user by verification email token
     *
     * @param string $token verify email token
     * @return static|null
     */
    public static function findByVerificationToken($token)
    {
        return self::getRepository()->findOneBy([
            'verificationToken' => $token,
            'status' => self::STATUS_INACTIVE
        ]);
        /*
        return static::findOne([
            'verification_token' => $token,
            'status' => self::STATUS_INACTIVE
        ]);
        */
    }

    /**
     * Finds out if password reset token is valid
     *
     * @param string $token password reset token
     * @return bool
     */
    public static function isPasswordResetTokenValid($token)
    {
        if (empty($token)) {
            return false;
        }

        $timestamp = (int) substr($token, strrpos($token, '_') + 1);
        $expire = Yii::$app->params['user.passwordResetTokenExpire'];
        return $timestamp + $expire >= time();
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthKey()
    {
        return $this->authKey;
    }

    /**
     * {@inheritdoc}
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->passwordHash);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->passwordHash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->authKey = Yii::$app->security->generateRandomString();
    }

    /**
     * Generates new password reset token
     */
    public function generatePasswordResetToken()
    {
        $this->passwordResetToken = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Generates new token for email verification
     */
    public function generateEmailVerificationToken()
    {
        $this->verificationToken = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Removes password reset token
     */
    public function removePasswordResetToken()
    {
        $this->passwordResetToken = null;
    }

    /**
     * @param string $username
     * @return self
     */
    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @param string $email
     * @return self
     */
    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getStatus(): ?int
    {
        return $this->status;
    }

    /**
     * @param integer $status
     * @return self
     */
    public function setStatus(int $status): self
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get the value of createdAt
     *
     * @return  int
     */
    public function getCreatedAt(): ?int
    {
        return $this->createdAt;
    }

    /**
     * Set the value of createdAt
     *
     * @param  int  $createdAt
     *
     * @return  self
     */
    public function setCreatedAt(int $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get the value of updatedAt
     *
     * @return  int
     */
    public function getUpdatedAt(): ?int
    {
        return $this->updatedAt;
    }

    /**
     * Set the value of updatedAt
     *
     * @param  int  $updatedAt
     *
     * @return  self
     */
    public function setUpdatedAt(int $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }
}
