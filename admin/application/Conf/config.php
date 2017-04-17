<?php
return array(
	//'配置项'=>'配置值'
	'URL_MODEL' => 2,
	//'APP_GROUP_LIST'=>'Index',
	//手机短信安全码
	'pCMS_Kind' => 'Model_youxia',
	//定义主题目录
	'DEFAULT_THEME'=>'default', 
	// 默认输出编码
	'DEFAULT_CHARSET' => 'UTF-8', 
	// 数据库配置,模块的配置在模块对于的model里面设置
	'URL_PATHINFO_DEPR'=>'/',
	//配置后缀
	'TMPL_TEMPLATE_SUFFIX' => '.tpl',
    'DB_PORT' => '3306',
    'DB_PREFIX' => 'yx_',
    'DB_CHARSET' => 'UTF8', // 数据库编码默认采用UTF
	// 项目设置
	'MEMCACHE_STATUS' => TRUE,
    'BEANSDB_STATUS' => TRUE,
	/* 语言设置 */
    'LANG_SWITCH_ON'        => TRUE,   // 默认关闭多语言包功能
    'LANG_AUTO_DETECT'      => FALSE,   // 自动侦测语言 开启多语言功能后有效

	// url 模式
	'URL_DISPATCH_ON' => true,
	// 不区分大小写
	'URL_CASE_INSENSITIVE' =>   TRUE,
	// 自动加载
	'APP_AUTOLOAD_PATH'=> '@.Common.,@.Model.,',
	//模板分隔符
	'TMPL_L_DELIM'          => '<{',			// 模板引擎普通标签开始标记
    'TMPL_R_DELIM'          => '}>',			// 模板引擎普通标签结束标记

	// 调试模式
	'APP_DEBUG' => True ,	// 
	'SESSION_AUTO_START' => true, //是否开启session
	'SHOW_DB_TIMES'=> FALSE,  	// 
	'DB_FIELDTYPE_CHECK'=>FALSE,  // 开启字段类型验证	
	'VAR_FILTERS'=>'htmlspecialchars',  //系统变量的全局过滤

	// 此项目相关的配置
	'app_fastapply_channelname' => 'CHANNEL_CO',//合作会员的一键申请资源的系统渠道
	'LOAD_EXT_CONFIG' => 'config_db,web',
);