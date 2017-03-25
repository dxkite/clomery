<?php

namespace cn\atd3;

use suda\core\Query;

class Lostfound
{
	                                                                                                                                                                                                                                                                                               	
	/**
	* add A Item
	* @return  the id of item
	*/
    public static function add(string $name,string $where,string $discription,int $user,string $qq,string $phone,int $time,int $type,int $check,int $found)
    {
        return Query::insert('lostfound',['name'=>$name,'where'=>$where,'discription'=>$discription,'user'=>$user,'qq'=>$qq,'phone'=>$phone,'time'=>$time,'type'=>$type,'check'=>$check,'found'=>$found]);
    }
	
	/**
	*  	Delete A Item By Primary Key
	*	@return  rows
	*/
	public static function delete(int $id){
        return Query::delete('lostfound',['id'=>$id]);
    }
	
	/**
	*  	get A Item By Primary Key 
	* 	@return  item
	*/  
	public static function get(int $id)
    {
        return ($get=Query::where('lostfound', ['id','name','where','discription','user','qq','phone','time','type','check','found'],['id'=>$id])->fetch()) ? $get  : false;
    }
	
	/**
	*  	get A Item By Primary Key 
	* 	@return  item
	*/  
	public static function count()
    {
        return Query::count('lostfound');
    }
	
	 
	
	/**
	* Get By name varchar(100)  
	*/ 
	public static function getByName(string $name)
    {
        return ($get=Query::where('lostfound', ['id','where','discription','user','qq','phone','time','type','check','found'],['name'=>$name])->fetch()) ? $get  : false;
    }
	
	 
	
	/**
	* Get By where varchar(255)  
	*/ 
	public static function getByWhere(string $where)
    {
        return ($get=Query::where('lostfound', ['id','name','discription','user','qq','phone','time','type','check','found'],['where'=>$where])->fetch()) ? $get  : false;
    }
	
	 
	
	/**
	* Get By time int(11)  
	*/ 
	public static function getByTime(int $time)
    {
        return ($get=Query::where('lostfound', ['id','name','where','discription','user','qq','phone','type','check','found'],['time'=>$time])->fetch()) ? $get  : false;
    }
	
	 
	
	/**
	* Get By type tinyint(1)  
	*/ 
	public static function getByType(int $type)
    {
        return ($get=Query::where('lostfound', ['id','name','where','discription','user','qq','phone','time','check','found'],['type'=>$type])->fetch()) ? $get  : false;
    }
	
	 
	
	/**
	* Get By check tinyint(1)  
	*/ 
	public static function getByCheck(int $check)
    {
        return ($get=Query::where('lostfound', ['id','name','where','discription','user','qq','phone','time','type','found'],['check'=>$check])->fetch()) ? $get  : false;
    }
	
	 
	
	/**
	* Get By found tinyint(1)  
	*/ 
	public static function getByFound(int $found)
    {
        return ($get=Query::where('lostfound', ['id','name','where','discription','user','qq','phone','time','type','check'],['found'=>$found])->fetch()) ? $get  : false;
    }
	
	 
    public static function update(int $id,string $name=null,string $where=null,string $discription=null,int $user=null,string $qq=null,string $phone=null,int $time=null,int $type=null,int $check=null,int $found=null){
	   $sets=[];
	    
	   if  (!is_null($id))
	   {
		   $sets['id']=$id;
	   }
        
	   if  (!is_null($name))
	   {
		   $sets['name']=$name;
	   }
        
	   if  (!is_null($where))
	   {
		   $sets['where']=$where;
	   }
        
	   if  (!is_null($discription))
	   {
		   $sets['discription']=$discription;
	   }
        
	   if  (!is_null($user))
	   {
		   $sets['user']=$user;
	   }
        
	   if  (!is_null($qq))
	   {
		   $sets['qq']=$qq;
	   }
        
	   if  (!is_null($phone))
	   {
		   $sets['phone']=$phone;
	   }
        
	   if  (!is_null($time))
	   {
		   $sets['time']=$time;
	   }
        
	   if  (!is_null($type))
	   {
		   $sets['type']=$type;
	   }
        
	   if  (!is_null($check))
	   {
		   $sets['check']=$check;
	   }
        
	   if  (!is_null($found))
	   {
		   $sets['found']=$found;
	   }
        
       return Query::update('lostfound',$sets,['id'=>$id]); 
    }
	 
