<?php

namespace bvb\icontact\api\v1\controllers;

use bvb\icontact\common\helpers\ApiHelper;
use bvb\icontact\common\models\Contact;
use Yii;
use yii\helpers\Json;
use yii\web\Controller;

/**
 * ContactsController is for endpoints related to iContact Contact records
 * in this data on in iContacts' system
 */
class ContactsController extends Controller
{
    /**
     * Attempts to subscribe the provided email to the provided list
     * @return mixed
     */
    public function actionAdd($createModel = false)
    {
        $success = false;
        $message = '';

        $email = Yii::$app->request->post('email');

        if($createModel){
            $contact = new Contact(['params' => ['email' => $email]]);
            if($contact->save()){
                $success = true;
            } else {
                $message = implode("\n", $contact->getErrorSummary(true));
            }
        } else {    
            try{
                $apiResponse = ApiHelper::getSingleton()->getInstance()->addContact($email);
                $success = true;
            } catch(\Throwable $e){
                $message = implode("\n", ApiHelper::getSingleton()->getInstance()->getErrors());
            }
        }

        return [
            'success' => $success,
            'message' => $message,
            'data' => [
                'apiResponse' => $apiResponse
            ]
        ];
    }
}
