-- MySQL dump 10.13  Distrib 5.7.23, for osx10.9 (x86_64)
--
-- Host: localhost    Database: shop
-- ------------------------------------------------------
-- Server version	5.7.23

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `shop_address`
--

DROP TABLE IF EXISTS `shop_address`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shop_address` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(10) unsigned NOT NULL COMMENT '用户id',
  `province` varchar(20) CHARACTER SET utf8 NOT NULL COMMENT '省',
  `province_id` tinyint(1) unsigned NOT NULL COMMENT '省id',
  `city` varchar(20) CHARACTER SET utf8 NOT NULL COMMENT '市',
  `district` varchar(20) CHARACTER SET utf8 NOT NULL COMMENT '区',
  `detail` varchar(200) CHARACTER SET utf8 NOT NULL COMMENT '详细地址',
  `contact_number` varchar(20) CHARACTER SET utf8mb4 NOT NULL COMMENT '联系人电话',
  `contact_name` varchar(20) CHARACTER SET utf8mb4 NOT NULL COMMENT '联系人姓名',
  `isdefault` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否是默认地址',
  `create_time` int(10) unsigned NOT NULL COMMENT '添加时间',
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`) USING BTREE,
  KEY `create_time` (`create_time`) USING BTREE,
  KEY `contact_number` (`contact_number`) USING BTREE,
  KEY `province_id` (`province_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `shop_address`
--

LOCK TABLES `shop_address` WRITE;
/*!40000 ALTER TABLE `shop_address` DISABLE KEYS */;
INSERT INTO `shop_address` VALUES (8,13,'北京市',0,'北京市','通州区','康居家园','18355613657','laokiea11111',0,1544521532),(9,13,'北京市',0,'北京市','通州区','康居家园','18355613657','laokiea11111',0,1544521680),(10,13,'北京市',0,'北京市','通州区','康居家园','18355613659','laokiea11111',1,1544521693);
/*!40000 ALTER TABLE `shop_address` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `shop_attribute`
--

DROP TABLE IF EXISTS `shop_attribute`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shop_attribute` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '规格/属性id',
  `name` varchar(20) CHARACTER SET utf8 NOT NULL COMMENT '规格名称（口味，系列）',
  `create_time` int(10) unsigned NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `shop_attribute`
--

LOCK TABLES `shop_attribute` WRITE;
/*!40000 ALTER TABLE `shop_attribute` DISABLE KEYS */;
INSERT INTO `shop_attribute` VALUES (1,'口味',1543406899),(2,'系列',1543406917),(7,'长度',1543494558);
/*!40000 ALTER TABLE `shop_attribute` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `shop_attribute_value`
--

DROP TABLE IF EXISTS `shop_attribute_value`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shop_attribute_value` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '商品规格id',
  `attr_id` int(10) unsigned NOT NULL COMMENT '商品id',
  `name` varchar(20) CHARACTER SET utf8 NOT NULL COMMENT '规格值',
  `create_time` int(10) unsigned NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `attr_id_value` (`attr_id`,`name`) USING BTREE,
  KEY `attr_id` (`attr_id`) USING BTREE,
  KEY `attr_value` (`name`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `shop_attribute_value`
--

LOCK TABLES `shop_attribute_value` WRITE;
/*!40000 ALTER TABLE `shop_attribute_value` DISABLE KEYS */;
INSERT INTO `shop_attribute_value` VALUES (1,1,'薄荷味',1543406899),(2,1,'苹果味',1543406916),(3,2,'国风系列',1543406970),(4,7,'10cm',1543728721),(5,7,'12cm',1543728721),(6,7,'15cm',1543728770);
/*!40000 ALTER TABLE `shop_attribute_value` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `shop_banner`
--

DROP TABLE IF EXISTS `shop_banner`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shop_banner` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `link_type` varchar(10) CHARACTER SET utf8mb4 NOT NULL DEFAULT '' COMMENT 'banner类型',
  `link_value` varchar(200) CHARACTER SET utf8mb4 NOT NULL COMMENT 'url',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'banner状态,1为显示',
  `create_time` int(10) unsigned NOT NULL COMMENT '创建时间',
  `position` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '顺序',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `type` (`link_type`) USING BTREE,
  KEY `value` (`link_value`(191)) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `shop_banner`
--

LOCK TABLES `shop_banner` WRITE;
/*!40000 ALTER TABLE `shop_banner` DISABLE KEYS */;
INSERT INTO `shop_banner` VALUES (1,'good_id','32',1,1544109120,1,1544109203),(3,'good_id','32',1,1545039846,2,1545047020);
/*!40000 ALTER TABLE `shop_banner` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `shop_car`
--

DROP TABLE IF EXISTS `shop_car`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shop_car` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(10) unsigned NOT NULL COMMENT '用户id',
  `good_id` int(10) unsigned NOT NULL COMMENT '商品id(冗余数据)',
  `segment_id` int(10) unsigned NOT NULL COMMENT '细分商品id',
  `good_count` int(10) unsigned NOT NULL COMMENT '商品数量',
  `add_time` int(10) unsigned NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_car_good` (`uid`,`good_id`,`segment_id`) USING BTREE,
  KEY `uid` (`uid`) USING BTREE,
  KEY `good_id` (`good_id`) USING BTREE,
  KEY `segment_id` (`segment_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `shop_car`
--

LOCK TABLES `shop_car` WRITE;
/*!40000 ALTER TABLE `shop_car` DISABLE KEYS */;
INSERT INTO `shop_car` VALUES (12,13,32,42,1,1544772108);
/*!40000 ALTER TABLE `shop_car` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `shop_good`
--

DROP TABLE IF EXISTS `shop_good`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shop_good` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '商品主键',
  `category_id` int(10) unsigned NOT NULL COMMENT '商品分类id',
  `model` varchar(10) CHARACTER SET utf8mb4 NOT NULL DEFAULT '' COMMENT '型号',
  `name` varchar(100) CHARACTER SET utf8mb4 NOT NULL DEFAULT '' COMMENT '商品名称',
  `description` varchar(200) CHARACTER SET utf8mb4 NOT NULL DEFAULT '' COMMENT '商品描述',
  `stock` int(10) unsigned NOT NULL COMMENT '基本库存',
  `price` decimal(10,2) unsigned NOT NULL COMMENT '基本价格',
  `salesnum` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '销量',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '商品状态',
  `create_time` int(10) unsigned NOT NULL COMMENT '创建时间',
  `update_time` int(10) unsigned NOT NULL COMMENT '上次修改时间',
  `name_pinyin` varchar(200) CHARACTER SET utf8mb4 NOT NULL DEFAULT '' COMMENT '商品名拼音',
  `description_pinyin` varchar(200) CHARACTER SET utf8mb4 NOT NULL DEFAULT '' COMMENT '描述拼音',
  `views` int(20) unsigned NOT NULL DEFAULT '0' COMMENT '访问量',
  `tag` tinyint(1) unsigned NOT NULL DEFAULT '3' COMMENT '商品标签（1-热卖 2-促销 3-新品）',
  `props` varchar(100) CHARACTER SET utf8mb4 DEFAULT NULL COMMENT '额外属性',
  `nodiscount` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否受用户等级折扣影响',
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique1` (`model`,`name`) USING BTREE,
  KEY `category_id` (`category_id`) USING BTREE,
  KEY `name` (`name`) USING BTREE,
  KEY `description` (`description`(191)) USING BTREE,
  KEY `desc_pinyin` (`description_pinyin`(191)) USING BTREE,
  KEY `name_pinyin` (`name_pinyin`(191)) USING BTREE,
  KEY `stock` (`stock`) USING BTREE,
  KEY `price` (`price`) USING BTREE,
  KEY `salesnum` (`salesnum`) USING BTREE,
  KEY `views` (`views`) USING BTREE,
  KEY `tag` (`tag`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=37 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `shop_good`
--

LOCK TABLES `shop_good` WRITE;
/*!40000 ALTER TABLE `shop_good` DISABLE KEYS */;
INSERT INTO `shop_good` VALUES (32,1,'Nt-pass','电子烟-pass1','passpasspass',20,99.99,3,0,1544364188,1545796426,'dianziyanpass1','passpasspass',10,1,NULL,1),(36,1,'','电子烟4','',20,99.99,0,0,1545124786,1545132712,'dianziyan4','',0,1,NULL,0);
/*!40000 ALTER TABLE `shop_good` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `shop_good_category`
--

DROP TABLE IF EXISTS `shop_good_category`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shop_good_category` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '分类表主键',
  `name` varchar(20) CHARACTER SET utf8 NOT NULL COMMENT '分类名称',
  `parent_id` int(10) unsigned NOT NULL COMMENT '腹肌分类id',
  `status` tinyint(1) NOT NULL COMMENT '分类状态',
  `create_time` int(10) unsigned NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`),
  KEY `name` (`name`) USING BTREE,
  KEY `parent_id` (`parent_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `shop_good_category`
--

LOCK TABLES `shop_good_category` WRITE;
/*!40000 ALTER TABLE `shop_good_category` DISABLE KEYS */;
INSERT INTO `shop_good_category` VALUES (1,'电子烟',0,0,1543406899);
/*!40000 ALTER TABLE `shop_good_category` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `shop_good_comment`
--

DROP TABLE IF EXISTS `shop_good_comment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shop_good_comment` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `order_id` int(10) unsigned NOT NULL,
  `good_id` int(10) unsigned NOT NULL COMMENT '商品id',
  `uid` int(10) unsigned NOT NULL COMMENT '用户id',
  `segment_id` int(10) unsigned NOT NULL COMMENT '细分商品id',
  `comment` varchar(255) NOT NULL DEFAULT '' COMMENT '评价内容',
  `add_time` int(10) unsigned NOT NULL COMMENT '评价时间',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '评价状态 1好评 2中评 3差评',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uid_orderid` (`uid`,`order_id`),
  KEY `good_id` (`good_id`),
  KEY `uid` (`uid`),
  KEY `segment_id` (`segment_id`),
  KEY `addtime` (`add_time`),
  KEY `order_id` (`order_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `shop_good_comment`
--

LOCK TABLES `shop_good_comment` WRITE;
/*!40000 ALTER TABLE `shop_good_comment` DISABLE KEYS */;
/*!40000 ALTER TABLE `shop_good_comment` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `shop_good_segment`
--

DROP TABLE IF EXISTS `shop_good_segment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shop_good_segment` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `good_id` int(10) unsigned NOT NULL COMMENT '商品id',
  `price` decimal(10,2) unsigned NOT NULL COMMENT '价格',
  `stock` int(10) unsigned NOT NULL COMMENT '库存',
  `salesnum` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '销量',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '商品状态',
  `create_time` int(10) unsigned NOT NULL COMMENT '创建时间',
  `update_time` int(10) unsigned NOT NULL COMMENT '更新时间',
  `attr_ids` varchar(20) CHARACTER SET utf8mb4 DEFAULT NULL,
  `attr_value_ids` varchar(20) CHARACTER SET utf8mb4 DEFAULT NULL,
  `cost_price` decimal(10,2) unsigned DEFAULT NULL COMMENT '成本价',
  PRIMARY KEY (`id`),
  UNIQUE KEY `attr_value_ids` (`attr_value_ids`) USING BTREE,
  KEY `good_id` (`good_id`) USING BTREE,
  KEY `price` (`price`) USING BTREE,
  KEY `stock` (`stock`) USING BTREE,
  KEY `salesnum` (`salesnum`),
  KEY `attr_ids` (`attr_ids`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=54 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `shop_good_segment`
--

LOCK TABLES `shop_good_segment` WRITE;
/*!40000 ALTER TABLE `shop_good_segment` DISABLE KEYS */;
INSERT INTO `shop_good_segment` VALUES (42,32,119.90,20,1,-1,1544364188,1544364817,'1_2_7','1_3_4',NULL),(43,32,99.90,40,2,-1,1544364188,1544364817,'1_2_7','2_3_6',NULL),(48,32,10.99,10,0,-1,1545124786,1545711183,'1_2','1_3',0.00),(49,32,19.99,10,0,0,1545124786,1545711183,'1_2','2_3',0.00);
/*!40000 ALTER TABLE `shop_good_segment` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `shop_good_segment_attribute`
--

DROP TABLE IF EXISTS `shop_good_segment_attribute`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shop_good_segment_attribute` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `good_id` int(10) unsigned NOT NULL COMMENT '商品id',
  `segment_id` int(10) unsigned NOT NULL COMMENT '商品细分表id',
  `attr_id` int(10) unsigned NOT NULL COMMENT '属性id',
  `attr_value_id` int(10) unsigned NOT NULL COMMENT '规格值表id',
  PRIMARY KEY (`id`),
  UNIQUE KEY `seg_attr_value` (`segment_id`,`attr_id`,`attr_value_id`),
  KEY `segment_id` (`segment_id`) USING BTREE,
  KEY `attr_value_id` (`attr_value_id`) USING BTREE,
  KEY `attr_id` (`attr_id`) USING BTREE,
  KEY `good_id` (`good_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=150 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `shop_good_segment_attribute`
--

LOCK TABLES `shop_good_segment_attribute` WRITE;
/*!40000 ALTER TABLE `shop_good_segment_attribute` DISABLE KEYS */;
INSERT INTO `shop_good_segment_attribute` VALUES (37,32,42,1,1),(38,32,42,2,3),(39,32,42,7,4),(40,32,43,1,2),(41,32,43,2,3),(42,32,43,7,6),(146,32,48,1,1),(147,32,48,2,3),(148,32,49,1,2),(149,32,49,2,3);
/*!40000 ALTER TABLE `shop_good_segment_attribute` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `shop_manage_member`
--

DROP TABLE IF EXISTS `shop_manage_member`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shop_manage_member` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键id',
  `username` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '用户名',
  `password` varchar(128) CHARACTER SET utf8 NOT NULL COMMENT '密码',
  `status` tinyint(1) NOT NULL COMMENT '状态',
  `group` tinyint(2) unsigned NOT NULL COMMENT '组id',
  `create_time` int(10) unsigned NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`),
  KEY `group` (`group`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `shop_manage_member`
--

LOCK TABLES `shop_manage_member` WRITE;
/*!40000 ALTER TABLE `shop_manage_member` DISABLE KEYS */;
INSERT INTO `shop_manage_member` VALUES (1,'admin','$2y$10$nQZPtzdzzYjSh5zy6fF17uPAcPzCx8vmVuJUM52YzEknHFijwg6jm',0,1,1543478855);
/*!40000 ALTER TABLE `shop_manage_member` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `shop_member`
--

DROP TABLE IF EXISTS `shop_member`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shop_member` (
  `id` int(12) unsigned NOT NULL AUTO_INCREMENT COMMENT '用户id',
  `username` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '用户昵称',
  `mobile` varchar(11) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL COMMENT '手机号',
  `lastUid` int(12) unsigned NOT NULL DEFAULT '0' COMMENT '上级用户id',
  `level` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '会员等级',
  `property_ratio` decimal(4,2) unsigned zerofill NOT NULL DEFAULT '00.00' COMMENT '佣金返现比例',
  `sex` varchar(6) CHARACTER SET utf8 DEFAULT NULL COMMENT '性别',
  `location` varchar(10) CHARACTER SET utf8 DEFAULT NULL COMMENT '所在地（省 直辖市）',
  `age` tinyint(3) unsigned DEFAULT NULL COMMENT '年龄',
  `avatar_status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否上传自定义头像',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '用户状态 0为正常',
  `create_time` int(10) unsigned NOT NULL COMMENT '注册时间',
  `last_login_time` int(10) unsigned DEFAULT NULL COMMENT '上次登录时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `username_index` (`username`) USING BTREE,
  UNIQUE KEY `mobile_index` (`mobile`) USING BTREE,
  KEY `level_index` (`level`) USING BTREE,
  KEY `age_index` (`age`) USING BTREE,
  KEY `location_index` (`location`) USING BTREE,
  KEY `lastUid_index` (`lastUid`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `shop_member`
--

LOCK TABLES `shop_member` WRITE;
/*!40000 ALTER TABLE `shop_member` DISABLE KEYS */;
INSERT INTO `shop_member` VALUES (12,'laokiea1','18355613698',0,1,00.00,'male','',18,0,0,1542891041,1545294039),(13,'onemorething','18355613657',12,2,00.00,NULL,'',NULL,0,0,1543376622,1545294039),(16,'kikokiko','18355613658',0,1,00.00,NULL,NULL,NULL,0,0,1545142937,1545294039);
/*!40000 ALTER TABLE `shop_member` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `shop_member_account`
--

DROP TABLE IF EXISTS `shop_member_account`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shop_member_account` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(10) unsigned NOT NULL COMMENT '用户id',
  `account` varchar(32) CHARACTER SET utf8mb4 NOT NULL COMMENT '账号',
  `type` varchar(10) CHARACTER SET utf8mb4 NOT NULL COMMENT '账号类型',
  `bind_time` int(11) unsigned NOT NULL COMMENT '绑定时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `shop_member_account`
--

LOCK TABLES `shop_member_account` WRITE;
/*!40000 ALTER TABLE `shop_member_account` DISABLE KEYS */;
INSERT INTO `shop_member_account` VALUES (3,13,'123456789','bank',1545662213);
/*!40000 ALTER TABLE `shop_member_account` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `shop_member_count`
--

DROP TABLE IF EXISTS `shop_member_count`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shop_member_count` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'id',
  `uid` int(10) unsigned NOT NULL COMMENT '用户id',
  `return_money` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '佣金',
  `balance` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '余额',
  `credit` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '积分',
  `redbag` decimal(10,0) unsigned NOT NULL DEFAULT '0' COMMENT '红包金额',
  `consume_money` decimal(12,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '用户总消费金额',
  `property` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '总资产(佣金+红包)',
  `property_financing_income` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '资产理财收益',
  `property_financing` tinyint(1) NOT NULL DEFAULT '0' COMMENT '资产是否开启理财',
  `property_financing_start` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '理财开始计算时间',
  `property_financing_expire` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '理财到期时间',
  `property_financing_remain` smallint(3) unsigned NOT NULL DEFAULT '0' COMMENT '理财剩余天数',
  `property_financing_ratio` decimal(4,1) unsigned NOT NULL DEFAULT '0.0' COMMENT '资产理财收益率(暂时不用，先统一设置)',
  `property_financing_basemoney` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '计算利息的本金，为申请理财那一刻的资产金额',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uid` (`uid`),
  KEY `return` (`return_money`) USING BTREE,
  KEY `balance` (`balance`) USING BTREE,
  KEY `credit` (`credit`) USING BTREE,
  KEY `return_income` (`property_financing_income`) USING BTREE,
  KEY `redbag` (`redbag`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `shop_member_count`
--

LOCK TABLES `shop_member_count` WRITE;
/*!40000 ALTER TABLE `shop_member_count` DISABLE KEYS */;
INSERT INTO `shop_member_count` VALUES (1,16,0.00,0.00,0,0,0.00,79.97,0.00,0,0,0,0,0.0,0.00),(2,13,0.00,0.00,33,0,1000.10,1330.62,0.60,0,0,0,0,0.0,0.00);
/*!40000 ALTER TABLE `shop_member_count` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `shop_member_log_property`
--

DROP TABLE IF EXISTS `shop_member_log_property`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shop_member_log_property` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `uid` int(10) NOT NULL COMMENT '用户id',
  `amount` decimal(10,2) NOT NULL COMMENT '金额',
  `log_time` int(10) unsigned NOT NULL COMMENT 'log时间',
  `description` varchar(255) CHARACTER SET utf8mb4 NOT NULL DEFAULT '' COMMENT '描述',
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `shop_member_log_property`
--

LOCK TABLES `shop_member_log_property` WRITE;
/*!40000 ALTER TABLE `shop_member_log_property` DISABLE KEYS */;
INSERT INTO `shop_member_log_property` VALUES (1,13,-8.01,1545820641,'资产提现'),(2,13,0.12,1545904305,'理财收益'),(3,13,0.12,1545904513,'理财收益'),(4,13,0.12,1545904661,'理财收益'),(5,13,0.12,1545904807,'理财收益'),(6,13,0.12,1545904833,'理财收益'),(8,13,-8.01,1546091035,'资产提现'),(9,13,-8.01,1546091926,'资产提现');
/*!40000 ALTER TABLE `shop_member_log_property` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `shop_member_log_redbag`
--

DROP TABLE IF EXISTS `shop_member_log_redbag`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shop_member_log_redbag` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(10) unsigned NOT NULL COMMENT '用户id',
  `amount` decimal(10,2) unsigned NOT NULL COMMENT '记录金额',
  `log_time` int(12) unsigned NOT NULL COMMENT '时间',
  `related_id` int(10) unsigned NOT NULL COMMENT '关联活动id',
  `total` decimal(10,2) unsigned NOT NULL COMMENT '红包总额',
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`) USING BTREE,
  KEY `dateline` (`log_time`) USING BTREE,
  KEY `amount` (`amount`) USING BTREE,
  KEY `related_id` (`related_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `shop_member_log_redbag`
--

LOCK TABLES `shop_member_log_redbag` WRITE;
/*!40000 ALTER TABLE `shop_member_log_redbag` DISABLE KEYS */;
/*!40000 ALTER TABLE `shop_member_log_redbag` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `shop_member_log_return`
--

DROP TABLE IF EXISTS `shop_member_log_return`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shop_member_log_return` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(10) unsigned NOT NULL COMMENT '用户id',
  `amount` decimal(10,2) unsigned NOT NULL COMMENT '记录金额',
  `log_time` int(12) unsigned NOT NULL COMMENT '时间',
  `order_id` int(10) unsigned NOT NULL COMMENT '订单id',
  PRIMARY KEY (`id`),
  UNIQUE KEY `order_id` (`order_id`),
  KEY `uid` (`uid`) USING BTREE,
  KEY `dateline` (`log_time`) USING BTREE,
  KEY `amount` (`amount`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `shop_member_log_return`
--

LOCK TABLES `shop_member_log_return` WRITE;
/*!40000 ALTER TABLE `shop_member_log_return` DISABLE KEYS */;
/*!40000 ALTER TABLE `shop_member_log_return` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `shop_member_log_undeposit`
--

DROP TABLE IF EXISTS `shop_member_log_undeposit`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shop_member_log_undeposit` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(10) unsigned NOT NULL COMMENT '用户id',
  `amount` decimal(10,2) unsigned NOT NULL COMMENT '记录金额',
  `log_time` int(12) unsigned NOT NULL COMMENT '时间',
  `status` tinyint(3) NOT NULL DEFAULT '0' COMMENT '提现操作进度状态',
  `account_id` tinyint(1) unsigned NOT NULL COMMENT '收款账户id',
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`) USING BTREE,
  KEY `dateline` (`log_time`) USING BTREE,
  KEY `amount` (`amount`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `shop_member_log_undeposit`
--

LOCK TABLES `shop_member_log_undeposit` WRITE;
/*!40000 ALTER TABLE `shop_member_log_undeposit` DISABLE KEYS */;
INSERT INTO `shop_member_log_undeposit` VALUES (9,13,8.01,1545662274,1,3);
/*!40000 ALTER TABLE `shop_member_log_undeposit` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `shop_member_message`
--

DROP TABLE IF EXISTS `shop_member_message`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shop_member_message` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(10) unsigned NOT NULL,
  `message` varchar(255) NOT NULL DEFAULT '',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `message_time` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `shop_member_message`
--

LOCK TABLES `shop_member_message` WRITE;
/*!40000 ALTER TABLE `shop_member_message` DISABLE KEYS */;
INSERT INTO `shop_member_message` VALUES (5,13,'您的订单已发货，运单号为123456789',1,1546091974),(6,13,'您的退货申请已同意，请将商品连同购物单寄至地址%s',1,1546135544),(7,13,'您的退货申请已拒绝, 原因: 订单超时',1,1546136664),(8,13,'您的退款申请已通过, 退款将退至您绑定的账户，如果您没有绑定账户，请至个人中心绑定',0,1546164986),(9,13,'您的退款申请已拒绝, 原因: 订单超时',0,1546165437),(10,13,'您的退款申请已拒绝, 原因: 订单超时',0,1546254651);
/*!40000 ALTER TABLE `shop_member_message` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `shop_order`
--

DROP TABLE IF EXISTS `shop_order`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shop_order` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '订单表主键',
  `order_no` varchar(64) CHARACTER SET utf8mb4 NOT NULL COMMENT '订单编号',
  `uid` int(10) unsigned NOT NULL COMMENT '用户id',
  `order_status` tinyint(1) NOT NULL COMMENT '订单状态 0待付款,1已付款,2已发货,3已签收,-1退货申请,-2退货中,-3已退货,-4取消交易',
  `address_id` int(1) unsigned NOT NULL COMMENT '地址id',
  `pay_channel` tinyint(1) unsigned NOT NULL COMMENT '支付渠道 1-支付宝 2-微信',
  `source` varchar(10) CHARACTER SET utf8mb4 NOT NULL COMMENT '支付来源 app web wap wx',
  `escrow_trade_no` varchar(64) CHARACTER SET utf8mb4 DEFAULT ' ' COMMENT '三方平台交易单号',
  `remark` varchar(255) CHARACTER SET utf8mb4 NOT NULL COMMENT '用户备注',
  `discount` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '优惠金额',
  `create_time` int(10) unsigned NOT NULL COMMENT '创建时间',
  `update_time` int(10) unsigned NOT NULL COMMENT '修改时间',
  `postage` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '邮费',
  `order_total_money` decimal(10,2) unsigned NOT NULL COMMENT '订单总价',
  `order_good_total_money` decimal(10,2) unsigned NOT NULL COMMENT '订单商品总价',
  `pay_time` int(10) unsigned DEFAULT '0' COMMENT '支付时间',
  `track_no` varchar(32) CHARACTER SET utf8mb4 NOT NULL DEFAULT '' COMMENT '运单号',
  PRIMARY KEY (`id`),
  UNIQUE KEY `order_no` (`order_no`) USING BTREE,
  KEY `uid` (`uid`) USING BTREE,
  KEY `order_status` (`order_status`) USING BTREE,
  KEY `pay_channel` (`pay_channel`) USING BTREE,
  KEY `source` (`source`) USING BTREE,
  KEY `escrow_trade_no` (`escrow_trade_no`) USING BTREE,
  KEY `order_total_money` (`order_total_money`) USING BTREE,
  KEY `track_no` (`track_no`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `shop_order`
--

LOCK TABLES `shop_order` WRITE;
/*!40000 ALTER TABLE `shop_order` DISABLE KEYS */;
INSERT INTO `shop_order` VALUES (3,'2018122600001315',13,-4,8,1,'app','','',9.12,1545192869,1545491124,25.99,336.57,319.70,0,''),(5,'2018125400001320',13,-4,8,1,'app',' ','速发货',11.24,1544794676,1545019901,25.99,336.57,319.70,0,''),(6,'2018129200001321',13,-4,8,1,'app',' ','',9.12,1545019788,1545019901,25.99,336.57,319.70,0,''),(7,'2018124400001322',13,1,8,1,'app',' ','',9.12,1545301600,1546254651,25.99,336.57,319.70,0,'123456789'),(10,'2018120400001323',13,2,8,1,'app','ch_9Si1GGuzvHGKnDeL0KXrolq5','',9.12,1545794064,1545796426,25.99,336.57,319.70,1545796426,'123456789');
/*!40000 ALTER TABLE `shop_order` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `shop_order_good`
--

DROP TABLE IF EXISTS `shop_order_good`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shop_order_good` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `uid` int(10) unsigned NOT NULL COMMENT '用户id',
  `order_id` int(10) unsigned NOT NULL COMMENT '订单表id',
  `category_id` int(10) unsigned NOT NULL COMMENT '分类id',
  `good_id` int(10) unsigned NOT NULL COMMENT '商品id',
  `segment_id` int(10) unsigned NOT NULL COMMENT '细分商品id',
  `good_name` varchar(100) CHARACTER SET utf8mb4 NOT NULL COMMENT '商品名称',
  `good_count` smallint(10) unsigned NOT NULL COMMENT '商品数量',
  `good_price` decimal(10,2) unsigned NOT NULL COMMENT '细分商品价格',
  `good_money` decimal(10,2) unsigned NOT NULL COMMENT '商品总价',
  `good_model` varchar(100) CHARACTER SET utf8mb4 NOT NULL COMMENT '商品型号',
  `attr_value_ids` varchar(20) CHARACTER SET utf8mb4 NOT NULL COMMENT '属性id',
  `category_name` varchar(20) CHARACTER SET utf8mb4 NOT NULL COMMENT '分类名称',
  `good_description` varchar(100) CHARACTER SET utf8mb4 DEFAULT '' COMMENT '商品描述',
  PRIMARY KEY (`id`),
  UNIQUE KEY `order_segment` (`order_id`,`segment_id`) USING BTREE,
  KEY `uid` (`uid`) USING BTREE,
  KEY `good_id` (`good_id`) USING BTREE,
  KEY `order_id` (`order_id`) USING BTREE,
  KEY `segment_id` (`segment_id`) USING BTREE,
  KEY `good_total_money` (`good_money`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `shop_order_good`
--

LOCK TABLES `shop_order_good` WRITE;
/*!40000 ALTER TABLE `shop_order_good` DISABLE KEYS */;
INSERT INTO `shop_order_good` VALUES (2,13,3,1,32,42,'电子烟-pass1',1,119.90,119.90,'Nt-pass','1_3_4','','passpasspass'),(3,13,3,1,32,43,'电子烟-pass1',2,99.90,199.80,'Nt-pass','2_3_6','','passpasspass'),(4,13,5,1,32,42,'电子烟-pass1',1,119.90,119.90,'Nt-pass','1_3_4','','passpasspass'),(5,13,5,1,32,43,'电子烟-pass1',2,99.90,199.80,'Nt-pass','2_3_6','','passpasspass'),(6,13,6,1,32,42,'电子烟-pass1',1,119.90,119.90,'Nt-pass','1_3_4','电子烟','passpasspass'),(7,13,6,1,32,43,'电子烟-pass1',2,99.90,199.80,'Nt-pass','2_3_6','电子烟','passpasspass'),(8,13,7,1,32,42,'电子烟-pass1',1,119.90,119.90,'Nt-pass','1_3_4','电子烟','passpasspass'),(9,13,7,1,32,43,'电子烟-pass1',2,99.90,199.80,'Nt-pass','2_3_6','电子烟','passpasspass'),(10,13,10,1,32,42,'电子烟-pass1',1,119.90,119.90,'Nt-pass','1_3_4','电子烟','passpasspass'),(11,13,10,1,32,43,'电子烟-pass1',2,99.90,199.80,'Nt-pass','2_3_6','电子烟','passpasspass');
/*!40000 ALTER TABLE `shop_order_good` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `shop_orderno_unique`
--

DROP TABLE IF EXISTS `shop_orderno_unique`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shop_orderno_unique` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '增长id',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `shop_orderno_unique`
--

LOCK TABLES `shop_orderno_unique` WRITE;
/*!40000 ALTER TABLE `shop_orderno_unique` DISABLE KEYS */;
INSERT INTO `shop_orderno_unique` VALUES (1),(2),(3),(4),(5),(6),(7),(8),(9),(10),(11),(14),(15),(20),(21),(22),(23);
/*!40000 ALTER TABLE `shop_orderno_unique` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2019-01-01 23:13:12
