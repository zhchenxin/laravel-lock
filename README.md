## 安装

需要使用 composer 工具安装，安装命令如下

```shell
composer require zhchenxin/lock
```

### laravel

本扩展提供了包自动发现功能，如果 laravel 版本大于5.5，则不需要任何配置，可以直接使用。

如果小于5.5，则需要执行下面的步骤进行初始化。

（1）打开配置文件 `config/app.php` ，然后增加以下行代码：

```php
'providers' => array(

    [...]

    'Zhchenxin\Lock\LockServiceProvider'
),
```

（2）修改配置

在 `config/` 目录下，复制扩展的配置文件 `lock.php`。修改里面的配置项

### 其他项目

在代码开始部分，加载composer的自动加载文件：

```
require 'vendor/autoload.php';
```

## 使用方法

（1）初始化

```php

// laravel 以外的项目
$tool = new LockTool('redis', [
    'host' => '127.0.0.1',
    'password' => null,
    'port' => 6379,
    'database' => 0,
]);

// laravel 中
$tool = app('Zhchenxin\Lock\LockTool')
```

（2）使用方法

```php
// 方案1

if ($tool->lock('key', '123', 60)) {

    // 业务逻辑...

    $tool->unlock('key', '123');
}

// 方案2

$tool->serial('key', 60, function() {

    // 业务逻辑...

});

```
