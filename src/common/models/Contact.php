<?php

namespace bvb\icontact\common\models;

use bvb\icontact\common\helpers\ApiHelper;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "icontact_contact".
 *
 * @property integer $user_id
 * @property integer $contact_id
 * @property string $last_sync_time
 * @property string $create_time
 * @property string $update_time
 *
 * @property User $user
 */
class Contact extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'icontact_contact';
    }
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['contact_id'], 'required'],
            [['contact_id'], 'integer'],
            [['last_sync_time'], 'safe'],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Yii::$app->user->identityClass, 'targetAttribute' => ['user_id' => Yii::$app->user->identityClass::instance()->primaryKey()[0]]]
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(Yii::$app->user->identityClass, ['id' => 'user_id']);
    }

    /**
     * Connect to the API to add the contact
     * {@inheritdoc}
     */
    public function beforeValidate()
    {
        // --- This is done in a funny way because of how many arguments they have
        // --- in their API function. We will allow for a custom mapping of properties
        // --- via our application param and we will have to call the function
        // --- via call_user_func_array to pass them in properly
        $map = [
            'sEmail' => 'user.email'
        ];
        if(isset(Yii::$app->params['iContact']['contactPropertyMap'])){
            $map = ArrayHelper::merge($map, Yii::$app->params['iContact']['contactPropertyMap']);
        }

        if($this->isNewRecord){
            $args = ApiHelper::$addContactArguments;
            foreach($args as $argumentName => $argumentValue){
                if(isset($map[$argumentName])){
                    $args[$argumentName] = ArrayHelper::getValue($this, $map[$argumentName]);
                }
            }
            $contact = call_user_func_array([ApiHelper::getSingleton()->getInstance(), 'addContact'], $args);
            $this->contact_id = $contact->contactId;
        } else {
            $args = ApiHelper::$updateContactArguments;
            $args['iContactId'] = $this->contact_id;
            foreach($args as $argumentName => $argumentValue){
                if(isset($map[$argumentName])){
                    $args[$argumentName] = ArrayHelper::getValue($this, $map[$argumentName]);
                }
            }           
            $contact = call_user_func_array([ApiHelper::getSingleton()->getInstance(), 'updateContact'], $args);
        }
        return parent::beforeValidate();
    }
}
