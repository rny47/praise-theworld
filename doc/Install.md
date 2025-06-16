防报毒API 安装文档
===============

###  运行环境

> * PHP 7.4+ [扩展:swoole4+,redis,pdo-mysql,fileinfo,bz2,event,opcache]
> * MYSQL 8+
> * Redis 5.0.3+
> * Nginx 1.19.1+


### 部署流程

```shell

# 切换到普通用户操作
su www

# 进入需要部署的目录（非固定 /home/www）
cd /home/www

# 拉取项目代码
git clone ssh://git@source.nu0g.com:16868/root/ApkSigner.git


# 进入项目根目录
cd /home/www/ApkSigner

# 更新子仓库
git submodule update --init

# 进入项目实际代码目录
cd /home/www/ApkSigner/code

cp -r .env.example .env

# 修改 .env 里面的数据库配置，REDIS配置等
vim .env

# composer 安装扩展
composer install

# 初始化表结构
php artisan migrate

# 初始化数据
php artisan db:seed

# Nginx 站点目录
root /home/www/ApkSigner/code

# Nginx 伪静态配置
location / {
     index index.php;
     try_files $uri $uri/ /index.php?$query_string;
}

# 启动服务
php artisan task start --d
        
```




### 版本升级，以下不需要操作，仅供参考.........................................

```shell

# 开发过程中，如果需要对数据库操作, 比如开发过程中需要修改仪表的字段，今天是 20230629，那么执行以下命令
php artisan make:migration version20230629_01

```

```php
# 这样会在database/migrations/目录下 生成一个 2023_06_29_192313_version20230629_01.php 文件，你需要在这里添加或者修改数据库
class Version2023062901 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //添加一个绑定时间的字段
        Schema::table('miao_users', function (Blueprint $table) {
            $table->timestamp("mu_bind_phone_date")->nullable()->comment("绑定手机号时间")->after("created_at");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        #回退一个绑定时间的字段
        Schema::table('miao_users', function (Blueprint $table) {
            $table->dropColumn('mu_bind_phone_date');
        });
    }
}

```


```shell

# 其他环境，比如正式环境拉取代码后，可以执行以下命令查看哪些SQL文件是否未执行
php artisan migrate:status

+------+----------------------------------------------+-------+
| Ran? | Migration                                    | Batch |
+------+----------------------------------------------+-------+
| Yes  | 2023_05_08_175727_create_custom_webs_table   | 1     |
| No   | 2023_06_29_192313_version20230629_01         |       |
+------+----------------------------------------------+-------+

# 这里可以看到是我们上面生成的文件 Ran 是 No,也就是未执行，那么我么可以直接执行以下语句添加字段

php artisan migrate

# 再次查看执行状态

php artisan migrate:status

+------+----------------------------------------------+-------+
| Ran? | Migration                                    | Batch |
+------+----------------------------------------------+-------+
| Yes  | 2023_05_08_175727_create_custom_webs_table   | 1     |
| Yes   | 2023_06_29_192313_version20230629_01        | 1     |
+------+----------------------------------------------+-------+

# 可以看到SQL已经执行，数据表进行了裁剪修改

# 如果需要回退一个执行，则执行下面语句， step 表示回退次数，记住不是回退文件个数。
php artisan migrate:rollback --step=1


```

####  IDE 模型插件

```shell

composer require --dev barryvdh/laravel-ide-helper

php artisan ide-helper:generate
php artisan ide-helper:models -W
php artisan ide-helper:meta

```

### Swoole微服务

```shell

# 启动服务 前台执行
php artisan task start  

# 启动服务 后台执行
php artisan task start --d

# 关闭服务
php artisan task stop

# 重启服务
php artisan task restart

# 查看服务详情
php artisan task status

```

#### 微服务 异步任务处理

```php

#HTTP 前端服务运行过程中，或者微服务运行过程中，需要将一个不影响当前结果的任务，丢出交给异步服务做可以参看以下代码

$arrTask = [
    'callback' => [\App\Task\Module\Robot::class, 'sendPublicVideoMsgToTelegram'],
    'data' => $arrVideo,
];

$TaskCore = new TaskCore();
$TaskCore->set($arrTask);

# 只需要配置 $arrTask数组即可，callback为回调对象，第一个参数是类名，第二个为方法，data 为传递的数据。
# 异步服务收到任务后，会实例化类得到一个对象，然后调用传送的方法，且携带传送的数据


```