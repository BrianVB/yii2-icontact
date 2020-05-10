<?php

namespace bvb\icontact\common\helpers;

use bvb\singleton\Singleton;
use iContact\iContactApi;
use Yii;
use yii\base\BaseObject;
use yii\helpers\ArrayHelper;

/**
 * ApiHelper is in a sense a wrapper for [[iContact\iContactApi]] but is using
 * Yii's ActiveRecord models to sync data in our system with what is happening
 * in iContact's system
 */
class ApiHelper extends BaseObject
{
    /**
     * Implement singleton trait to use one instance across app if desired
     */
    use Singleton;

    /**
     * Array of configuration information passed through to 
     * @var array
     */
    public $instanceConfig = [];

    /**
     * Instance from the iContactApi php package
     * @var iContactApi
     */
    private $_instance;

    /**
     * Array of arguments that can be passed to [[iContact\iContactApi::addContact]]
     * in order to be used by external applications to call the function properly
     * @var array
     */
    static $addContactArguments = [
        'sEmail' => null,
        'sStatus' => 'normal',
        'sPrefix' => null,
        'sFirstName' => null,
        'sLastName' => null,
        'sSuffix' => null,
        'sStreet' => null,
        'sStreet2' => null,
        'sCity' => null,
        'sState' => null,
        'sPostalCode' => null,
        'sPhone' => null,
        'sFax' => null,
        'sBusiness' => null,
    ];

    /**
     * Array of arguments that can be passed to [[iContact\iContactApi::updateContact]]
     * in order to be used by external applications to call the function properly
     * @var array
     */
    static $updateContactArguments = [
        'iContactId' => null,
        'sEmail' => null,
        'sPrefix' => null,
        'sFirstName' => null,
        'sLastName' => null,
        'sSuffix' => null,
        'sStreet' => null,
        'sStreet2' => null,
        'sCity' => null,
        'sState' => null,
        'sPostalCode' => null,
        'sPhone' => null,
        'sFax' => null,
        'sBusiness' => null,
        'sStatus' => null,
    ];

    /**
     * Return an instance of the iContact API class that has had the credentials
     * applied. Uses Yii2 the following application parameters:
     * ['iContact']['appId']
     * ['iContact']['apiPassword']
     * ['iContact']['apiUsername']
     * Optionally one can set a boolean true value on the following to connect
     * to the sandbox:
     * ['iContact']['useSandbox']
     * Optionally set a default client folder to be set upon loading by setting
     * ['iContact']['clientFolderId']
     * @return iContact\iContactApi
     */
    public function getInstance()
    {
        if(empty($this->_instance)){
            $defaultInstanceConfig = [
                'appId'       => isset(Yii::$app->params['iContact']['appId']) ? Yii::$app->params['iContact']['appId'] : null,
                'apiPassword'       => isset(Yii::$app->params['iContact']['apiPassword']) ? Yii::$app->params['iContact']['apiPassword'] : null,
                'apiUsername'       => isset(Yii::$app->params['iContact']['apiUsername']) ? Yii::$app->params['iContact']['apiUsername'] : null,
            ];
            $this->_instance = iContactApi::getInstance()->setConfig(ArrayHelper::merge($defaultInstanceConfig, $this->instanceConfig));
            if(isset(Yii::$app->params['iContact']['useSandbox']) && Yii::$app->params['iContact']['useSandbox']){
                $this->_instance->useSandbox();
            }

            if(isset(Yii::$app->params['iContact']['clientFolderId'])){
                $this->_instance->setClientFolderId(Yii::$app->params['iContact']['clientFolderId']);
            }
        }
        return $this->_instance;
    }
}