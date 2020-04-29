<?php

use bvb\icontact\common\helpers\ApiHelper;

$this->title = 'iContact Api';

?>
<p>
    The following configuration is currently being used to connect to iContact:
    <ul>
    	<li>App ID: <?= Yii::$app->params['iContact']['appId']; ?></li>
    	<li>API Username: <?= Yii::$app->params['iContact']['apiUsername']; ?></li>
    	<li>Environment: <?= isset(Yii::$app->params['iContact']['useSandbox']) && Yii::$app->params['iContact']['useSandbox'] ? 'Sandbox' : 'Production' ?></li>
    </ul>
</p>

<p>The account ID returned from the API call: <?= ApiHelper::getSingleton()->getInstance()->setAccountId(); ?></p>
<p>The client folder ID returned from the API call: <?= ApiHelper::getSingleton()->getInstance()->setClientFolderId(); ?></p>