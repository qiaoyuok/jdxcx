/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 50553
Source Host           : localhost:3306
Source Database       : jdxcx

Target Server Type    : MYSQL
Target Server Version : 50553
File Encoding         : 65001

Date: 2018-08-01 22:44:13
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for jd_user
-- ----------------------------
DROP TABLE IF EXISTS `jd_user`;
CREATE TABLE `jd_user` (
  `uid` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '用户ID号',
  `realname` varchar(255) NOT NULL DEFAULT '' COMMENT '真实姓名',
  `nickname` varchar(255) NOT NULL DEFAULT '' COMMENT '用户昵称',
  `wx` varchar(255) NOT NULL DEFAULT '' COMMENT '微信号',
  `tel` varchar(20) NOT NULL DEFAULT '' COMMENT '用户openid',
  `sex` tinyint(1) NOT NULL DEFAULT '0' COMMENT '默认；0：保密；1：男；2：女',
  `avatarurl` varchar(255) NOT NULL DEFAULT '' COMMENT '用户头像',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '用户状态；1：默认普通用户；-1：用户被拉黑',
  `create_time` datetime NOT NULL COMMENT '用户注册时间',
  `update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '上次更新时间',
  PRIMARY KEY (`uid`)
) ENGINE=MyISAM AUTO_INCREMENT=18 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of jd_user
-- ----------------------------
INSERT INTO `jd_user` VALUES ('17', '', '瞎折腾的够呛', '', '17678328512', '1', '/Uploads/images/201808/th_20180801163507_759.jpg', '1', '2018-07-31 23:25:25', '2018-08-01 16:35:08');


drop procedure if exists test;
delimiter $$
create procedure test()
begin
declare a int(10) DEFAULT 10;
select a;
set a = a + 1;
select a;
end
$$
delimiter ;

drop procedure if exists test;
delimiter $$
create procedure test(in a int)
	begin
	select a;
	set a = a+1;
	select a;
	end
	$$
delimiter ;

drop procedure if exists test;
delimiter $$
create procedure test(in a int)
	begin
	if a<10 then 
		select '小毛孩';
	elseif a>10 and a<18 then
		select '差不多懂事了';
	else
		select "老激活了";
	end if;
	end
	$$
delimiter ;

drop procedure if exists test;
delimiter $$
create procedure test()
	begin
	declare a int default 1;
	while a<=10000 do
		insert into user values(null,0);
		set a = a + 1;
	end while;
	end
	$$
delimiter ;

drop procedure if exists deal;
delimiter $$
create procedure deal()
	begin
	declare imin int(10) default 1;
	declare imax int(10) default 1;
	select min(id) into imin from user;
	select max(id) into imax from user;
	repeat 
		if imin % 2 = 0 then
			update user set mark = 1 where id = imin;
		end if;
		set imin = imin + 1;
		until imin > imax
	end repeat;
	end
	$$
delimiter ;


drop procedure if exists deal1;
delimiter $$
create procedure deal1()
	begin
	declare imin int(10) default 1;
	declare imax int(10) default 1;
	select min(id) into imin from user;
	select max(id) into imax from user;
	myloop:loop
		if imin % 2 = 0 then
			update user set mark = 2 where id = imin;
		end if;
		set imin = imin + 1;
	if imin>imax then
		leave myloop;
	end if;
	end loop;
	end;
	$$
delimiter ;

delimiter $$
create procedure erroedeal1()
	begin
	declare continue handler for sqlstate '23000' set @a = 1;
	insert into user values(20001,0);
	insert into user values(null,0);
	end;
	$$
delimiter ;

delimiter $$
drop function if exists demo;
create function demo(a int,b int)
	returns int
	begin
	return a+b;
	end
	$$
delimiter ;

drop trigger if exists t_demo;
delimiter $$
create trigger t_demo after insert
	on user for each row
	begin
		declare username varchar(60);
		set username = new.username;
		insert into test values(null,username);
	end
	$$
delimiter ;

delimiter $$
    create trigger t_goods before update
    on goods for each row
    begin
    declare goods_num int(10) default 0;
    set goods_num = old.goods_num;
    select goods_num;
    end
    $$
 delimiter ;
drop table if exists account;
create table account(
   id int(11) not null auto_increment,
   name varchar(50) not null,
   money decimal(9,2) not null default 0,
   primary key (id)
)ENGINE=InnoDB DEFAULT CHARSET = utf8;

insert into account values(null,'孙乔雨',5000);
insert into account values(null,'张三丰',3000);

drop table if exists testloop1;
create table testloop1(
   id int(11) not null AUTO_INCREMENT,
   name varchar(50) not null,
   primary key (id)
)ENGINE=InnoDB DEFAULT CHARSET = utf8;

drop procedure if exists testloop2;
delimiter $$
	create procedure testloop2()
		begin
		declare a int(10) default 1;
		repeat
			insert into testloop1 values(null,'bbb');
			set a = a + 1;
		until a>1000000
		end repeat;
		end
		$$
delimiter ;

drop table if exists eventtable;
create table eventtable(
	id int(11) not null auto_increment,
	time varchar(30) not null,
	primary key (id)
)engine=InnoDB DEFAULT CHARSET = utf8;

drop procedure if exists e_insert;
delimiter $$
create procedure e_insert()
	begin
	insert into eventtable values(null,now());
	end
	$$
delimiter ;

drop event if exists test_event;
create event test_event
	on schedule every 1 second
	on completion preserve disable
	do call e_insert;

alter event test_event on completion preserve enable;