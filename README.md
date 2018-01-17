[![](https://avatars0.githubusercontent.com/u/993323?s=100&v=4)](https://github.com/yiisoft)
[![](https://avatars0.githubusercontent.com/u/35477647?s=100&v=4)](https://github.com/Yii2You)

#BMatriX Yii2 - проект CMS Yii2You

<a name="list">Содержание</a>
=============================

1. [Цель](#target)
2. [Решение](#solution)
3. [Пространства имен](#namespace)



## <a name="target">Цель</a> ##

>Создать CMS-проект, отвечающий основным требованиям среднестатистического заказчика (визитка, лэндинг, магазин). Сайт должен включать/отключать готовые модули, а так же иметь возможность расширяться, благодаря созданию новых модулей.


##<a name="solution">РЕШЕНИЯ</a>
Проект будет содержать два уровня доступа:

1. **Frontend** - общедоступная часть 
2. **Backend** - административная часть

### Основные модули

1. **CoreModule** - Главный модуль приложения
2. **DocsModule** - Документация
2. **InstallModule** - Модуль инсталляции
2. **SiteModule** - Модуль фронтэнда
3. **UserModule** -Модуль для управления пользователями

### Структура модуля
	
	module
		|_assets - ресурсы модуля
		|	|_css
		|	|_js
		|_components - компоненты модуля
		|_controllers - контроллеры
		|_install* - файлы конфигурации и миграции бд
		|	|_migrations
		|_messages - файлы интернационализации
		|	|_en
		|	|_ru
		|_models - модели
		|_views - представления
		|_widgets - виджеты

###Базовые компоненоты
1. ***bmx\core\components\WebModule*** - базовый модуль для **всех**! модулей
2. ***bmx\core\components\ModuleManager*** - управление модулями
3. ***bmx\core\components\ConfigManager*** - управление конфигурациями модулей и приложения
4. ***bmx\core\components\controllers\Controller*** - абстрактный класс базового контроллера, родитель **всех**! контроллеров приложения
5. ***bmx\core\components\controllers\BackController*** - абстрактный класс базового контроллера backend-уровня приложения
6. ***bmx\core\components\controllers\FrontController*** - абстрактный класс базового контроллера frontend-уровня приложения


## <a name="namespace">ПРОСТРАНСТВА ИМЕН</a> ##


	bmx/
	|___core/
	|	|__components/
	|	|__controllers/
	|
	|___site
	|___admin
	|___user



СТРУКТУРА ПАПОК
-------------------
    assets
    commands
    config
		|_modules
    mail
        |_layouts
    modules
		|_core
		|_docs
		|_install
		|_site
		|_user
	vendor
	views
	web
	widgets


REQUIREMENTS
------------



INSTALLATION
------------

	
## Quick setup ##

**https**

	https://github.com/Yii2You/bmatrix.git

**create a new repository on the command line**

	echo "# bmatrix" >> README.md
	git init
	git add README.md
	git commit -m "first commit"
	git remote add origin https://github.com/Yii2You/bmatrix.git
	git push -u origin master

**SSH**

	git@github.com:Yii2You/bmatrix.git

**create a new repository on the command line**

	echo "# bmatrix" >> README.md
	git init
	git add README.md
	git commit -m "first commit"
	git remote add origin git@github.com:Yii2You/bmatrix.git
	git push -u origin master

**…or push an existing repository from the command line**

	git remote add origin https://github.com/Yii2You/bmatrix.git
	git push -u origin master

CONFIGURATION
-------------

