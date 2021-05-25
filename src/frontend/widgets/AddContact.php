<?php

namespace bvb\icontact\frontend\widgets;

use bvb\icontact\api\ApiModule;
use bvb\icontact\frontend\iContactModule;
use bvb\icontact\frontend\models\AddContactForm;
use Yii;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;

/**
 * SubscribeToList displays a form with a slot for a user's email to subscribe
 * them to a specified list
 */
class AddContact extends \yii\base\Widget
{
    /**
     * @var string The name of the event to be triggered on success
     */
    const JS_SUCCESS_EVENT = 'iContactAddContactSuccess';
    /**
     * The model representing the intake of a user's email address to subscribe
     * them to a list
     * @var \bvb\icontact\frontend\models\AddContactForm
     */
    public $addContactForm;

    /**
     * The ID of the Module that is needed to determine the URL endpoint
     * used to add an email address to a list
     * @var string
     */
    public $moduleId = iContactModule::DEFAULT_ID;

    /**
     * The ID of the API module that is needed to determine the URL endpoint
     * used to add an email address to a list
     * @var string
     */
    public $apiModuleId = ApiModule::DEFAULT_ID;

    /**
     * The button to render. Can be set as a string to render the string
     * itself or it can be an array configuration for an HtmlElement {@see \brianvb\yiiwidget\HtmlElement}
     * @var mixed
     */
    public $button = [];

    /**
     * Label used to render the email activefield
     * @var string
     */
    public $label;

    /**
     * Hint to be used to render the email input. Defaults to the value on the
     * model
     * @var string
     */
    public $hint;

    /**
     * Displays the form
     * {@inheritdoc}
     */ 
    public function run()
    {
        $form = ActiveForm::begin();
        $this->registerJavascript($form->getId());
            $emailField = $form->field($this->addContactForm, 'email');

            // --- Apply any label/hint passed into the widget
            if($this->label === false){
                $emailField->label(false);
            }
            if(is_string($this->label)){
                $emailField->label($this->label);
            }
            if($this->hint === false){
                $emailField->hint(false);
            }
            if(is_string($this->hint)){
                $emailField->hint($this->hint);
            }

            echo $emailField->input('email', ['placeholder' => 'Enter Email Address']); ?>
            <div id="<?= $form->getId(); ?>-success-message" class="success-message-container"></div>


            <?php 
            if(is_string($this->button)){
                $button = $this->button;
            } else {
                $defaultButtonOptions = [
                    'tag' => 'button',
                    'type' => 'submit',
                    'content' => 'Subscribe',
                    'class' => 'btn btn-primary'
                ];
                $buttonOptions = ArrayHelper::merge($defaultButtonOptions, $this->button);
                $tag = ArrayHelper::remove($buttonOptions, 'tag');
                $content = ArrayHelper::remove($buttonOptions, 'content');
                $button = Html::tag($tag, $content, $buttonOptions);
            }
            echo $button;
        ActiveForm::end();
    }

    /**
     * Getter for [[$addContactForm]] and will create a new instance if 
     * none is supplied during widget initialization
     * @var \bvb\mailchimp\frontend\models\SubscribeToListForm
     */
    public function getAddContactForm()
    {
        if(empty($this->addContactForm)){
            $this->addContactForm = new AddContactForm();
        }
        return $this->addContactForm;
    }

    /**
     * Registers javascript to add a contact via ajax
     * @param string $formId ID attribute of the HTML form element
     * @return void
     */
    public function registerJavascript($formId)
    {
        $eventName = self::JS_SUCCESS_EVENT;
        $emailInputId = Html::getInputId($this->getAddContactForm(), 'email');
        $addContactUrl = Url::to(['/'.$this->moduleId.'/'.$this->apiModuleId.'/v1/contacts/add?createModel=0']);
        $csrfParam = Yii::$app->request->csrfParam;
        $csrfToken = Yii::$app->request->csrfToken;
$readyJs = <<<JAVASCRIPT
$("#{$formId}").on("beforeSubmit", function(e){
    fetch("{$addContactUrl}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify({
                email: $("#{$emailInputId}").val(),
                $csrfParam: "{$csrfToken}"
            })
        })
        .then(response => Promise.all([response.ok, response.json()]))
        .then(([responseOk, body])  => {
            if(!responseOk || !body.success){
                if(body.message){
                    $('#{$formId}').yiiActiveForm('updateAttribute', '{$emailInputId}', [body.message]);
                } else {
                    $('#{$formId}').yiiActiveForm('updateAttribute', '{$emailInputId}', ["Unknown error"]);
                }
            } else {
                $('#{$formId}-success-message').html("Sign-up successful");
                document.dispatchEvent(new CustomEvent('{$eventName}', {detail: body}));
            }
        });
    return false;
});
JAVASCRIPT;
        $this->getView()->registerJs($readyJs);
    }
}