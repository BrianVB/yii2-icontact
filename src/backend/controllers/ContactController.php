<?php

namespace bvb\icontact\backend\controllers;

use bvb\icontact\common\models\Contact;
use bvb\user\backend\controllers\traits\AdminAccess;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * @todo This has yet to be completed and also poses some challenges because the
 * function for updating a contact does not allow for custom parameters so we 
 * must find a way to do that which maybe will require not using their functionality
 * at all 
 */
class ContactController extends Controller
{
    /**
     * Implement AccessControl that requires admin role to access actions
     */
    use AdminAccess;

    /**
     * Syncs contact data by just using validate() on the model to trigger
     * beforeValidate() where it all happens
     * @param string $userId ID of the contact in iContact
     * @param string $redirectUrl URL to redirect the uesr to
     * @return yii\web\Response
     */
    public function actionSync($userId, $redirectUrl)
    {
        $contact = Contact::findOne($userId);
        if(!$contact){
            throw new NotFoundHttpException('Contact not found');
        }
        if($contact->validate()){
            Yii::$app->session->addFlash('success', 'Contact updated in iContact');
        } else {
            $message = 'There was an error updating the contact in iContact';
            Yii::error($message.': '.print_r($contact->getErrors(), true));
            Yii::$app->session->addFlash('error', $message);
        }
        return $this->redirect($redirectUrl);
    }
}