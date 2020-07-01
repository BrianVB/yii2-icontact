<?php

namespace bvb\icontact\backend\controllers;

use bvb\icontact\common\models\Contact;
use bvb\user\backend\controllers\traits\AdminAccess;
use yii\web\Controller;

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
     * Syncs contact data
     * @param string $id ID of the contact in iContact
     * @param string $redirectUrl URL to redirect a user to after completion
     * @return yii\web\Response
     */
    public function actionSync($id, $redirectUrl)
    {
        $this->makeCall("/a/{$this->setAccountId()}/c/{$this->setClientFolderId()}/contacts/{$iContactId}", 'POST', $aContact, 'contact');
        return $this->redirect($redirectUrl);
    }
}