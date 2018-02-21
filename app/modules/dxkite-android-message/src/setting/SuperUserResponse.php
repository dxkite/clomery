<?php
namespace dxkite\android\message\response\setting;

use dxkite\support\visitor\Context;
use dxkite\support\template\Manager;
use dxkite\user\table\UserTable;
use dxkite\support\visitor\Permission;
use dxkite\support\visitor\GrantManager;

class SuperUserResponse extends \dxkite\support\setting\Response
{
    public function onAdminView($view, $context)
    {
        if (setting('super-user-id') == 0) {
            $permissions=(new Permission)->getSystemPermissions();
            if ($data=table('role')->select('id', ['name'=>'超级用户'])->fetch()) {
                $id=  $data['id'];
            } else {
                $id= (new GrantManager)->createRole('超级用户', new Permission($permissions));
            }
            if ($data=table('user')->select(['id'], ['name'=>'SuperDXkite'])->fetch()) {
                $userId = $data['id'];
            } else {
                $userId=table('user')->insert([
                    'name'=>'SuperDXkite',
                    'signupTime'=>time(),
                    'signupIp'=>request()->ip(),
                    'status'=>UserTable::STATUS_ACTIVE,
                ]);
            }
            (new GrantManager)->grant($id, $userId);
            setting_set('super-user-id', $userId);
        }
        if (empty(setting('super-user-token')) || request()->get('refresh')) {
            setting_set('super-user-token', md5('dxkite'.time()));
        }
        return true;
    }

    public function adminContent($template)
    {
        \suda\template\Manager::include('android-message:setting/superuser', $template)->render();
    }
}
