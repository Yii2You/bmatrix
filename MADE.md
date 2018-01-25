[![](https://avatars0.githubusercontent.com/u/35477647?s=100&v=4)](https://github.com/Yii2You)
[![](https://avatars0.githubusercontent.com/u/993323?s=100&v=4)](https://github.com/yiisoft)

#  BMatriX Yii2 - проект CMS Yii2You
## Процесс разработки
В данном файле описан весь процесс разработки "с самого нуля"
#### Ссылки

[Дмитрий Елисеев » Блог » Программирование » Записи с меткой «SeoKeys»](http://www.elisdn.ru/blog/tag/SeoKeys "Дмитрий Елисеев » Блог ")


##<a name="list">Содержание</a>


1. [Установка фреймворка](#setup)
2. [Подключаем `git`](#git)
3. [Конфигурация приложения](#config)
4. [Переход к модульной структуре](#modules)
5. [Перенос пользователей в БД](#db_user)
6. [Автозаполнение формы обратной связи](#feedback)
7. [Консольное управление](#console)
8. [Доработка шаблона приложения](#template)
9. [Язык интерфейса приложения](#lang)

### PhpStorm

Активация `IntelliJ IDEA 2017.3 and 2018` с помощью интернет серверов:

	http://idea.singee77.com
	http://idea.ibdyr.com
	http://ice-cloud.ru:1017
	http://noddes.ru:1017
	http://roothat.ru:1017

## <a href="#list" name="target">Установка фреймворка</a> ##

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

## <a href="#list" name="git">Используем `git` через консоль</a>

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

## <a href="#list" name="config">Конфигурация приложения</a>

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
	git commit -m "Extended config system"

> Этот коммит пропустили...

## <a href="#list" name="modules">Переход к модульной структуре</a>

Через `Gii Module Generator` создаем два модуля:

1. Main 
2. User

Регистрируем модули в `common.php`

	'modules' => [
        'main' => [
            'class' => 'app\modules\main\Module',
        ],
        'user' => [
            'class' => 'app\modules\user\Module',
        ],
    ],

Контроллер обратной связи сделаем отдельным в модуле `main`:

	namespace app\modules\main\controllers;
	 
	use app\modules\main\models\ContactForm;
	use yii\web\Controller;
	use Yii;
	 
	class ContactController extends Controller
	{
	    public function actions()
	    {
	        return [
	            'captcha' => [
	                'class' => 'yii\captcha\CaptchaAction',
	                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
	            ],
	        ];
	    }
	 
	    public function actionIndex()
	    {
	        $model = new ContactForm();
	        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
	            Yii::$app->session->setFlash('contactFormSubmitted');
	 
	            return $this->refresh();
	        } else {
	            return $this->render('index', [
	                'model' => $model,
	            ]);
	        }
	    }
	}


В модели `ContactForm` находим строку подключения `CaptchaValidator`:

	['verifyCode', 'captcha'],

и указываем новый маршрут:

	['verifyCode', 'captcha', 'captchaAction' => '/main/contact/captcha'],

Аналогично в файле представления `modules/main/views/contact/index.php` находим конструкцию вывода виджета:

	<?= $form->field($model, 'verifyCode')->widget(Captcha::className(), [
	    'template' => '<div class="row"><div class="col-lg-3">{image}</div><div class="col-lg-6">{input}</div></div>',
	]) ?>

и добавляем такой же параметр:

	<?= $form->field($model, 'verifyCode')->widget(Captcha::className(), [
	    'captchaAction' => '/main/contact/captcha',
	    'template' => '<div class="row"><div class="col-lg-3">{image}</div><div class="col-lg-6">{input}</div></div>',
	]) ?>

Контроллер по умолчанию в модуле `main` будет выводить страницы и ошибки:

	namespace app\modules\main\controllers;
	 
	use yii\web\Controller;
	 
	class DefaultController extends Controller
	{
	    public function actions()
	    {
	        return [
	            'error' => [
	                'class' => 'yii\web\ErrorAction',
	            ],
	        ];
	    }
	 
	    public function actionIndex()
	    {
	        return $this->render('index');
	    }
	}

К нему переносим все его представления `index.php` и `error.php` из папки `views/site` в папку `modules/main/views/default`.

В его контроллер модуля `user` мы перенесём авторизацию и представление `login.php`:

	namespace app\modules\user\controllers;
	 
	use app\modules\user\models\LoginForm;
	use yii\filters\AccessControl;
	use yii\filters\VerbFilter;
	use yii\web\Controller;
	use Yii;
	 
	class DefaultController extends Controller
	{
	    public function behaviors()
	    {
	        return [
	            'access' => [
	                'class' => AccessControl::className(),
	                'only' => ['logout'],
	                'rules' => [
	                    [
	                        'actions' => ['logout'],
	                        'allow' => true,
	                        'roles' => ['@'],
	                    ],
	                ],
	            ],
	            'verbs' => [
	                'class' => VerbFilter::className(),
	                'actions' => [
	                    'logout' => ['post'],
	                ],
	            ],
	        ];
	    }
	 
	    public function actionLogin()
	    {
	        if (!Yii::$app->user->isGuest) {
	            return $this->goHome();
	        }
	 
	        $model = new LoginForm();
	        if ($model->load(Yii::$app->request->post()) && $model->login()) {
	            return $this->goBack();
	        } else {
	            return $this->render('login', [
	                'model' => $model,
	            ]);
	        }
	    }
	 
	    public function actionLogout()
	    {
	        Yii::$app->user->logout();
	 
	        return $this->goHome();
	    }
	}

Пустые папки `controllers`, `models` и `views/site` в корне удаляем.

В итоге получилась новая файловая структура, состоящая из двух модулей:

	assets/
	commands/
	config/
	mail/
	modules/
	    main/
	        controllers/
	            DefaultController.php
	            ContactController.php
	        models/
	            ContactForm.php
	        views/
	            default/
	                error.php
	                index.php
	            contact/
	                index.php
	        Module.php
	    user/
	        controllers/
	            DefaultController.php
	        models/
	            LoginForm.php
	            User.php
	        views/
	            default/
	                login.php
	        Module.php
	tests/
	vendor/
	views/
	    layouts/
	        main.php
	web/

Доработаем файлы конфигурации. Укажем маршрут по умолчанию вместо `site/index` и действие для вывода ошибок вместо `site/error` в `config/web.php`. Помимо этого нужно добавить новый путь для свойства `loginUrl`:

	$config = [
	    'id' => 'app',
	    'defaultRoute' => 'main/default/index',
	    'components' => [
	        'user' => [
	            'identityClass' => 'app\modules\user\models\User',
	            'enableAutoLogin' => true,
	            'loginUrl' => ['user/default/login'],
	        ],
	        'errorHandler' => [
	            'errorAction' => 'main/default/error',
	        ],
	        'request' => [
	            'cookieValidationKey' => '',
	        ],
	        'log' => [
	            'traceLevel' => YII_DEBUG ? 3 : 0,
	        ],
	    ],
	];

Также дополним правила маршрутизации с учётом новых модулей в `config/common.php`:

	return [
	    'basePath' => dirname(__DIR__),
	    'bootstrap' => ['log'],
	    'modules' => [
	        'main' => [
	            'class' => 'app\modules\main\Module',
	        ],
	        'user' => [
	            'class' => 'app\modules\user\Module',
	        ],
	    ],
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
	                '' => 'main/default/index',
	                'contact' => 'main/contact/index',
	                '<_a:error>' => 'main/default/<_a>',
	                '<_a:(login|logout)>' => 'user/default/<_a>',
	 
	                '<_m:[\w\-]+>' => '<_m>/default/index',
	                '<_m:[\w\-]+>/<_c:[\w\-]+>' => '<_m>/<_c>/index',
	                '<_m:[\w\-]+>/<_c:[\w\-]+>/<id:\d+>' => '<_m>/<_c>/view',
	                '<_m:[\w\-]+>/<_c:[\w\-]+>/<_a:[\w\-]+>/<id:\d+>' => '<_m>/<_c>/<_a>',
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

Теперь исправим маршруты ссылок в главном меню `views/layouts/main`:

	echo Nav::widget([
	    'options' => ['class' => 'navbar-nav navbar-right'],
	    'items' => [
	        ['label' => 'Home', 'url' => ['/main/default/index']],
	        ['label' => 'Contact', 'url' => ['/main/contact/index']],
	        Yii::$app->user->isGuest ?
	            ['label' => 'Login', 'url' => ['/user/default/login']] :
	            ['label' => 'Logout (' . Yii::$app->user->identity->username . ')',
	                'url' => ['/user/default/logout'],
	                'linkOptions' => ['data-method' => 'post']],
	    ],
	]);

## <a href="#list" name="db_user">Перенос пользователей в БД</a>

Запустим команду создания первой миграции и ответим `yes` или `y`:

	php yii migrate/create create_user_table

Если у нас есть папка `migrations`, то увидим уведомление, что всё у нас получилось:

	
	New migration created successfully.

Структуру таблицы мы можем позаимствовать из миграции расширенного шаблона с некоторыми изменениями. А именно, удалим лишние `NOT NULL` и добавим индексы для оптимизации поиска:

	use yii\db\Schema;
	use yii\db\Migration;
 
	class m140916_150445_create_user_table extends Migration
	{
	   public function up()
	    {
	        $tableOptions = null;
	        if ($this->db->driverName === 'mysql') {
	            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
	        }
	 
	        $this->createTable('{{%user}}', [
	            'id' => $this->primaryKey(),
	            'created_at' => $this->integer()->notNull(),
	            'updated_at' => $this->integer()->notNull(),
	            'username' => $this->string()->notNull(),
	            'auth_key' => $this->string(32),
	            'email_confirm_token' => $this->string(),
	            'password_hash' => $this->string()->notNull(),
	            'password_reset_token' => $this->string(),
	            'email' => $this->string()->notNull(),
	            'status' => $this->smallInteger()->notNull()->defaultValue(0),
	        ], $tableOptions);
	 
	        $this->createIndex('idx-user-username', '{{%user}}', 'username');
	        $this->createIndex('idx-user-email', '{{%user}}', 'email');
	        $this->createIndex('idx-user-status', '{{%user}}', 'status');
	    }
	 
	    public function down()
	    {
	        $this->dropTable('{{%user}}');
	    }
	}

Дополнительные параметры таблицы вынесены в переменную `$tableOptions` и индексам даны уникальные имена в формате `idx_{таблица}_{поле}`. Это делает миграции кроссплатформенными, то есть полностью рабочими на базах `MySQL` и `PostgreSQL`.

Теперь в командной строке (или в PhpMyAdmin) создадим новую базу данных:

	mysql -uroot --execute='create database yii2_bmatrix character set utf8;'

Проверим настройки соединения в `config/common-local.php`:

	'db' => [
	    'dsn' => 'mysql:host=localhost;dbname=yii2_bmatrix',
	    'username' => 'root',
	    'password' => '',
	    'tablePrefix' => 'bmx_',
	],

и применим эту миграцию:

	php yii migrate/up

Получаем

	Yii Migration Tool (based on Yii v2.0.13.1)
	
	Creating migration history table "bmx_migration"...Done.
	Total 1 new migration to be applied:
	        m180117_180915_create_user_table
	
	Apply the above migration? (yes|no) [no]:y
	*** applying m180117_180915_create_user_table
	    > create table {{%user}} ... done (time: 0.200s)
	    > create index idx-user-username on {{%user}} (username) ... done (time: 0.449s)
	    > create index idx-user-email on {{%user}} (email) ... done (time: 0.096s)
	    > create index idx-user-status on {{%user}} (status) ... done (time: 0.124s)
	*** applied m180117_180915_create_user_table (time: 0.978s)
	
	
	1 migration was applied.
	
	Migrated up successfully.

Теперь по этой таблице нужно сгенерировать новую модель `User`. Прверяем, что в `web/index.php` константа `YII_ENV` у нас выставлена в `dev` и переходим по адресу `http://localhost/gii`. Там переходим по ссылке `Model Generator` и вбиваем имена таблицы и модели:

>`Table Name`: `bmx_user`
>
>`Model Class Name`: `User`
>
>`Namespace`: `app\modules\user\models`

Теперь жмём **Preview**, ставим галочку что хотим перезаписать старую модель и жмём **Generate**

Дополним модель. Первым делом, введём в неё константы для указания статуса, статический метод `getStatusesArray` для получения их списка и метод `getStatusName` для получения имени статуса пользователя. Эти методы пригодятся, например, при выводе пользователей в панели управления:

	namespace app\modules\user\models;
	 
	use Yii;
	use yii\base\NotSupportedException;
	use yii\behaviors\TimestampBehavior;
	use yii\db\ActiveRecord;
	use yii\helpers\ArrayHelper;
	 
	/**
	 * This is the model class for table "{{%user}}".
	 *
	 * @property integer $id
	 * @property integer $created_at
	 * @property integer $updated_at
	 * @property string $username
	 * @property string $auth_key
	 * @property string $email_confirm_token
	 * @property string $password_hash
	 * @property string $password_reset_token
	 * @property string $email
	 * @property integer $status
	 */
	class User extends ActiveRecord
	{
	    const STATUS_BLOCKED = 0;
	    const STATUS_ACTIVE = 1;
	    const STATUS_WAIT = 2;
	 
	    ...
	 
	    public function getStatusName()
	    {
	        return ArrayHelper::getValue(self::getStatusesArray(), $this->status);
	    }
	 
	    public static function getStatusesArray()
	    {
	        return [
	            self::STATUS_BLOCKED => 'Заблокирован',
	            self::STATUS_ACTIVE => 'Активен',
	            self::STATUS_WAIT => 'Ожидает подтверждения',
	        ];
	    }
	}

Поля `created_at` и `updated_at`, в которые нужно вписывать дату при создании и каждом обновлении записи. Для этого нам пригодится уже имеющееся в `Yii2` поведение:

	use yii\behaviors\TimestampBehavior;
	 
	class User extends ActiveRecord
	{
	    ...
	 
	    public function behaviors()
	    {
	        return [
	            TimestampBehavior::className(),
	        ];
	    }
	}

В старой модели наш класс заодно осуществлял хранение авторизованного пользователя в `Yii::$app->user->identity` и для этого реализовывал интерфейс `IdentityInterface`. Допишем методы, которые требует добавить этот интерфейс. Коды методов позаимствуем из того же расширенного шаблона приложения.

Добавляем методы:

	use yii\web\IdentityInterface;
	 
	class User extends ActiveRecord implements IdentityInterface
	{
	    ...
	 
	    public static function findIdentity($id)
	    {
	        return static::findOne(['id' => $id, 'status' => self::STATUS_ACTIVE]);
	    }
	 
	    public static function findIdentityByAccessToken($token, $type = null)
	    {
	        throw new NotSupportedException('findIdentityByAccessToken is not implemented.');
	    }
	 
	    public function getId()
	    {
	        return $this->getPrimaryKey();
	    }
	 
	    public function getAuthKey()
	    {
	        return $this->auth_key;
	    }
	 
	    public function validateAuthKey($authKey)
	    {
	        return $this->getAuthKey() === $authKey;
	    }
	}

В старой модели также были `findByUsername` и `validatePassword` для работы класса `LoginForm`. Добавим и их. В методе `findByUsername` не будем искать по статусу `User::STATUS_ACTIV`E. Так как у нас много статусов, то проверки удобнее будет совершать в контроллере или в форме `LoginForm`. Для хэширования паролей будем использовать новый компонент `Security`:

	class User extends ActiveRecord implements IdentityInterface
	{
	    ...
	 
	    /**
	     * Finds user by username
	     *
	     * @param string $username
	     * @return static|null
	     */
	    public static function findByUsername($username)
	    {
	        return static::findOne(['username' => $username]);
	    }
	 
	    /**
	     * Validates password
	     *
	     * @param string $password password to validate
	     * @return boolean if password provided is valid for current user
	     */
	    public function validatePassword($password)
	    {
	        return Yii::$app->security->validatePassword($password, $this->password_hash);
	    }
	}

Перед записью в базу для каждого пользователя нужно генерировать хэш пароля и дополнительный ключ автоматической аутентификации. Добавим методы их генерации и сделаем второй метод автозапускаемым при создании записи:

	class User extends ActiveRecord implements IdentityInterface
	{
	    /**
	     * @param string $password
	     */
	    public function setPassword($password)
	    {
	        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
	    }
	 
	    /**
	     * Generates "remember me" authentication key
	     */
	    public function generateAuthKey()
	    {
	        $this->auth_key = Yii::$app->security->generateRandomString();
	    }
	 
	    public function beforeSave($insert)
	    {
	        if (parent::beforeSave($insert)) {
	            if ($insert) {
	                $this->generateAuthKey();
	            }
	            return true;
	        }
	        return false;
	    }
	}	

Теперь добавим возможность смены пароля. Для этого у нас предусмотрено поле password_reset_token. При запросе восстановления мы в это поле будем записывать уникальную случайную строку с временной меткой и посылать по электронной почте ссылку с этим хешэм на контроллер с действием активаци. А в контроллере уже найдём этого пользователя по данному хешу и поменяем ему пароль.

Добавим методы для генерации хеша и поиска по нему:

	class User extends ActiveRecord implements IdentityInterface
	{
	    ...
	 
	    /**
	     * Finds user by password reset token
	     *
	     * @param string $token password reset token
	     * @return static|null
	     */
	    public static function findByPasswordResetToken($token)
	    {
	        if (!static::isPasswordResetTokenValid($token)) {
	            return null;
	        }
	        return static::findOne([
	            'password_reset_token' => $token,
	            'status' => self::STATUS_ACTIVE,
	        ]);
	    }
	 
	    /**
	     * Finds out if password reset token is valid
	     *
	     * @param string $token password reset token
	     * @return boolean
	     */
	    public static function isPasswordResetTokenValid($token)
	    {
	        if (empty($token)) {
	            return false;
	        }
	        $expire = Yii::$app->params['user.passwordResetTokenExpire'];
	        $parts = explode('_', $token);
	        $timestamp = (int) end($parts);
	        return $timestamp + $expire >= time();
	    }
	 
	    /**
	     * Generates new password reset token
	     */
	    public function generatePasswordResetToken()
	    {
	        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
	    }
	 
	    /**
	     * Removes password reset token
	     */
	    public function removePasswordResetToken()
	    {
	        $this->password_reset_token = null;
	    }
	}

Для регистрирующихся пользователей не помешает сделать подтверждение адреса электронной почты. Для этой цели добавим несколько методов для управления `email_confirm_token`. При регистрации мы будем присваивать пользователю статус `STATUS_WAIT`, генерировать ключ и отправлять ссылку с ключом на почту. А в контроллере (при переходе по этой ссылке) найдём пользователя по ключу и активируем:

	class User extends ActiveRecord implements IdentityInterface
	{
	    ...
	 
	    /**
	     * @param string $email_confirm_token
	     * @return static|null
	     */
	    public static function findByEmailConfirmToken($email_confirm_token)
	    {
	        return static::findOne(['email_confirm_token' => $email_confirm_token, 'status' => self::STATUS_WAIT]);
	    }
	 
	    /**
	     * Generates email confirmation token
	     */
	    public function generateEmailConfirmToken()
	    {
	        $this->email_confirm_token = Yii::$app->security->generateRandomString();
	    }
	 
	    /**
	     * Removes email confirmation token
	     */
	    public function removeEmailConfirmToken()
	    {
	        $this->email_confirm_token = null;
	    }
	}

В файл `params.php` добавим параметр, определяющий время «жизни» токена сброса пароля и дополнительное поле `supportEmail`, значение которого будет использоваться в поле `From` для исходящих писем:

	return [
	    'adminEmail' => '',
	    'supportEmail' => '',
	    'user.passwordResetTokenExpire' => 3600,
	];

В личном же файле `config/params-local.php` впишем свои значения:
	
	return [
	    'adminEmail' => 'admin@site.com',
	    'supportEmail' => 'info@site.com',
	];

Модифицируем класс `LoginForm`. А именно в валидатор `validatePassword` добавим проверку статуса пользователя. В целях безопасности проверку будем осуществлять только при правильном пароле. Это не позволит взломщику узнать даже имена пользователей. Всё, что он увидит – это фразу «Неверное имя пользователя или пароль»:

	<?php
	 
	namespace app\modules\user\models;
	 
	use Yii;
	use yii\base\Model;
	 
	/**
	 * LoginForm is the model behind the login form.
	 */
	class LoginForm extends Model
	{
	    public $username;
	    public $password;
	    public $rememberMe = true;
	 
	    private $_user = false;
	 
	    /**
	     * @return array the validation rules.
	     */
	    public function rules()
	    {
	        return [
	            [['username', 'password'], 'required'],
	            ['rememberMe', 'boolean'],
	            ['password', 'validatePassword'],
	        ];
	    }
	 
	    /**
	     * Validates the username and password.
	     * This method serves as the inline validation for password.
	     */
	    public function validatePassword()
	    {
	        if (!$this->hasErrors()) {
	            $user = $this->getUser();
	 
	            if (!$user || !$user->validatePassword($this->password)) {
	                $this->addError('password', 'Неверное имя пользователя или пароль.');
	            } elseif ($user && $user->status == User::STATUS_BLOCKED) {
	                $this->addError('username', 'Ваш аккаунт заблокирован.');
	            } elseif ($user && $user->status == User::STATUS_WAIT) {
	                $this->addError('username', 'Ваш аккаунт не подтвежден.');
	            }
	        }
	    }
	 
	    /**
	     * Logs in a user using the provided username and password.
	     * @return boolean whether the user is logged in successfully
	     */
	    public function login()
	    {
	        if ($this->validate()) {
	            return Yii::$app->user->login($this->getUser(), $this->rememberMe ? 3600*24*30 : 0);
	        } else {
	            return false;
	        }
	    }
	 
	    /**
	     * Finds user by [[username]]
	     *
	     * @return User|null
	     */
	    public function getUser()
	    {
	        if ($this->_user === false) {
	            $this->_user = User::findByUsername($this->username);
	        }
	 
	        return $this->_user;
	    }
	}

Теперь позаимствуем класс формы регистрации пользователей `SignupForm` из advanced-шаблона, дополнив его выводом капчи `verifyCode`, установкой статуса `User::STATUS_WAIT`, вызовом генерации токена подтверждения и отправке этого токена по почте:

	namespace app\modules\user\models;
	 
	use yii\base\Model;
	use Yii;
	 
	/**
	 * Signup form
	 */
	class SignupForm extends Model
	{
	    public $username;
	    public $email;
	    public $password;
	    public $verifyCode;
	 
	    public function rules()
	    {
	        return [
	            ['username', 'filter', 'filter' => 'trim'],
	            ['username', 'required'],
	            ['username', 'match', 'pattern' => '#^[\w_-]+$#i'],
	            ['username', 'unique', 'targetClass' => User::className(), 'message' => 'This username has already been taken.'],
	            ['username', 'string', 'min' => 2, 'max' => 255],
	 
	            ['email', 'filter', 'filter' => 'trim'],
	            ['email', 'required'],
	            ['email', 'email'],
	            ['email', 'unique', 'targetClass' => User::className(), 'message' => 'This email address has already been taken.'],
	 
	            ['password', 'required'],
	            ['password', 'string', 'min' => 6],
	 
	            ['verifyCode', 'captcha', 'captchaAction' => '/user/default/captcha'],
	        ];
	    }
	 
	    /**
	     * Signs user up.
	     *
	     * @return User|null the saved model or null if saving fails
	     */
	    public function signup()
	    {
	        if ($this->validate()) {
	            $user = new User();
	            $user->username = $this->username;
	            $user->email = $this->email;
	            $user->setPassword($this->password);
	            $user->status = User::STATUS_WAIT;
	            $user->generateAuthKey();
	            $user->generateEmailConfirmToken();
	 
	            if ($user->save()) {
	                Yii::$app->mailer->compose('@app/modules/user/mails/emailConfirm', ['user' => $user])
	                    ->setFrom([Yii::$app->params['supportEmail'] => Yii::$app->name])
	                    ->setTo($this->email)
	                    ->setSubject('Email confirmation for ' . Yii::$app->name)
	                    ->send();
	                return $user;
	            }
	        }
	 
	        return null;
	    }
	}

Немного дополним...

>В исходном приложении без модульной структуры все представления писем хранятся в папке `mail` в корне. При переходе к модульной структуре желательно переместить письма тоже в свои модули. Так что при написании письмо первым параметром укажем новый путь `@app/modules/user/mails` для файла письма `emailConfirm.php`.

Создадим файл `modules\user\mail\emailConfirm.php`. В него поместим приветствие и ссылку:

	<?php
	use yii\helpers\Html;
	 
	/* @var $this yii\web\View */
	/* @var $user app\modules\user\models\User */
	 
	$confirmLink = Yii::$app->urlManager->createAbsoluteUrl(['user/default/email-confirm', 'token' => $user->email_confirm_token]);
	?>
	 
	Здравствуйте, <?= Html::encode($user->username) ?>!
	 
	Для подтверждения адреса пройдите по ссылке:
	 
	<?= Html::a(Html::encode($confirmLink), $confirmLink) ?>
	 
	Если Вы не регистрировались у на нашем сайте, то просто удалите это письмо.

Остальные формы [`PasswordResetRequestForm`](https://github.com/yiisoft/yii2-app-advanced/blob/master/frontend/models/PasswordResetRequestForm.php) и [`ResetPasswordForm`](https://github.com/yiisoft/yii2-app-advanced/blob/master/frontend/models/ResetPasswordForm.php) можно взять из папки [`frontend\models`](https://github.com/yiisoft/yii2-app-advanced/tree/master/frontend/models) [`advanced`-приложения](https://github.com/yiisoft/yii2-app-advanced). Просто заменим в них пространства имён:

	namespace frontend\models;
	 
	use common\models\User;
	use yii\base\Model;
	 
	/**
	 * Password reset request form
	 */
	class PasswordResetRequestForm extends Model
	...

на другие адреса:

	namespace app\modules\user\models;
	 
	use app\modules\user\models\User;
	use yii\base\Model;
	 
	/**
	 * Password reset request form
	 */
	class PasswordResetRequestForm extends Model
	...

###
	requestPasswordResetToken.php
	resetPassword.php
	signup.php

Изменим в `PasswordResetRequestForm` относительный путь до представления письма на полный:

`Yii::$app->mailer->compose('@app/modules/user/mails/passwordReset', ['user' => $user])`

И от себя добавим форму подтверждения `Email`-адреса `EmailConfirmForm` по примеру формы сброса пароля. В ней мы будем искать пользователя по токену, вызывая `User::findByEmailConfirmToken`, активировать его и очищать поле `email_confirm_token`:

	namespace app\modules\user\models;
	 
	use yii\base\InvalidParamException;
	use yii\base\Model;
	use Yii;
	 
	class EmailConfirmForm extends Model
	{
	    /**
	     * @var User
	     */
	    private $_user;
	 
	    /**
	     * Creates a form model given a token.
	     *
	     * @param  string $token
	     * @param  array $config
	     * @throws \yii\base\InvalidParamException if token is empty or not valid
	     */
	    public function __construct($token, $config = [])
	    {
	        if (empty($token) || !is_string($token)) {
	            throw new InvalidParamException('Отсутствует код подтверждения.');
	        }
	        $this->_user = User::findByEmailConfirmToken($token);
	        if (!$this->_user) {
	            throw new InvalidParamException('Неверный токен.');
	        }
	        parent::__construct($config);
	    }
	 
	    /**
	     * Confirm email.
	     *
	     * @return boolean if email was confirmed.
	     */
	    public function confirmEmail()
	    {
	        $user = $this->_user;
	        $user->status = User::STATUS_ACTIVE;
	        $user->removeEmailConfirmToken();
	 
	        return $user->save();
	    }
	}

Теперь модифицируем `DefaultController` модуля `user`:

	namespace app\modules\user\controllers;
	 
	use app\modules\user\models\EmailConfirmForm;
	use app\modules\user\models\LoginForm;
	use app\modules\user\models\PasswordResetRequestForm;
	use app\modules\user\models\PasswordResetForm;
	use app\modules\user\models\SignupForm;
	use yii\base\InvalidParamException;
	use yii\filters\AccessControl;
	use yii\filters\VerbFilter;
	use yii\web\BadRequestHttpException;
	use yii\web\Controller;
	use Yii;
	 
	class DefaultController extends Controller
	{
	    public function behaviors()
	    {
	        return [
	            'access' => [
	                'class' => AccessControl::className(),
	                'only' => ['logout', 'signup'],
	                'rules' => [
	                    [
	                        'actions' => ['signup'],
	                        'allow' => true,
	                        'roles' => ['?'],
	                    ],
	                    [
	                        'actions' => ['logout'],
	                        'allow' => true,
	                        'roles' => ['@'],
	                    ],
	                ],
	            ],
	            'verbs' => [
	                'class' => VerbFilter::className(),
	                'actions' => [
	                    'logout' => ['post'],
	                ],
	            ],
	        ];
	    }
	 
	    public function actions()
	    {
	        return [
	            'captcha' => [
	                'class' => 'yii\captcha\CaptchaAction',
	                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
	            ],
	        ];
	    }
	 
	    public function actionLogin()
	    {
	        if (!Yii::$app->user->isGuest) {
	            return $this->goHome();
	        }
	 
	        $model = new LoginForm();
	        if ($model->load(Yii::$app->request->post()) && $model->login()) {
	            return $this->goBack();
	        } else {
	            return $this->render('login', [
	                'model' => $model,
	            ]);
	        }
	    }
	 
	    public function actionLogout()
	    {
	        Yii::$app->user->logout();
	 
	        return $this->goHome();
	    }
	 
	    public function actionSignup()
	    {
	        $model = new SignupForm();
	        if ($model->load(Yii::$app->request->post())) {
	            if ($user = $model->signup()) {
	                Yii::$app->getSession()->setFlash('success', 'Подтвердите ваш электронный адрес.');
	                return $this->goHome();
	            }
	        }
	 
	        return $this->render('signup', [
	            'model' => $model,
	        ]);
	    }
	 
	    public function actionEmailConfirm($token)
	    {
	        try {
	            $model = new EmailConfirmForm($token);
	        } catch (InvalidParamException $e) {
	            throw new BadRequestHttpException($e->getMessage());
	        }
	 
	        if ($model->confirmEmail()) {
	            Yii::$app->getSession()->setFlash('success', 'Спасибо! Ваш Email успешно подтверждён.');
	        } else {
	            Yii::$app->getSession()->setFlash('error', 'Ошибка подтверждения Email.');
	        }
	 
	        return $this->goHome();
	    }
	 
	    public function actionPasswordResetRequest()
	    {
	        $model = new PasswordResetRequestForm();
	        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
	            if ($model->sendEmail()) {
	                Yii::$app->getSession()->setFlash('success', 'Спасибо! На ваш Email было отправлено письмо со ссылкой на восстановление пароля.');
	 
	                return $this->goHome();
	            } else {
	                Yii::$app->getSession()->setFlash('error', 'Извините. У нас возникли проблемы с отправкой.');
	            }
	        }
	 
	        return $this->render('passwordResetRequest', [
	            'model' => $model,
	        ]);
	    }
	 
	    public function actionPasswordReset($token)
	    {
	        try {
	            $model = new PasswordResetForm($token);
	        } catch (InvalidParamException $e) {
	            throw new BadRequestHttpException($e->getMessage());
	        }
	 
	        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->resetPassword()) {
	            Yii::$app->getSession()->setFlash('success', 'Спасибо! Пароль успешно изменён.');
	 
	            return $this->goHome();
	        }
	 
	        return $this->render('passwordReset', [
	            'model' => $model,
	        ]);
	    }
	}

Оповещения об успешности операции отправляются в сессию в виде одноразовых `Flash`-сообщений. Для их вывода в шаблоне имеется готовый виджет `Alert` в папке `widgets` и пространство имён `app\widgets`. Он подключен к шаблону `views\layouts\main` после хлебных крошек:

	<?php
	 
	...
	use yii\widgets\Breadcrumbs;
	use app\widgets\Alert;
	 
	?>
	...
	<div class="container">
	    <?= Breadcrumbs::widget([
	        'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
	    ]) ?>
	    <?= Alert::widget() ?>
	    <?= $content ?>
	</div>
	...

Представления `login`, `passwordReset` и `passwordResetRequest` копируем из похожих в папке [`frontend/views/site`](https://github.com/yiisoft/yii2-app-advanced/tree/master/frontend/views/site). Представление `login.php` будет выглядеть так:

	<?php

		use yii\helpers\Html;
		use yii\bootstrap\ActiveForm;
		 
		/* @var $this yii\web\View */
		/* @var $form yii\bootstrap\ActiveForm */
		/* @var $model \app\modules\user\models\LoginForm */
		 
		$this->title = 'Login';
		$this->params['breadcrumbs'][] = $this->title;

	?>

	<div class="user-default-login">
	    <div class="container">
	        <h1><?= Html::encode($this->title) ?></h1>
	        <p>Please fill out the following fields to login:</p>
	        <div class="row">
	            <div class="col-lg-5">
	                <?php $form = ActiveForm::begin(['id' => 'login-form']); ?>
	                <?= $form->field($model, 'username') ?>
	                <?= $form->field($model, 'password')->passwordInput() ?>
	                <?= $form->field($model, 'rememberMe')->checkbox() ?>
	                <div style="color:#999;margin:1em 0">
	                    If you forgot your password you can <?= Html::a('reset it', ['password-reset-request']) ?>.
	                </div>
	                <div class="form-group">
	                    <?= Html::submitButton('Login', ['class' => 'btn btn-primary', 'name' => 'login-button']) ?>
	                </div>
	                <?php ActiveForm::end(); ?>
	            </div>
	        </div>
	    </div>
	</div>

Также нужно добавить свой шаблон письма для изменения пароля `modules/user/mails/passwordReset.php`:

	<?php
	use yii\helpers\Html;
	 
	/* @var $this yii\web\View */
	/* @var $user app\modules\user\models\User */
	 
	$resetLink = Yii::$app->urlManager->createAbsoluteUrl(['user/default/password-reset', 'token' => $user->password_reset_token]);
	?>
	 
	Здравствуйте, <?= Html::encode($user->username) ?>!
	 
	Пройдите по ссылке, чтобы сменить пароль:
	 
	<?= Html::a(Html::encode($resetLink), $resetLink) ?>

Представление `modules/user/views/default/signup.php` тоже позаимствуем, но скопируем в него вывод `captcha` из представления модуля `contact` с новым значением параметра `'captchaAction' => '/user/default/captcha'`:

	use yii\captcha\Captcha;
	use yii\helpers\Html;
	use yii\bootstrap\ActiveForm;
	 
	/* @var $this yii\web\View */
	/* @var $form yii\bootstrap\ActiveForm */
	/* @var $model app\modules\user\models\SignupForm */
	 
	$this->title = 'Signup';
	$this->params['breadcrumbs'][] = $this->title;
	?>
	<div class="user-default-signup">
	    <h1><?= Html::encode($this->title) ?></h1>
	 
	    <p>Please fill out the following fields to signup:</p>
	 
	    <div class="row">
	        <div class="col-lg-5">
	            <?php $form = ActiveForm::begin(['id' => 'form-signup']); ?>
	            <?= $form->field($model, 'username') ?>
	            <?= $form->field($model, 'email') ?>
	            <?= $form->field($model, 'password')->passwordInput() ?>
	            <?= $form->field($model, 'verifyCode')->widget(Captcha::className(), [
	                'captchaAction' => '/user/default/captcha',
	                'template' => '<div class="row"><div class="col-lg-3">{image}</div><div class="col-lg-6">{input}</div></div>',
	            ]) ?>
	            <div class="form-group">
	                <?= Html::submitButton('Signup', ['class' => 'btn btn-primary', 'name' => 'signup-button']) ?>
	            </div>
	            <?php ActiveForm::end(); ?>
	        </div>
	    </div>
	</div>

##<a href="#list" name="feedback">Автозаполнение формы обратной связи</a>

Добавим автоподстановку значений в поля формы:

	namespace app\modules\main\controllers;
	 
	use app\modules\main\models\ContactForm;
	use yii\web\Controller;
	use Yii;
 
	class ContactController extends Controller
	{
	    ...
	 
	    public function actionIndex()
	    {
	        $model = new ContactForm();
	        if ($user = Yii::$app->user->identity) {
	            /** @var \app\modules\user\models\User $user */
	            $model->name = $user->username;
	            $model->email = $user->email;
	        }
	        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
	            Yii::$app->session->setFlash('contactFormSubmitted');
	            return $this->refresh();
	        } else {
	            return $this->render('index', [
	                'model' => $model,
	            ]);
	        }
	    }
	}

##<a href="#list" name="console">Консольное управление</a>

Создадим свою консольную команду для управления пользователями. Добавим в неё примитивный набор действий:

	namespace app\commands;
	 
	use app\modules\user\models\User;
	use yii\base\Model;
	use yii\console\Controller;
	use yii\console\Exception;
	use yii\helpers\Console;
	 
	class UsersController extends Controller
	{
	    public function actionIndex()
	    {
	        echo 'yii users/create' . PHP_EOL;
	        echo 'yii users/remove' . PHP_EOL;
	        echo 'yii users/activate' . PHP_EOL;
	        echo 'yii users/change-password' . PHP_EOL;
	    }
	 
	    public function actionCreate()
	    {
	        $model = new User();
	        $this->readValue($model, 'username');
	        $this->readValue($model, 'email');
	        $model->setPassword($this->prompt('Password:', [
	            'required' => true,
	            'pattern' => '#^.{6,255}$#i',
	            'error' => 'More than 6 symbols',
	        ]));
	        $model->generateAuthKey();
	        $this->log($model->save());
	    }
	 
	    public function actionRemove()
	    {
	        $username = $this->prompt('Username:', ['required' => true]);
	        $model = $this->findModel($username);
	        $this->log($model->delete());
	    }
	 
	    public function actionActivate()
	    {
	        $username = $this->prompt('Username:', ['required' => true]);
	        $model = $this->findModel($username);
	        $model->status = User::STATUS_ACTIVE;
	        $model->removeEmailConfirmToken();
	        $this->log($model->save());
	    }
	 
	    public function actionChangePassword()
	    {
	        $username = $this->prompt('Username:', ['required' => true]);
	        $model = $this->findModel($username);
	        $model->setPassword($this->prompt('New password:', [
	            'required' => true,
	            'pattern' => '#^.{6,255}$#i',
	            'error' => 'More than 6 symbols',
	        ]));
	        $this->log($model->save());
	    }
	 
	    /**
	     * @param string $username
	     * @throws \yii\console\Exception
	     * @return User the loaded model
	     */
	    private function findModel($username)
	    {
	        if (!$model = User::findOne(['username' => $username])) {
	            throw new Exception('User not found');
	        }
	        return $model;
	    }
	 
	    /**
	     * @param Model $model
	     * @param string $attribute
	     */
	    private function readValue($model, $attribute)
	    {
	        $model->$attribute = $this->prompt(mb_convert_case($attribute, MB_CASE_TITLE, 'utf-8') . ':', [
	            'validator' => function ($input, &$error) use ($model, $attribute) {
	                $model->$attribute = $input;
	                if ($model->validate([$attribute])) {
	                    return true;
	                } else {
	                    $error = implode(',', $model->getErrors($attribute));
	                    return false;
	                }
	            },
	        ]);
	    }
	 
	    /**
	     * @param bool $success
	     */
	    private function log($success)
	    {
	        if ($success) {
	            $this->stdout('Success!', Console::FG_GREEN, Console::BOLD);
	        } else {
	            $this->stderr('Error!', Console::FG_RED, Console::BOLD);
	        }
	        echo PHP_EOL;
	    }
	}

Теперь можно выполнить:

	php yii users/create

и ввести данные пользователя.

##<a name="template" href="list">Доработка шаблона приложения</a>

Заголовок окна главной страницы формируем в файле представления `index.php` модуля `main`:

	<?php
	/* @var $this yii\web\View */
	$this->title = Yii::$app->name;
	?>

В навигатор добавим логотип и имя:

	...
	NavBar::begin([
        'brandLabel' => '<img class="logo" src="/img/B-Matrix.png">' . Yii::$app->name,
        'brandUrl' => Yii::$app->homeUrl,
        'options' => [
            'class' => 'navbar-inverse navbar-fixed-top',
        ],
    ]);
	...

Главная страница `modules/main/views/default/index.php`:

	<div class="main-default-index">
	    <div class="body-content">
	        <div class="row">
	            <div class="jumbotron">
	                <h1>B-Matrix CMS Yii2</h1>
	                <img class="logo-yii" src="/img/B-Matrix.png">
	                <img class="logo-yii" src="/img/yii.png">
	                <p class="lead">Панель управления и инструменты на базе Yii2-фреймворка. Лёгкий CMS для быстрых сайтов.</p>
	                <p><a class="btn btn-lg btn-success" href="https://github.com/Yii2You/bmatrix">Get started with B-Matrix</a></p>
	            </div>
	        </div>
	        <div id="features">
	            <h2>FEATURES</h2>
	            <div class="feature">
	                <i class="glyphicon glyphicon-dashboard"></i>
	                <h3>Fast engine</h3>
	                <p>Automatically caches all possible content and makes minimum requests to database.</p>
	            </div>
	            <div class="feature">
	                <i class="glyphicon glyphicon-pencil"></i>
	                <h3>Live edit</h3>
	                <p>The main feature is live editable content. You can easily change everything.</p>
	            </div>
	            <div class="feature">
	                <i class="glyphicon glyphicon-wrench"></i>
	                <h3>Easy</h3>
	                <p>Easyii admin panel is very simple. Also development extremely fast and easy.</p>
	            </div>
	            <div class="feature">
	                <i class="glyphicon glyphicon-thumbs-up"></i>
	                <h3>Powered by Yii2</h3>
	                <p>Yii is a high-performance PHP framework best for developing Web 2.0 applications.</p>
	            </div>
	        </div>
	    </div>
	</div>

## <a name="lang" href="list">Язык интерфейса приложения</a>