    public static function set(int $id,array $data){
	   foreach($data as $name=>$value){
			if (!in_array($name,['id','name','where','discription','user','qq','phone','time','type','check','found'])){
				return false;
			}
	   }
       return Query::update('lostfound',$data,['id'=>$id]); 
    }
    public static function list(int $page=1, int $count=10)
    {
        return Query::where('lostfound', ['id','name','where','discription','user','qq','phone','time','type','check','found'], '1', [], [$page, $count])->fetchAll();
    }
	
  
	/**
	* list By name varchar(100)  
	*/ 
	public static function listByName(string $name,int $page=1, int $count=10)
    { 
		return ($get=Query::where('lostfound', ['id','name','where','discription','user','qq','phone','time','type','check','found'], ' `name` LIKE CONCAT("%",:name,"%") ',['name'=>$name],[$page, $count])->fetchAll()) ? $get  : false; 
	}
 
	/**
	* list By where varchar(255)  
	*/ 
	public static function listByWhere(string $where,int $page=1, int $count=10)
    { 
		return ($get=Query::where('lostfound', ['id','name','where','discription','user','qq','phone','time','type','check','found'], ' `where` LIKE CONCAT("%",:where,"%") ',['where'=>$where],[$page, $count])->fetchAll()) ? $get  : false; 
	}
 
	/**
	* list By time int(11)  
	*/ 
	public static function listByTime(int $time,int $page=1, int $count=10)
    { 
        return ($get=Query::where('lostfound', ['id','name','where','discription','user','qq','phone','type','check','found'],['time'=>$time],[],[$page, $count])->fetchAll()) ? $get  : false; 
	}
 
	/**
	* list By type tinyint(1)  
	*/ 
	public static function listByType(int $type,int $page=1, int $count=10)
    { 
        return ($get=Query::where('lostfound', ['id','name','where','discription','user','qq','phone','time','check','found'],['type'=>$type],[],[$page, $count])->fetchAll()) ? $get  : false; 
	}
 
	/**
	* list By check tinyint(1)  
	*/ 
	public static function listByCheck(int $check,int $page=1, int $count=10)
    { 
        return ($get=Query::where('lostfound', ['id','name','where','discription','user','qq','phone','time','type','found'],['check'=>$check],[],[$page, $count])->fetchAll()) ? $get  : false; 
	}
 
	/**
	* list By found tinyint(1)  
	*/ 
	public static function listByFound(int $found,int $page=1, int $count=10)
    { 
        return ($get=Query::where('lostfound', ['id','name','where','discription','user','qq','phone','time','type','check'],['found'=>$found],[],[$page, $count])->fetchAll()) ? $get  : false; 
	}
 

  
	/**
	* list By name varchar(100)  
	*/ 
	public static function countIfName(string $name)
    { 
		return Query::count('lostfound', ' `name` LIKE CONCAT("%",:name,"%") ',['name'=>$name] ); 
	}
 
	/**
	* list By where varchar(255)  
	*/ 
	public static function countIfWhere(string $where)
    { 
		return Query::count('lostfound', ' `where` LIKE CONCAT("%",:where,"%") ',['where'=>$where] ); 
	}
 
	/**
	* list By time int(11)  
	*/ 
	public static function countIfTime(int $time)
    { 
        return Query::count('lostfound', ['time'=>$time] ); 
	}
 
	/**
	* list By type tinyint(1)  
	*/ 
	public static function countIfType(int $type)
    { 
        return Query::count('lostfound', ['type'=>$type] ); 
	}
 
	/**
	* list By check tinyint(1)  
	*/ 
	public static function countIfCheck(int $check)
    { 
        return Query::count('lostfound', ['check'=>$check] ); 
	}
 
	/**
	* list By found tinyint(1)  
	*/ 
	public static function countIfFound(int $found)
    { 
        return Query::count('lostfound', ['found'=>$found] ); 
	}
 
}

/**
* DTA FILE:
; 丢失物品
id bigint(20) auto primary 
name varchar(100) key comment="丢失的物品名"
where varchar(255) key comment="丢失地点"
discription text comcomment="其他描述"
user bigint(20) comment="发布的人" 
qq varchar(12) comment="联系QQ"
phone varchar(11) comment="手机号"
time int(11) key comment="发布时间"
type tinyint(1) key comment="丢失或者找回"
check tinyint(1) key comment="是否通过审核"
found tinyint(1) key comment="是否被找到"
*/