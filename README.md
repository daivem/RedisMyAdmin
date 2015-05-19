# RedisMyAdmin
这个一个简单的Redis数据库在线管理工具

本项目参考于phpRedisAdmin，部分核心代码源自于此项目

RedisMyAdmin最早于唯品会内部开源（v0.1），现使用CodeIgniter-2.2.0框架重新开发并在github开源

Demo地址：
http://demo.redismyadmin.org

-----------------------------------------------

主要配置文件

application/config/config_global.php  # 全局配置

application/config/config_redis.php # 服务器列表配置

application/config/config_auth.php # 登录及验证码配置

-----------------------------------------------

基于原版的改进：

1、对整体界面进行了汉化。

2、增加了对db的支持。

3、优化建树性能，树型结构每层皆为异步加载。

4、支持简单的登录校验，支持中文/英文登录验证码。


-----------------------------------------------
更新日志：

v0.2.1

修正不同redis版本中对保存时间的key不同而产生的错误

修正无法多服务器跳转的问题

——————————————————

v0.2

使用CI-2.2重新开发，并修正v0.1版中的BUG
