<?php
/**
 * Created by PhpStorm.
 * User: stalker
 * Date: 8/10/26
 * Time: 3:22 PM
 */

namespace Application\Assistance\View;

use Config\CC as C;

class Meta
{
    private $schemaOrg = [
        '@context' => 'https://schema.org',
        'organization' => [
            '@type' => 'Organization',
            'name' => '',
            'url' => '',
            'logo' => '',
            'description' => ''
        ],
        'author' => [
            '@type' => 'Person',
            'name' => ''
        ],
        'publisher' => [
            '@type' => 'Organization',
            'name' => '',
            'logo' => [
                '@type' => 'ImageObject',
                'url' => ''
            ]
        ],
        'schema' => [
            '@type' => 'Article',
            'headline' => '',
            'description' => ''
        ]
    ];

    private $title = '';
    private $description = '';
    private $keywords = '';
    private $ogDescription = '';

    /**
     * @var \Application\Assistance\View\View $view
     */
    public function __construct($view)
    {
        $schemaOrg['organization']['name'] = C::locale(C::get('meta_name'));
        $schemaOrg['organization']['url'] = ROOT;
        $schemaOrg['organization']['logo'] = ROOT . C::get('meta_logo');
        $schemaOrg['organization']['description'] = C::locale(C::get('meta_description'));
        $schemaOrg['author']['name'] = C::get('meta_author');
        $schemaOrg['publisher']['name'] = CALLING_DOMEN;
        $schemaOrg['publisher']['logo']['url'] = ROOT . C::get('meta_logo');
        $schemaOrg['schema']['page'] = $view->url($view->module, $view->controller, $view->action);

        $this->title = C::locale('SLOT-H — Single Logical Operating Tool for Hosting');
        $this->description = C::locale('SLOT-H - это боевая экосистема для мультипроектных PHP-систем. Один код - множество проектов. Без микросервисов, без контейнеризации, без головной боли. 15+ лет в продакшене.');
        $this->keywords = C::locale('SLOT-H, PHP, мультипроектность, экосистема, фреймворк, ORM, мультитенантность, сайт за 5 минут');
        $this->ogDescription = C::locale('SLOT-H - экосистема для мультипроектных систем на PHP. Один код - множество проектов. Без микросервисов, без контейнеризации.');
    }

    public function setTitle($value)
    {
        $this->title = $value;
        return $this;
    }

    public function setDescription($value)
    {
        $this->description = trim(preg_replace("/`/", "'", $value));
        return $this;
    }

    public function setKeywords($value)
    {
        $this->keywords = $value;
        return $this;
    }

    public function setOgDescription($value)
    {
        $this->ogDescription = trim(preg_replace("/`/", "'", $value));
        return $this;
    }

    public function setSchemaType($value)
    {
        $this->schemaOrg['schema']['@type'] = $value;
        return $this;
    }

    public function setSchemaHeadLine($value)
    {
        $this->schemaOrg['schema']['headline'] = $value;
        return $this;
    }

    public function setSchemaDescription($value)
    {
        $this->schemaOrg['schema']['description'] = trim(preg_replace("/`/", "'", $value));
        return $this;
    }

    public function getMeta()
    {
        $concat = implode('`', [$this->description, $this->ogDescription, $this->schemaOrg['schema']['description']]);
        if (preg_match("/``/", $concat)) {
            if (preg_match("/`[^`]{1,}`/", $concat)) {
                preg_match("/`([^`]{1,})`/", $concat, $m);
                if (empty($this->description)) {
                    $this->description = $m[1];
                }
                if (empty($this->ogDescription)) {
                    $this->ogDescription = $m[1];
                }
                if (empty($this->schemaOrg['schema']['description'])) {
                    $this->schemaOrg['schema']['description'] = $m[1];
                }
            } else {
                $this->description = ROOT;
                $this->ogDescription = ROOT;
                $this->schemaOrg['schema']['description'] = ROOT;
            }
        }
        echo '<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>' . $this->title . '</title>
    <meta name="description" content="' . $this->description . '">
    <meta name="keywords" content="' . $this->keywords . '">
    <meta name="author" content="' . ROOT . '">
    <meta name="robots" content="index, follow">
    <meta name="googlebot" content="index, follow">
    <meta property="og:title" content="' . $this->title . '">
    <meta property="og:description" content="' . $this->ogDescription . '">
    <meta property="og:type" content="website">
    <meta property="og:url" content="' . ROOT . '">
    <meta property="og:site_name" content="' . CALLING_DOMEN . '">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="' . $this->title . '">
    <meta name="twitter:description"
          content="' . $this->description . '">
    <link rel="canonical" href="' . $this->schemaOrg['schema']['page'] . '">
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,400;14..32,600;14..32,700&display=swap"
          rel="stylesheet">';
//    <link rel="icon" href="/logo_ico_32x32.ico" type="image/x-icon">';
        if (C::get('meta_google') != '') {
            echo '<script async src="https://www.googletagmanager.com/gtag/js?id=' . C::get('meta_google') . '"></script>
            <script>
                window.dataLayer = window.dataLayer || [];
                function gtag() {
                    dataLayer.push(arguments);
                }
                gtag(\'js\', new Date());
                gtag(\'config\', \'' . C::get('meta_google') . '\');
            </script>';
        }
        if (C::get('meta_yandex') != '') {
            echo '<script type="text/javascript">
            (function(m,e,t,r,i,k,a){
                m[i]=m[i]||function(){(m[i].a=m[i].a||[]).push(arguments)};
                m[i].l=1*new Date();
                for (var j = 0; j < document.scripts.length; j++) {if (document.scripts[j].src === r) { return; }}
                k=e.createElement(t),a=e.getElementsByTagName(t)[0],k.async=1,k.src=r,a.parentNode.insertBefore(k,a)
            })(window, document,\'script\',\'https://mc.yandex.ru/metrika/tag.js?id=110332902\', \'ym\');
    
            ym(' . C::get('meta_yandex') . ', \'init\', {ssr:true, webvisor:true, clickmap:true, ecommerce:"dataLayer", referrer: document.referrer, url: location.href, accurateTrackBounce:true, trackLinks:true});
            </script>
            <noscript><div><img src="https://mc.yandex.ru/watch/' . C::get('meta_yandex') . '" style="position:absolute; left:-9999px;" alt="" /></div></noscript>';
        }
        echo '<script type="application/ld+json">' . json_encode($this->schemaOrg, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . '</script>';
    }
}