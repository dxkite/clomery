<?php
namespace cn\atd3\user\dao;

use suda\archive\Table;
use suda\core\Query;
use suda\core\Request;

class UserDAO extends Table
{
    const ACTIVE=1;
    const FREEZE=0;
    const EXISTS_USER=-4;
    const EXISTS_EMAIL=-5;

    public function __construct()
    {
        parent::__construct(conf('module.tables.user', 'user'));
    }
    
    public function onBuildCreator($table)
    {
        return $table->fields(
            $table->field('id', 'bigint', 20)->primary()->unsigned()->auto(),
            $table->field('name', 'varchar', 255)->unique()->default(null)->comment("用户名"),
            $table->field('email', 'varchar', 255)->unique()->default(null)->comment("邮箱"),
            $table->field('password', 'varchar', 60)->default(null)->comment("密码"),
            $table->field('avatar', 'bigint', 20)->default(0)->comment("头像ID"),
            $table->field('group_id', 'bigint', 20)->key()->default(0)->comment("分组ID"),
            $table->field('valid_email', 'tinyint', 1)->key()->default(0)->comment("邮箱验证"),
            $table->field('signup_ip', 'varchar', 32)->comment("注册IP"),
            $table->field('signup_time', 'int', 11)->comment("注册时间"),
            $table->field('valid_token', 'varchar', 32)->comment("验证内容"),
            $table->field('valid_expire', 'int', 11)->comment("过期时间"),
            $table->field('status', 'tinyint', 1)->key()->default(0)->comment("用户状态")
        );
    }

    protected function _inputNameField($name)
    {
        if (!preg_match('/^[\w\x{4e00}-\x{9aff}]{4,255}$/u', $name)) {
            throw new UserException('invalid user name',UserException::NAME_FORMAT);
        }
        return $name;
    }

    protected function _inputEmailField($email)
    {
        if (!preg_match('/^([a-zA-Z0-9_\.\-\+])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/', $email)) {
            throw new UserException('invalid user email',UserException::EMAIL_FORMAT);
        }
        return $email;
    }
    /**
     * 通过用户名获取ID（不区分大小写）
     *
     * @param string $name
     * @return void
     */
    public function getByName(string $name)
    {
        return ($fetch=Query::where($this->getTableName(), ['id'], 'LOWER(name)=LOWER(:name)', ['name'=>$name])->fetch())?$fetch['id']:null;
    }

    /**
     * 通过用户名获取ID（不区分大小写）
     *
     * @param string $name
     * @return void
     */
    public function searchByName(string $name)
    {
        return $this->search('name', $name)->fetchAll();
    }

    /**
     * 通过用户名获取ID（不区分大小写）
     *
     * @param string $name
     * @return void
     */
    public function searchByEmail(string $email)
    {
        return $this->search('email', $email)->fetchAll();
    }

    /**
     * 通过用户邮箱获取ID（不区分大小写）
     *
     * @param string $email
     * @return void
     */
    public function getByEmail(string $email)
    {
        return ($fetch=Query::where($this->getTableName(), $this->getFields(), 'LOWER(email)=LOWER(:email)', ['email'=>$email])->fetch())?$fetch['id']:null;
    }

    /**
     * 通过ID获取信息
     *
     * @param string $name
     * @return void
     */
    public function getInfo(int $id)
    {
        return (new UserDAO)->setFields(['id','name','email','group_id'])->getByPrimaryKey($id);
    }

    /**
     * 设置状态
     *
     * @param int $id
     * @param int $status
     * @return void
     */
    public function setStatus(int $id, int $status)
    {
        return $this->updateByPrimaryKey($id, ['status'=>$status]);
    }
    
    public function add(string $name, string $email, string $password, int $group)
    {
        if (self::checkNameExists($name)) {
            return UserDAO::EXISTS_USER;
        }
        if (self::checkEmailExists($email)) {
            return UserDAO::EXISTS_EMAIL;
        }
        return $this->insert([
            'name'=>$name,
            'email'=>$email,
            'password'=>password_hash($password, PASSWORD_DEFAULT),
            'group_id'=>$group,
            'signup_time'=>time(),
            'signup_ip'=>Request::ip(),
            'status'=>UserDAO::ACTIVE,
            'valid_token'=>'',
            'valid_expire'=>'',
        ]);
    }

    public function edit(int $id, string $name, string $email, int $group, string $password=null)
    {
        if ($uid=self::checkNameExists($name)) {
            if ($uid!=$id) {
                return UserDAO::EXISTS_USER;
            }
        }
        if ($uid=self::checkEmailExists($email)) {
            if ($uid!=$id) {
                return UserDAO::EXISTS_EMAIL;
            }
        }
        $sets= [
            'name'=>$name,
            'email'=>$email,
            'group_id'=>$group,
            'signup_time'=>time(),
            'signup_ip'=>Request::ip(),
            'status'=>UserDAO::ACTIVE,
            'valid_token'=>'',
            'valid_expire'=>'',
        ];
        if (is_null($password)) {
            return $this->updateByPrimaryKey($id, $sets);
        }
        $sets['password']=password_hash($password, PASSWORD_DEFAULT);
        return $this->updateByPrimaryKey($id, $sets);
    }
    
    public function checkNameExists(string $name)
    {
        return $this->getByName($name);
    }

    public function checkEmailExists(string $email)
    {
        return $this->getByEmail($email);
    }

    public function checkTokenVaild(int $uid, string $token)
    {
        if ($user=$this->select(['valid_expire'], ['id'=>$uid,'valid_token'=>$token])->fetch()) {
            return $user['valid_expire']>time();
        }
        return false;
    }

    public function changePassword(int $userid, string $password)
    {
        return $this->updateByPrimaryKey($userid, [
            'password'=>password_hash($password, PASSWORD_DEFAULT),
            'valid_token'=>'',
            'valid_expire'=>'',
        ]);
    }

    public function checkPassword(int $id, string $password)
    {
        if ($user=$this->setFields(['password'])->getByPrimaryKey($id)) {
            if (password_verify($password, $user['password'])) {
                return true;
            }
        }
        return false;
    }

    public function refershToken(int $uid, string $token, int $valid_expire)
    {
        return $this->updateByPrimaryKey($uid, [
            'valid_token'=>$token,
            'valid_expire'=>$valid_expire,
        ]);
    }
}
