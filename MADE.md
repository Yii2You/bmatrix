[![](https://avatars0.githubusercontent.com/u/993323?s=100&v=4)](https://github.com/yiisoft)
[![](https://avatars0.githubusercontent.com/u/35477647?s=100&v=4)](https://github.com/Yii2You)

#  BMatriX Yii2 - проект CMS Yii2You
## Процесс разработки

<a name="list">Содержание</a>
=============================

1. [Установка фреймворка](#setup)
2. [Подключаем `git`](#git)
3. [Конфигурация приложения](#config)


## <a name="target">Установка фреймворка</a> ##

### Установка фреймворка через `composer`


Yii2 доступен для быстрой установки из репозитория.

	https://github.com/yiisoft/yii2-app-basic.git
	https://github.com/yiisoft/yii2-app-advanced.git

Для установки запускаем консоль и переходим в корень проекта 

	cd /path/domain/name

Для начала установим плагин для работы с менеджером **Bower**, затем устанавливаем пакет в текущий каталог.  Флаг `--stability=dev` не указываем, чтобы не скачивать нестабильные версии.
Шаблон будет развёрнут в корне, а все нужные зависимости скачаны в поддиректорию `vendor`.

Установим `yii2-app-basic`:

	composer global require "fxp/composer-asset-plugin:^1.2.0"
	composer create-project --prefer-dist yiisoft/yii2-app-basic ./

После установки запускаем обновления

	composer update

### Конфигурация сервера

Первым делом проверим наш сервер. Запускаем в консоли:

	php requirements.php

#### Файл `php.ini`

Для работы с `MySQL`-базами можно помимо `Intl`, `MBString` и `MCrypt` подключить только связанные с `MySQL` расширения:

	extension=php_intl.dll
	extension=php_mbstring.dll
	extension=php_mcrypt.dll
	extension=php_mysql.dll
	extension=php_mysqli.dll
	extension=php_pdo_mysql.dll

Устанавливаем временную зону:

	date.timezone = "Europe/Samara"

## <a name="git">Используем `git` через консоль</a>

	git init
	git add README.md
	git commit -m "Initial commit"

> В этом проекте коммиты делались позже...
> После создания всех необходимых файлов и настроек

	git add .
	git commit -m "Create project"

### Настройка `.gitignore`

Создаём файл `config/.gitignore` и указываем не индексировать личные настройки:

	/db.php
	/params.php

Создаём файл `web/.gitignore` и вписываем исключаемые файлы конкретно в этой папке:

	/.htaccess
	/index.php
	/index-test.php

### Настройка `urlManager`:

	'components' => [
    ...
	    'urlManager' => [
	        'enablePrettyUrl' => true,
	        'showScriptName' => false,
	        'rules' => [
	            '<_c:[\w\-]+>/<id:\d+>' => '<_c>/view',
	            '<_c:[\w\-]+>' => '<_c>/index',
	            '<_c:[\w\-]+>/<_a:[\w\-]+>/<id:\d+>' => '<_c>/<_a>',
	        ],
	    ],
    ...
	],

Теперь, собственно, создаём файл `web/.htaccess`:

	Order Allow,Deny
	Allow from all
	
	AddDefaultCharset utf-8
	
	RewriteEngine on
	
	RewriteCond %{REQUEST_FILENAME} !-f
	RewriteCond %{REQUEST_FILENAME} !-d
	RewriteRule . index.php

В корне создаём файл `.htaccess`
	
	AddDefaultCharset utf-8

	Options +FollowSymLinks
	IndexIgnore */*
	RewriteEngine On
	
	RewriteCond %{REQUEST_URI} !^/(web)
	RewriteRule ^assets/(.*)$ /web/assets/$1 [L]
	RewriteRule ^css/(.*)$ /web/css/$1 [L]
	RewriteRule ^js/(.*)$ /web/js/$1 [L]
	RewriteRule ^images/(.*)$ /web/images/$1 [L]
	RewriteRule (.*)$ /web/$1
	
	RewriteCond %{REQUEST_FILENAME} !-f
	RewriteCond %{REQUEST_FILENAME} !-d
	RewriteRule .* /web/index.php

## <a name="config">Конфигурация приложения</a>

В файлах `config/web.php` и `config/console.php` параметры включаются из одного файла:

	$params = require(__DIR__ . '/params.php');
	 
	return [
	    ...
	    'params' => $params,
	];

Удалим адрес из `config/params.php` и добавим параметр `supportEmail`:

	return [
	    'adminEmail' => '',
	    'supportEmail' => '',
	];

и перенесём его в новый файл `config/params-local.php`:

	return [
	    'adminEmail' => 'admin@example.com',
	    'supportEmail' => 'info@example.com',
	];

Второй параметр нам пригодится для указания в поле `From` для отправляемых с сайта писем. В коде контактной формы по умолчанию туда подставляется адрес отправителя, пишущего сообщение:

	class ContactForm extends Model
	{
	    ...
	 
	    public function contact($email)
	    {
	        if ($this->validate()) {
	            Yii::$app->mailer->compose()
	                ->setTo($email)
	                ->setFrom([$this->email => $this->name])
	                ->setSubject($this->subject)
	                ->setTextBody($this->body)
	                ->send();
	 
	            return true;
	        } else {
	            return false;
	        }
	    }
	}


Но привередливые почтовые системы вроде ***MailRu*** никак не принимают письма с подменённым именем отправителя. Соответственно лучше в поле From подставлять фиксированный адрес, а ***Email*** отправителя подставлять в поле ***ReplyTo***:

	class ContactForm extends Model
	{
	    ...
	 
	    public function contact($email)
	    {
	        if ($this->validate()) {
	            Yii::$app->mailer->compose()
	                ->setTo($email)
	                ->setFrom([Yii::$app->params['supportEmail'] => Yii::$app->name])
	                ->setReplyTo([$this->email => $this->name])
	                ->setSubject($this->subject)
	                ->setTextBody($this->body)
	                ->send();
	 
	            return true;
	        } else {
	            return false;
	        }
	    }
	}

Теперь если администратор нажмёт «Ответить» в письме, то в поле Кому подставится именно оригинальный адрес отправителя.

Откроем `cofig/.gitignore` и заменим его содержимое на:

	/db.php
	*-local.php

Файл `params.php` теперь проиндексируется и попадёт в общий репозиторий, а `params-local.php` останется только на вашем компьютере. Каждый разработчик может добавить свой файл `params-local.php` с личными параметрами и не переживать за свои пароли.

Теперь в `config/web.php` и `config/console.php` добавим механизм склейки файлов:

	use yii\helpers\ArrayHelper;
	 
	...
	 
	$params = ArrayHelper::merge(
	    require(__DIR__ . '/params.php'),
	    require(__DIR__ . '/params-local.php')
	);
	 
	...

Аналогично сделаем возможность переопределять настройки компонентов приложения.

На примере более «продвинутого» `advanced`-шаблона создадим файл `config/common.php` с общими настройками:

	use yii\helpers\ArrayHelper;
 
	$params = ArrayHelper::merge(
	    require(__DIR__ . '/params.php'),
	    require(__DIR__ . '/params-local.php')
	);
 
	return [
	    'basePath' => dirname(__DIR__),
	    'bootstrap' => ['log'],
	    'components' => [
	        'db' => [
	            'class' => 'yii\db\Connection',
	            'charset' => 'utf8',
	        ],
	        'urlManager' => [
	            'class' => 'yii\web\UrlManager',
	                'enablePrettyUrl' => true,
	                'showScriptName' => false,
	                'rules' => [
	                    '<_c:[\w\-]+>/<id:\d+>' => '<_c>/view',
	                    '<_c:[\w\-]+>' => '<_c>/index',
	                    '<_c:[\w\-]+>/<_a:[\w\-]+>/<id:\d+>' => '<_c>/<_a>',
	                ],
	        ],
	        'mailer' => [
	            'class' => 'yii\swiftmailer\Mailer',
	        ],
	        'cache' => [
	            'class' => 'yii\caching\DummyCache',
	        ],
	        'log' => [
	            'class' => 'yii\log\Dispatcher',
	        ],
	    ],
	    'params' => $params,
	];

Рядом положим `config/common-local.php` с недостающими личными параметрами для соответствующих компонентов:

	return [
	    'components' => [
	        'db' => [
	            'dsn' => 'mysql:host=localhost;dbname=seokeys',
	            'username' => 'root',
	            'password' => '',
	            'tablePrefix' => 'keys_',
	        ],
	        'mailer' => [
	            'useFileTransport' => true,
	        ],
	        'cache' => [
	            'class' => 'yii\caching\FileCache',
	        ],
	    ],
	];

При слиянии конфигураций наши локальные настройки переопределят общие. Второй файл не попадёт в репозиторий системы контроля версий, так как он тоже назван в виде `*-local.php`.

Заменим содержимое `config/.gitignore` на:

	*-local.php

То есть мы убрали игнорирование файла `config/db.php`. Он нам больше не нужен. Удалим его:

	rm config/db.php

Теперь из `config/console.php` и `config/web.php` мы можем убрать все общие настройки. Оставим там только индивидуальные.

Специфические параметры web-приложения `config/web.php`:

	$config = [
	    'id' => 'app',
	    'components' => [
	        'user' => [
	            'identityClass' => 'app\models\User',
	            'enableAutoLogin' => true,
	        ],
	        'errorHandler' => [
	            'errorAction' => 'site/error',
	        ],
	        'request' => [
	            'cookieValidationKey' => '',
	        ],
	        'log' => [
	            'traceLevel' => YII_DEBUG ? 3 : 0,
	        ],
	    ],
	];
	 
	if (YII_ENV_DEV) {
	    // configuration adjustments for 'dev' environment
	    $config['bootstrap'][] = 'debug';
	    $config['modules']['debug'] = 'yii\debug\Module';
	 
	    $config['bootstrap'][] = 'gii';
	    $config['modules']['gii'] = 'yii\gii\Module';
	}
	 
	return $config;

Параметры консольного режима `config/console.php`:

	Yii::setAlias('@tests', dirname(__DIR__) . '/tests');
	 
	return [
	    'id' => 'app-console',
	    'bootstrap' => ['gii'],
	    'controllerNamespace' => 'app\commands',
	    'modules' => [
	        'gii' => 'yii\gii\Module',
	    ],
	];

И дополнительно добавим возможность локального переопределения каждого варианта. Например, доработав один из рецептов из Yii Application Development Cookbook для Yii1 сделаем логирование в раздельные файлы:

Личные настройки `config/web-local.php`:

	return [
	    'components' => [
	        'request' => [
	            'cookieValidationKey' => 'jshd3qjaxp',
	        ],
	        'assetManager' => [
	            'linkAssets' => true,
	        ],
	        'log' => [
	            'targets' => [
	                [
	                    'class' => 'yii\log\FileTarget',
	                    'levels' => ['error'],
	                    'logFile' => '@app/runtime/logs/web-error.log'
	                ],
	                [
	                    'class' => 'yii\log\FileTarget',
	                    'levels' => ['warning'],
	                    'logFile' => '@app/runtime/logs/web-warning.log'
	                ],
	            ],
	        ],
	    ],
	];

Здесь мы от себя установили параметр `linkAssets` компонента `assetManager` в `true`, чтобы фреймворк не копировал папки в `web/assets`, а делал символические ссылки. Это и экономит место, и позволяет не удалять и перегенерировать папки при каждом обновлении вендоров.

Личные настройки `config/console-local.php`:

	return [
	    'components' => [
	        'log' => [
	            'targets' => [
	                [
	                    'class' => 'yii\log\FileTarget',
	                    'levels' => ['error'],
	                    'logFile' => '@app/runtime/logs/console-error.log'
	                ],
	                [
	                    'class' => 'yii\log\FileTarget',
	                    'levels' => ['warning'],
	                    'logFile' => '@app/runtime/logs/console-warning.log'
	                ],
	            ],
	        ],
	    ],
	];

Получилась гибкая система конфигурации.

В результате у нас получился целый набор файлов:

	config/
	    common.php
	    common-local.php
	    console.php
	    console-local.php
	    web.php
	    web-local.php
	    params.php
	    params-local.php

То есть общие параметры подключаются к `web` и `console` из `common` и `params`, а все личные настройки можно спокойно переопределять в `*-local` файлах.

Откроем файл `web/index.php`, найдём строку загрузки конфигурации:

	$config = require(__DIR__ . '/../config/web.php');

и заменим её на конструкцию:

	$config = yii\helpers\ArrayHelper::merge(
	    require(__DIR__ . '/../config/common.php'),
	    require(__DIR__ . '/../config/common-local.php'),
	    require(__DIR__ . '/../config/web.php'),
	    require(__DIR__ . '/../config/web-local.php')
	);

Аналогично для консольных команд изменим файл `yii` в корне. Вместо:

	$config = require(__DIR__ . '/config/console.php');

вставим:

	$config = yii\helpers\ArrayHelper::merge(
	    require(__DIR__ . '/config/common.php'),
	    require(__DIR__ . '/config/common-local.php'),
	    require(__DIR__ . '/config/console.php'),
	    require(__DIR__ . '/config/console-local.php')
	);

Теперь проиндексируем все изменения и сделаем ещё одну отметку в системе контроля версий:

	git add .
	git commit -m 'Extended config system'