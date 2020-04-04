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

### 设置可写权限【日志，图片上传】

chmod -R 777 storage

### 安装composer扩展包

> composer安装教程：https://www.phpcomposer.com/

```shell
composer install
```

### 创建数据库

```sql
CREATE DATABASE `ywysys` DEFAULT CHARACTER SET = `utf8mb4`;
```

### 配置 .env 文件

> 执行命令 cp .env.example .env 或手动拷贝将 .env.example 为 .env

```php

# 配置数据库
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ywysys
DB_USERNAME=root
DB_PASSWORD=password

# 配置API域名(二选一)
API_DOMAIN=http://api.createos.xyz
# ip+端口使用(二选一)
API_PREFIX=api

```

### 数据初始化

- 生成JWT SECRET

> 管理员登录令牌

```shell
php artisan jwt:secret
```

- 生成数据表

```shell
php artisan migrate
```

- 初始化数据

  - 管理员
  - 国家列表
  - 物流渠道

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

- 后端API

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
