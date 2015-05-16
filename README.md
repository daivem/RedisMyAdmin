# RedisMyAdmin
这个一个简单的Redis数据库在线管理工具

基于phpRedisAdmin使用CodeIgniter-2.2.0框架重新开发

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
