CREATE TABLE `zsd_sms_record` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `account` VARCHAR(16) NOT NULL COMMENT '发送账号' COLLATE 'utf8mb4_unicode_ci',
  `extno` VARCHAR(16) NOT NULL COMMENT '接入号，即SP服务号（106XXXXXX）' COLLATE 'utf8mb4_unicode_ci',
  `sign_name` VARCHAR(191) NOT NULL COMMENT '短信签名' COLLATE 'utf8mb4_unicode_ci',
  `content` VARCHAR(2000) NOT NULL COMMENT '短信内容' COLLATE 'utf8mb4_unicode_ci',
  `status` TINYINT(4) NOT NULL COMMENT '请求结果，',
  `status_desc` VARCHAR(191) NOT NULL COMMENT '请求结果说明' COLLATE 'utf8mb4_unicode_ci',
  `balance` INT(11) NOT NULL COMMENT '当前账户余额',
  `mobile` VARCHAR(2400) NOT NULL DEFAULT '' COMMENT '发送的手机号码，多个逗号分隔' COLLATE 'utf8mb4_unicode_ci',
  `create_time` TIMESTAMP NULL DEFAULT NULL,
  `update_time` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  INDEX `zsd_sms_records_status_index` (`status`),
  INDEX `zsd_sms_records_status_desc_index` (`status_desc`)
)
  COMMENT='众视达短信发送记录表'
  COLLATE='utf8mb4_unicode_ci'
  ENGINE=InnoDB ;

CREATE TABLE `zsd_sms_item` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `record_id` INT(11) NOT NULL COMMENT '短信发送记录ID',
  `mid` VARCHAR(32) NOT NULL DEFAULT '' COMMENT '消息ID（用于状态报告匹配）' COLLATE 'utf8mb4_unicode_ci',
  `mobile` VARCHAR(16) NOT NULL DEFAULT '' COMMENT '手机号' COLLATE 'utf8mb4_unicode_ci',
  `result` TINYINT(4) NOT NULL DEFAULT '0' COMMENT '短信请求结果',
  `result_desc` VARCHAR(16) NOT NULL DEFAULT '' COMMENT '短信请求结果描述' COLLATE 'utf8mb4_unicode_ci',
  `spid` VARCHAR(16) NOT NULL DEFAULT '' COMMENT '归属账号' COLLATE 'utf8mb4_unicode_ci',
  `access_code` VARCHAR(16) NOT NULL DEFAULT '' COMMENT '下发号码' COLLATE 'utf8mb4_unicode_ci',
  `stat` VARCHAR(16) NOT NULL DEFAULT '' COMMENT '状态报告代码' COLLATE 'utf8mb4_unicode_ci',
  `stat_desc` VARCHAR(191) NOT NULL DEFAULT '' COMMENT '状态报告代码描述' COLLATE 'utf8mb4_unicode_ci',
  `report_time` DATETIME NULL DEFAULT NULL COMMENT '状态报告时间',
  `create_time` TIMESTAMP NULL DEFAULT NULL,
  `update_time` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  INDEX `zsd_sms_items_record_id_index` (`record_id`),
  INDEX `zsd_sms_items_mid_index` (`mid`),
  INDEX `zsd_sms_items_mobile_index` (`mobile`)
)
  COMMENT='众视达短信发送详情表'
  COLLATE='utf8mb4_unicode_ci'
  ENGINE=InnoDB ;
