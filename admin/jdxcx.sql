-- phpMyAdmin SQL Dump
-- version 4.8.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: 2018-09-15 18:10:39
-- 服务器版本： 5.6.40-log
-- PHP Version: 7.0.31

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `jdxcx`
--

-- --------------------------------------------------------

--
-- 表的结构 `jd_address`
--

CREATE TABLE `jd_address` (
  `id` int(11) NOT NULL,
  `uid` int(11) NOT NULL DEFAULT '0' COMMENT '用户ID号',
  `realname` varchar(255) NOT NULL DEFAULT '' COMMENT '用户真实姓名',
  `tel` varchar(255) NOT NULL DEFAULT '' COMMENT '手机号',
  `address` varchar(500) NOT NULL DEFAULT '' COMMENT '用户地址',
  `name` varchar(255) NOT NULL DEFAULT '' COMMENT '地点名称',
  `detail` varchar(255) NOT NULL DEFAULT '' COMMENT '详细地址',
  `status` varchar(255) NOT NULL DEFAULT '0' COMMENT '地址状态；0：普通；1：默认地址',
  `latitude` varchar(255) NOT NULL DEFAULT '' COMMENT '纬度',
  `longitude` varchar(255) NOT NULL DEFAULT '' COMMENT '经度'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `jd_admin`
--

CREATE TABLE `jd_admin` (
  `uid` int(10) UNSIGNED NOT NULL COMMENT '管理员ID号',
  `username` varchar(255) NOT NULL DEFAULT '' COMMENT '管理员用户名/账号',
  `realname` varchar(255) NOT NULL DEFAULT '' COMMENT '姓名',
  `password` char(32) NOT NULL DEFAULT '' COMMENT '管理员登录密码',
  `email` varchar(255) NOT NULL DEFAULT '' COMMENT '管理员邮箱',
  `headerurl` varchar(255) NOT NULL DEFAULT '' COMMENT '头像地址',
  `key` char(18) NOT NULL DEFAULT '' COMMENT '加密参数key',
  `count` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '登陆次数',
  `ip` varchar(18) NOT NULL DEFAULT '' COMMENT '管理员上次登录ip',
  `status` tinyint(1) UNSIGNED NOT NULL DEFAULT '1' COMMENT '管理员状态；默认：1正常；0：禁用；-1：删除',
  `create_time` datetime NOT NULL COMMENT '账号添加时间',
  `update_time` datetime NOT NULL COMMENT '上次修改时间'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `jd_admin`
--

INSERT INTO `jd_admin` (`uid`, `username`, `realname`, `password`, `email`, `headerurl`, `key`, `count`, `ip`, `status`, `create_time`, `update_time`) VALUES
(1, 'admin', '乔宇', 'a477191b235b5b6dbfcd48b87fcfad05', '158994', 'http://img3.imgtn.bdimg.com/it/u=838463386,2763370289&fm=27&gp=0.jpg', 'FLN2j6Wyq9lUxSk7Rp', 37, '112.10.185.249', 1, '2018-07-03 14:30:05', '2018-07-04 14:29:36');

-- --------------------------------------------------------

--
-- 表的结构 `jd_assess`
--

CREATE TABLE `jd_assess` (
  `id` int(11) NOT NULL,
  `uid` int(11) NOT NULL DEFAULT '0' COMMENT '评价者用户ID号',
  `fid` int(11) NOT NULL DEFAULT '0' COMMENT '维修服务ID号',
  `content` text NOT NULL COMMENT '评价的内容',
  `create_time` varchar(20) NOT NULL DEFAULT '' COMMENT '评价时间',
  `score` tinyint(1) NOT NULL DEFAULT '0' COMMENT '评价分数；满分5分；一个星1分'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `jd_assess`
--

INSERT INTO `jd_assess` (`id`, `uid`, `fid`, `content`, `create_time`, `score`) VALUES
(175, 22, 67, '服务快，态度好', '2018.08.04', 5);

-- --------------------------------------------------------

--
-- 表的结构 `jd_collect`
--

CREATE TABLE `jd_collect` (
  `id` int(11) NOT NULL,
  `flag` varchar(255) NOT NULL DEFAULT '' COMMENT '收藏的类型；1：维修服务；2：废品回收站；3：附近商圈；4：品质货源',
  `s_id` int(11) NOT NULL DEFAULT '0' COMMENT '服务的ID号',
  `uid` int(11) NOT NULL DEFAULT '0' COMMENT '用户ID号'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `jd_collect`
--

INSERT INTO `jd_collect` (`id`, `flag`, `s_id`, `uid`) VALUES
(306, '2', 35, 22),
(307, '1', 67, 22);

-- --------------------------------------------------------

--
-- 表的结构 `jd_fixed`
--

CREATE TABLE `jd_fixed` (
  `id` int(10) UNSIGNED NOT NULL,
  `uid` int(11) NOT NULL DEFAULT '0' COMMENT '用户ID号',
  `des` text NOT NULL COMMENT '服务描述',
  `ability` text NOT NULL COMMENT '技能标签',
  `pics` text NOT NULL COMMENT '图片列表',
  `status` tinyint(4) NOT NULL DEFAULT '1' COMMENT '默认；1：正常；-1：下架',
  `view` int(11) NOT NULL DEFAULT '0',
  `create_time` datetime NOT NULL COMMENT '添加时间',
  `update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '上次更新时间'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `jd_fixed`
--

INSERT INTO `jd_fixed` (`id`, `uid`, `des`, `ability`, `pics`, `status`, `view`, `create_time`, `update_time`) VALUES
(67, 22, '从事家电维修行业数十年，有着丰富的行业经验，技术过硬。\n\n①精修家电：空调、冰箱、洗衣机、热水器、电视机等各种大小家电。\n\n②空调清洗、保养', '空调 冰箱 电视 洗衣机 热水器 空调清洗 空调加氟', '[{\"url\":\"/Uploads/images/201808/th_20180803171156_953.jpg\"},{\"url\":\"/Uploads/images/201808/th_20180803171336_420.jpg\"}]', 1, 61, '2018-08-03 17:24:49', '2018-09-11 05:58:53');

-- --------------------------------------------------------

--
-- 表的结构 `jd_fixed_order`
--

CREATE TABLE `jd_fixed_order` (
  `id` int(11) NOT NULL,
  `uid` int(11) NOT NULL DEFAULT '0' COMMENT '用户ID号',
  `fid` int(11) NOT NULL DEFAULT '0' COMMENT '维修服务的ID号',
  `addressdetail` varchar(700) NOT NULL DEFAULT '' COMMENT '预约单的详细信息',
  `remark` varchar(700) NOT NULL DEFAULT '' COMMENT '留言',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '[标志；1：待确认；2：待服务；3：待评价；4：已完成；5：已取消；0：全部]',
  `create_time` datetime NOT NULL COMMENT '预约时间',
  `update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '上次处理时间'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `jd_fixed_order`
--

INSERT INTO `jd_fixed_order` (`id`, `uid`, `fid`, `addressdetail`, `remark`, `status`, `create_time`, `update_time`) VALUES
(92, 25, 67, '{}', '涂抹', 5, '2018-08-23 21:11:37', '2018-09-11 06:00:17'),
(93, 22, 67, '{}', '哈哈哈', 5, '2018-09-11 13:59:28', '2018-09-11 06:00:08'),
(91, 22, 67, '{\"id\":\"14\",\"uid\":\"22\",\"realname\":\"孙乔雨\",\"tel\":\"17678328512\",\"address\":\"浙江省杭州市江干区下沙杭州市下沙第一小学(学林街南)\",\"name\":\"高沙小区\",\"detail\":\"56号\",\"status\":\"1\",\"latitude\":\"30.314495412797434\",\"longitude\":\"120.33753946423529\"}', '修空调', 4, '2018-08-04 09:21:49', '2018-08-04 01:23:05');

-- --------------------------------------------------------

--
-- 表的结构 `jd_menu`
--

CREATE TABLE `jd_menu` (
  `id` int(10) UNSIGNED NOT NULL COMMENT '菜单ID号',
  `pid` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '父级菜单ID号',
  `name` varchar(255) NOT NULL DEFAULT '' COMMENT '菜单名称',
  `status` tinyint(1) UNSIGNED NOT NULL DEFAULT '1' COMMENT '菜单状态；1：默认，显示；0：隐藏',
  `icon` varchar(255) NOT NULL DEFAULT '' COMMENT '图标',
  `url` varchar(255) NOT NULL DEFAULT '' COMMENT '菜单地址',
  `sort` tinyint(3) NOT NULL DEFAULT '0' COMMENT '排序；默认从大到小'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `jd_menu`
--

INSERT INTO `jd_menu` (`id`, `pid`, `name`, `status`, `icon`, `url`, `sort`) VALUES
(1, 0, '系统管理', 1, '&#xe620;', 'System/index', -5),
(2, 0, '管理员管理', 1, '&#xe612;', 'Admin/index', -3),
(3, 1, '菜单列表', 1, '', 'System/index', 0),
(24, 22, '后台首页', 1, '', 'Index/home', 0),
(20, 2, '管理员列表', 1, '', 'Admin/index', 0),
(37, 0, '用户管理', 1, '&#xe66f;', 'User/index', -4),
(39, 0, '系统公告', 1, '&#xe645;', 'Announce/index', 0),
(40, 39, '公告列表', 1, '', 'Announce/index', 0),
(41, 40, '添加公告', 1, '', 'Announce/add', 0),
(42, 40, '编辑公告', 1, '', 'Announce/edit', 0),
(48, 37, '用户列表', 1, '', 'User/index', 0),
(49, 37, '店铺列表', 1, '', 'User/shop', 0);

-- --------------------------------------------------------

--
-- 表的结构 `jd_recovery`
--

CREATE TABLE `jd_recovery` (
  `id` int(11) NOT NULL,
  `uid` int(11) NOT NULL DEFAULT '0' COMMENT '发布者ID号',
  `pics` varchar(700) NOT NULL DEFAULT '' COMMENT '图片',
  `des` text NOT NULL COMMENT '服务介绍',
  `view` int(11) NOT NULL DEFAULT '0' COMMENT '浏览次数',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1：默认正常通过；-1:下架',
  `address` varchar(255) NOT NULL DEFAULT '' COMMENT '地址；格式化',
  `name` varchar(255) NOT NULL DEFAULT '' COMMENT '地点名称',
  `detail` varchar(255) NOT NULL DEFAULT '' COMMENT '地点详细描述',
  `latitude` double NOT NULL DEFAULT '0' COMMENT '纬度',
  `longitude` double NOT NULL DEFAULT '0' COMMENT '经度',
  `create_time` datetime NOT NULL COMMENT '添加时间',
  `update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '上次更新时间'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `jd_recovery`
--

INSERT INTO `jd_recovery` (`id`, `uid`, `pics`, `des`, `view`, `status`, `address`, `name`, `detail`, `latitude`, `longitude`, `create_time`, `update_time`) VALUES
(35, 22, '[{\"url\":\"/Uploads/images/201808/th_20180803221210_943.jpg\"}]', '长期回收二手空调，工厂器械登废旧物资，价格从优', 19, 1, '浙江省杭州市江干区华中路', '横塘十区', '30号', 30.330101560885, 120.20457431674, '2018-08-03 22:12:17', '2018-09-01 08:55:04');

-- --------------------------------------------------------

--
-- 表的结构 `jd_serv`
--

CREATE TABLE `jd_serv` (
  `id` int(11) NOT NULL,
  `value` varchar(255) NOT NULL DEFAULT '' COMMENT '热门服务值',
  `name` varchar(255) NOT NULL DEFAULT '' COMMENT '名称',
  `check` tinyint(4) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `jd_serv`
--

INSERT INTO `jd_serv` (`id`, `value`, `name`, `check`) VALUES
(9, '空调', '空调', 0),
(10, '冰箱', '冰箱', 0),
(11, '热水器', '热水器', 0),
(12, '电视机', '电视机', 0),
(13, '手机', '手机', 0),
(14, '电脑', '电脑', 0);

-- --------------------------------------------------------

--
-- 表的结构 `jd_shop`
--

CREATE TABLE `jd_shop` (
  `id` int(10) UNSIGNED NOT NULL,
  `uid` int(11) NOT NULL DEFAULT '0' COMMENT '用户ID号',
  `shopname` varchar(255) NOT NULL DEFAULT '' COMMENT '店铺名',
  `name` varchar(255) NOT NULL DEFAULT '' COMMENT '店铺地址',
  `detail` varchar(255) DEFAULT NULL COMMENT '详细地址',
  `address` varchar(255) NOT NULL DEFAULT '' COMMENT '省市区',
  `latitude` varchar(255) NOT NULL DEFAULT '' COMMENT '纬度',
  `longitude` varchar(255) DEFAULT '' COMMENT '经度',
  `yyzz` varchar(255) NOT NULL DEFAULT '' COMMENT '营业执照图片',
  `idz` varchar(255) NOT NULL DEFAULT '' COMMENT '身份证正',
  `idf` varchar(255) NOT NULL DEFAULT '' COMMENT '身份证反',
  `status` tinyint(255) NOT NULL DEFAULT '0' COMMENT '店铺审核状态；0：默认，待审核；1：审核通过；-1：审核未通过',
  `create_time` datetime NOT NULL COMMENT '添加时间',
  `update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '上次更新时间'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `jd_shop`
--

INSERT INTO `jd_shop` (`id`, `uid`, `shopname`, `name`, `detail`, `address`, `latitude`, `longitude`, `yyzz`, `idz`, `idf`, `status`, `create_time`, `update_time`) VALUES
(11, 22, '洪涛家电制冷维修', '横塘十区', '30号', '浙江省杭州市江干区华中路', '30.330101560885286', '120.20457431674001', '/Uploads/images/201808/th_20180803170551_873.jpg', '', '/Uploads/images/201808/th_20180803170834_738.jpg', 1, '2018-08-03 17:09:36', '2018-08-03 09:09:55');

-- --------------------------------------------------------

--
-- 表的结构 `jd_token`
--

CREATE TABLE `jd_token` (
  `uid` int(10) UNSIGNED NOT NULL,
  `token` varchar(255) NOT NULL DEFAULT '' COMMENT '登录态校验码'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `jd_token`
--

INSERT INTO `jd_token` (`uid`, `token`) VALUES
(19, '5a871d2bc17dec9b473b7ff8ff564ec7'),
(20, '6c60d470114d4530f15fff00a01488d5'),
(21, '8681c5fabf7ec67a9524a50db32dc437'),
(22, 'af74147cc39588d60a95e64e5d06c5eb'),
(23, 'dec6fd1b45d53580b4eb0e015467d22f'),
(24, 'd3840483e689b1a89082ea5710ba7dae'),
(25, 'a6ad3dc35a0743d6cb5312a84822d527'),
(26, '64c39aab497309e13979295b6df9f938'),
(27, '9b4de942a9c9224a40803da3361dab51');

-- --------------------------------------------------------

--
-- 表的结构 `jd_user`
--

CREATE TABLE `jd_user` (
  `uid` int(10) UNSIGNED NOT NULL COMMENT '用户ID号',
  `realname` varchar(255) NOT NULL DEFAULT '' COMMENT '真实姓名',
  `nickname` varchar(255) NOT NULL DEFAULT '' COMMENT '用户昵称',
  `wx` varchar(255) NOT NULL DEFAULT '' COMMENT '微信号',
  `tel` varchar(11) DEFAULT NULL COMMENT '联系手机号',
  `rtel` varchar(20) NOT NULL DEFAULT '' COMMENT '用户注册手机号',
  `sex` tinyint(1) NOT NULL DEFAULT '0' COMMENT '默认；0：保密；1：男；2：女',
  `avatarurl` varchar(255) NOT NULL DEFAULT '' COMMENT '用户头像',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '用户状态；1：默认普通用户；-1：用户被拉黑',
  `create_time` datetime NOT NULL COMMENT '用户注册时间',
  `update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '上次更新时间'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `jd_user`
--

INSERT INTO `jd_user` (`uid`, `realname`, `nickname`, `wx`, `tel`, `rtel`, `sex`, `avatarurl`, `status`, `create_time`, `update_time`) VALUES
(22, '孙师傅', '瞎折腾', '13164957006', '13164957006', '17678328512', 1, '/Uploads/images/201808/th_20180803150554_963.jpg', 1, '2018-08-03 15:05:05', '2018-08-22 09:17:05'),
(23, '', '15724337732', '', '15724337732', '15724337732', 0, '', 1, '2018-08-10 10:44:44', '2018-08-10 02:44:53'),
(24, '', '15303191567', '', '15303191567', '15303191567', 0, '', 1, '2018-08-12 00:33:33', '2018-08-11 16:33:34'),
(25, '', '13221061810', '', '13221061810', '13221061810', 0, '', 1, '2018-08-23 21:11:11', '2018-08-23 13:11:30'),
(26, '', '13071820089', '', '13071820089', '13071820089', 0, '', 1, '2018-08-24 03:08:08', '2018-08-23 19:08:35'),
(27, '', '18514031330', '', '18514031330', '18514031330', 0, '', 1, '2018-08-30 19:16:16', '2018-08-30 11:16:27');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `jd_address`
--
ALTER TABLE `jd_address`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `jd_admin`
--
ALTER TABLE `jd_admin`
  ADD PRIMARY KEY (`uid`);

--
-- Indexes for table `jd_assess`
--
ALTER TABLE `jd_assess`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `jd_collect`
--
ALTER TABLE `jd_collect`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `jd_fixed`
--
ALTER TABLE `jd_fixed`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `jd_fixed_order`
--
ALTER TABLE `jd_fixed_order`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `jd_menu`
--
ALTER TABLE `jd_menu`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `jd_recovery`
--
ALTER TABLE `jd_recovery`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `jd_serv`
--
ALTER TABLE `jd_serv`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `jd_shop`
--
ALTER TABLE `jd_shop`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `jd_user`
--
ALTER TABLE `jd_user`
  ADD PRIMARY KEY (`uid`);

--
-- 在导出的表使用AUTO_INCREMENT
--

--
-- 使用表AUTO_INCREMENT `jd_address`
--
ALTER TABLE `jd_address`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- 使用表AUTO_INCREMENT `jd_admin`
--
ALTER TABLE `jd_admin`
  MODIFY `uid` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '管理员ID号', AUTO_INCREMENT=14;

--
-- 使用表AUTO_INCREMENT `jd_assess`
--
ALTER TABLE `jd_assess`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=176;

--
-- 使用表AUTO_INCREMENT `jd_collect`
--
ALTER TABLE `jd_collect`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=309;

--
-- 使用表AUTO_INCREMENT `jd_fixed`
--
ALTER TABLE `jd_fixed`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=68;

--
-- 使用表AUTO_INCREMENT `jd_fixed_order`
--
ALTER TABLE `jd_fixed_order`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=94;

--
-- 使用表AUTO_INCREMENT `jd_menu`
--
ALTER TABLE `jd_menu`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '菜单ID号', AUTO_INCREMENT=50;

--
-- 使用表AUTO_INCREMENT `jd_recovery`
--
ALTER TABLE `jd_recovery`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- 使用表AUTO_INCREMENT `jd_serv`
--
ALTER TABLE `jd_serv`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- 使用表AUTO_INCREMENT `jd_shop`
--
ALTER TABLE `jd_shop`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- 使用表AUTO_INCREMENT `jd_user`
--
ALTER TABLE `jd_user`
  MODIFY `uid` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '用户ID号', AUTO_INCREMENT=28;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
