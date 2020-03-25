# 部署

## 拉取代码

```shell
git clone https://email:password@gitee.com/hiywy/ywysys_back_v2.git
```

## 初始化

### 安装composer扩展包

> composer安装教程：https://www.phpcomposer.com/

```shell
composer install
```

### 配置DEV文件

```php

cp .env.example .env

# 配置数据库
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ywysys
DB_USERNAME=root
DB_PASSWORD=password

# 配置API域名(二选一)
API_DOMAIN=
# 或使用ip+端口使用(二选一)
API_PREFIX=

# 配置云途物流
YT_HOST=
YT_APP_KEY=
YT_APP_SECRET=

```

### 生成JWT SECRET

> 管理员登录令牌

```shell
php artisan jwt:secret
```

### 执行迁移文件，生成数据库

```shell
php artisan migrate
```

### 拉取订单

> 可根据需要做成定时任务，一分钟执行一次

```shell
php artisan receipt:pull page --shop=16407439
```

### 初始化国家数据

```shell
php artisan country:pull
```

## 配置WebServer（以Nginx为例）

```nginx

# 前端
server
{
  server_name pre.admin.createos.xyz;
  listen 80;
  root  /www/pre/ywysys_web;

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

  root /www/pre/ywysys_back_v2/public;

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

> 配置完需要reload nginx

### 图片上传权限

chmod -R 777 storage/app
