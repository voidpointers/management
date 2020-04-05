# 部署

## 依赖环境

- php7.0 +
- mysql5.6 +

- Windows环境可使用集成环境 XAMPP https://www.apachefriends.org/index.html

## 拉取代码

```shell
git clone https://{email}:{password}@gitee.com/hiywy/ywysys_back_v2.git
```

备注
{email} 邮箱【@需要转换为%40。例: vip%40qq.com】
{password} 密码

## 初始化

> 项目根目录 Linux系统示例：/www/ywysys_back_v2 Windows系统示例： C:\www\ywysys_back_v2

### 设置可写权限【日志，图片上传】

```shell
cd /www/ywysys_back_v2

chmod -R 777 storage
```

### 安装composer扩展包

> composer安装教程：https://www.phpcomposer.com/

```shell
cd /www/ywysys_back_v2

composer install
```

### 创建数据库

连接数据库后执行

```sql
CREATE DATABASE `ywysys` DEFAULT CHARACTER SET = `utf8mb4`;
```

### 配置 .env 文件

> 执行以下命令或手动拷贝将 .env.example 为 .env

```shell
cd /www/ywysys_back_v2

cp .env.example .env
```

使用vim 或者其他文本编辑器打开.env文件

```php

# 配置数据库
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
# 数据库名
DB_DATABASE=ywysys
# 数据库账号
DB_USERNAME=root
# 数据库密码
DB_PASSWORD=password

# 配置API域名(二选一)
API_DOMAIN=http://api.createos.xyz
# 使用ip+端口(二选一) 例 http://127.0.0.1:8000/api
API_PREFIX=api

```

### 数据初始化

- 生成数据表

```shell
php artisan migrate
```

- 初始化数据

```shell
php artisan db:seed
```

### 自动同步订单【可使用接口手动拉取】

> 可根据需要做成定时任务，一分钟执行一次，以Linux Crontab为例

```shell
*/1 * * * * php /www/ywysys_back_v2/artisan receipt:pull page --page=1 --limit=10 --shop=16407439 > /dev/null 2>&1
```

## 配置WebServer

- 前端界面

/www/ywysys_web

- 后端API【注意：这里需要配置到public目录】

/www/ywysys_back_v2/public

- 图片服务路径

/www/ywysys_back_v2/storage/app/public

### Nginx 配置示例

```nginx

# 前端
server
{
  server_name pre.admin.createos.xyz;
  listen 80;
  root  /www/ywysys_web;

  location / {
    try_files $uri $uri/ /index.html;
  }

  location = /index.html {
    add_header Cache-Control "no-cache, no-store";
  }
}

# 后端API
server
{
  server_name pre.api.createos.xyz;
  listen 80;

  root /www/ywysys_back_v2/public;

  # 配置图片URL
  location /images {
    root /www/pre/ywysys_back_v2/storage/app/public;
  }

  location ~ .*\.(php|php5)?$
  {
    fastcgi_pass  php:9000;
    fastcgi_index index.php;
    include fastcgi_params;
  }

  location / {
    try_files $uri $uri/ /index.php$is_args$query_string;
  }
}
```

### Apache 配置示例【以类 Unix系统为例，windows步骤类似】

- 开启虚拟主机配置【去掉#号注释即可】

```config
# Virtual hosts
Include /private/etc/apache2/extra/httpd-vhosts.conf
```

LoadModule rewrite_module libexec/apache2/mod_rewrite.so

- 配置站点

> /etc/apache2/extra/httpd-vhosts.conf

```
<VirtualHost *:80>
    DocumentRoot "/www/ywysys_back_v2/public"
    ServerName api.createos.xyz
    ErrorLog "/private/var/log/apache2/createos.one-error_log"
    CustomLog "/private/var/log/apache2/createos.one-access_log" common
</VirtualHost>
```

