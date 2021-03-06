<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2018 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\user\widgets;

use humhub\modules\friendship\models\Friendship;
use humhub\modules\ui\widgets\CounterSetItem;
use humhub\modules\ui\widgets\CounterSet;
use humhub\modules\user\models\User;
use Yii;
use yii\helpers\Url;


/**
 * Class ProfileHeaderCounter
 *
 * @since 1.3
 * @package humhub\modules\user\widgets
 */
class ProfileHeaderCounterSet extends CounterSet
{

    /**
     * @var User
     */
    public $user;

    /**
     * @inheritdoc
     */
    public function init()
    {
        if (Yii::$app->getModule('friendship')->getIsEnabled()) {
            $this->counters[] = new CounterSetItem([
                'label' => Yii::t('UserModule.widgets_views_profileHeader', 'Friends'),
                'value' => Friendship::getFriendsQuery($this->user)->count(),
                'url' => Url::to(['/friendship/list/popup', 'userId' => $this->user->id]),
                'linkOptions' => ['data-target' => '#globalModal']
            ]);
        }

        if (!Yii::$app->getModule('user')->disableFollow) {
            $this->counters[] = new CounterSetItem([
                'label' => Yii::t('UserModule.widgets_views_profileHeader', 'Followers'),
                'value' => $this->user->getFollowerCount(),
                'url' => Url::to(['/user/profile/follower-list', 'container' => $this->user]),
                'linkOptions' => ['data-target' => '#globalModal']
            ]);

            $this->counters[] = new CounterSetItem([
                'label' => Yii::t('UserModule.widgets_views_profileHeader', 'Following'),
                'value' => $this->user->getFollowingCount(User::class),
                'url' => Url::to(['/user/profile/followed-users-list', 'container' => $this->user]),
                'linkOptions' => ['data-target' => '#globalModal']
            ]);
        }

        $this->counters[] = new CounterSetItem([
            'label' => Yii::t('UserModule.widgets_views_profileHeader', 'Spaces'),
            'value' => $this->user->getFollowingCount(User::class),
            'url' => Url::to(['/user/profile/space-membership-list', 'container' => $this->user]),
            'linkOptions' => ['data-target' => '#globalModal']
        ]);

        parent::init();
    }

}
