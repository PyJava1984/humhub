<?php

use humhub\modules\friendship\models\Friendship;
use humhub\modules\space\models\Space;
use tests\codeception\_pages\LoginPage;
use humhub\modules\user\models\User;
use humhub\modules\user\models\GroupPermission;
use yii\helpers\Url;

/**
 * Inherited Methods
 * @method void wantToTest($text)
 * @method void wantTo($text)
 * @method void execute($callable)
 * @method void expectTo($prediction)
 * @method void expect($prediction)
 * @method void amGoingTo($argumentation)
 * @method void am($role)
 * @method void lookForwardTo($achieveValue)
 * @method void comment($description)
 * @method \Codeception\Lib\Friend haveFriend($name, $actorClass = NULL)
 *
 * @SuppressWarnings(PHPMD)
 */
class FunctionalTester extends \Codeception\Actor
{

    use _generated\FunctionalTesterActions;

    public function amAdmin($logout = false)
    {
        if ($logout) {
            $this->logout();
        }

        LoginPage::openBy($this)->login('admin', 'test');
        $this->see('Dashboard');
        $this->see('Administration');
    }

    public function setGroupPermission($groupId, $permission, $state = 1)
    {
        if(is_string($permission)) {
            $permission = Yii::createObject(['class' => $permission]);
        }

        (new GroupPermission([
            'permission_id' => $permission->id,
            'group_id' => $groupId,
            'module_id' => $permission->moduleId,
            'class' => get_class($permission),
            'state' => $state
        ]))->save();

        \Yii::$app->user->getPermissionManager()->clear();
    }

    public function assertSpaceAccessStatus($userGroup, $status, $path, $params = [], $post = false)
    {
        $space = $this->loginBySpaceUserGroup($userGroup, $path, $params, $post);
        $this->seeResponseCodeIs($status);
        $this->logout();
        return $space;
    }

    public function assertSpaceAccessTrue($userGroup, $path, $params = [], $post = false)
    {
        $space = $this->loginBySpaceUserGroup($userGroup, $path, $params, $post);
        $this->seeSuccessResponseCode();
        $this->logout();
        return $space;
    }

    public function seeSuccessResponseCode()
    {
        $this->seeResponseCodeIsBetween(200, 308);
    }

    public function assertSpaceAccessFalse($userGroup, $path, $params = [], $post = false)
    {
        $space = $this->loginBySpaceUserGroup($userGroup, $path, $params, $post);
        $this->dontSeeResponseCodeIs(200);
        $this->logout();
        return $space;
    }

    /**
     * This utility function finds a space membership relation of the given $userGroup and logs in the user and
     * also access the space and returns the related space model.
     *
     * @param $userGroup
     * @return Space|null
     */
    public function loginBySpaceUserGroup($userGroup, $path = null, $params = [], $post = false)
    {
        $spaceId = null;
        $user = null;
        switch($userGroup) {
            case 'root':
                $spaceId = 2;
                $user = 'Admin';
                break;
            case Space::USERGROUP_OWNER:
                $spaceId = 2;
                $user = 'User1';
                break;
            case Space::USERGROUP_ADMIN:
                $spaceId = 4;
                $user = 'User1';
                break;
            case Space::USERGROUP_MODERATOR:
                $spaceId = 3;
                $user = 'User2';
                break;
            case Space::USERGROUP_MEMBER:
                $spaceId = 3;
                $user = 'User1';
                break;
            case Space::USERGROUP_GUEST:
                $spaceId = 1;
                $user = 'User1';
                break;
        }

        $space = Space::findOne(['id' => $spaceId]);


        $this->amUser($user);
        $this->amOnSpace($space, $path, $params, $post);
        return $space;
    }


    public function amUser($user = null, $password = '123qwe', $logout = false)
    {
        if ($logout) {
            $this->logout();
        }

        if ($user == null) {
            $this->amUser1();
        } else {
            if(strtolower($user) == 'admin') {
                $password = 'test';
            }
            LoginPage::openBy($this)->login($user, $password);
            tests\codeception\_pages\DashboardPage::openBy($this);
            $this->see('Dashboard');
        }
    }

    public function amUser1($logout = false)
    {
        $this->amUser('User1', '123qwe', $logout);
    }

    public function amUser2($logout = false)
    {
        $this->amUser('User2', '123qwe', $logout);
    }

    public function amUser3($logout = false)
    {
        $this->amUser('User3', '123qwe', $logout);
    }

    public function logout()
    {
        $this->amGoingTo('logout');
        \Yii::$app->user->logout(true);
    }

    public function enableFriendships($enable = true)
    {
        Yii::$app->getModule('friendship')->settings->set('enable', $enable);
    }

    public function switchIdentity($username)
    {
        Yii::$app->user->switchIdentity(User::findOne(['username' => $username]));
    }

    public function amFriendWith($username)
    {
        $user = User::findOne(['username' => $username]);
        Friendship::add($user, Yii::$app->user->identity);
        Friendship::add(Yii::$app->user->identity, $user);
    }

    public function follow($username)
    {
        User::findOne(['username' => $username])->follow();
    }

    public function setProfileField($field, $value)
    {
        $user = Yii::$app->user->identity;
        $user->profile->setAttributes([$field => $value]);
        $user->profile->save();
    }

    public function amOnSpace1($path = '/space/space', $params = [], $post = false)
    {
        $this->amOnSpace(1, $path, $params, $post);
    }

    public function amOnSpace2($path = '/space/space', $params = [], $post = false)
    {
        $this->amOnSpace(2, $path, $params, $post);
    }

    public function amOnSpace3($path = '/space/space', $params = [], $post = false)
    {
        $this->amOnSpace(3, $path, $params, $post);
    }

    public function amOnSpace4($path = '/space/space', $params = [], $post = false)
    {
        $this->amOnSpace(4, $path, $params, $post);
    }

    public $spaces = [
        '5396d499-20d6-4233-800b-c6c86e5fa34a',
        '5396d499-20d6-4233-800b-c6c86e5fa34b',
        '5396d499-20d6-4233-800b-c6c86e5fa34c',
        '5396d499-20d6-4233-800b-c6c86e5fa34d',
    ];

    public function amOnSpace($guid, $path = '/space/space', $params = [], $post = false)
    {
        if(is_bool($params)) {
            $post = $params;
            $params = [];
        }

        if(!$path) {
            $path = '/space/space';
        }

        if(is_int($guid)) {
            $guid = $this->spaces[--$guid];
        } else if($guid instanceof Space) {
            $guid = $guid->guid;
        }

        $params['cguid'] = $guid;

        if($post) {
            $route =  array_merge([$path], $params);
            $this->sendAjaxPostRequest(Url::toRoute($route), (is_array($post) ? $post : []));
        } else {
            $this->amOnRoute($path, $params);
        }

    }

    public function enableModule($guid, $moduleId)
    {
        if(is_int($guid)) {
            $guid = $this->spaces[--$guid];
        }

        $space = Space::findOne(['guid' => $guid]);
        $space->enableModule($moduleId);
        Yii::$app->moduleManager->flushCache();
        \humhub\modules\space\models\Module::flushCache();
    }

}
