# 部署

## 拉取代码

```shell
git clone https://email:password@gitee.com/hiywy/ywysys_back_v2.git
```

## 安装composer扩展包

```shell
composer install
```

## 配置DEV文件
```
cp .env.example .env

# 配置数据库
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ywysys
DB_USERNAME=root
DB_PASSWORD=password

# 配置API域名
API_DOMAIN=http://pre.api.createos.xyz

```

## 生成JWT SECRET
> 管理员登录令牌

```shell
php artisan jwt:secret
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

## 执行迁移文件，生成数据库

```shell
php artisan migrate
```

## 执行订单拉取任务

```shell
php artisan receipt:pull page --shop=16407439
```
