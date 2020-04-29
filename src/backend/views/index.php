<?php

use bvb\icontact\common\helpers\ApiHelper;

$this->title = 'iContact Api';

?>
<p>
    The following configuration is currently being used to connect to iContact:<br />
    App ID: <?= Yii::$app->params['iContact']['appId']; ?><br />
    API Username: <?= Yii::$app->params['iContact']['apiUsername']; ?><br />
    Environment: <?= isset(Yii::$app->params['iContact']['useSandbox']) && Yii::$app->params['iContact']['useSandbox'] ? 'Sandbox' : 'Production' ?>
</p>