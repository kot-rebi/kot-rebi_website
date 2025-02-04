<?php
require_once 'vendor/autoload.php';
require_once __DIR__ . '/Config.php';
$config = Config::getInstance();

require_once $config->get('paths')['models'] . '/Database.php';
require_once $config->get('paths')['models'] . '/ArticleModel.php';
require_once $config->get('paths')['models'] . '/UserModel.php';

require_once $config->get('paths')['controllers'] . '/BaseArticleController.php';