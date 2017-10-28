# chess
有关代码的信息

本项目是用于学习和测试用的，难免会出现bug

环境要求：php>=5.6

代码中用的redis（需要安装redis）是3.0，所以最好是同样版本的redis.

代码中使用thinkphp3.2框架和window版的workerman插件，只能在window系统下运行，在linxu系统下运行可能会出现错误，
另外还需安装php_redis扩展，用于操作redis数据库

使用方法：<br>
1.将tp文件夹下的全部文件复制到apache或nginx等服务器的根目录下<br>
2.process文件夹可放在你电脑上的任何位置，双击process文件夹下的run.bat,此操作前需要配置php的环境变量<br>
3.在浏览器输入http://127.0.0.1/ 就可以访问了

