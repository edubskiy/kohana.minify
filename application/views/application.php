<!DOCTYPE html>
<!--[if lt IE 7]> <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang="en"> <![endif]-->
<!--[if IE 7]>    <html class="no-js lt-ie9 lt-ie8" lang="en"> <![endif]-->
<!--[if IE 8]>    <html class="no-js lt-ie9" lang="en"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" lang="en"> <!--<![endif]-->
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">

    <? /* @var $App Application */ ?>
    <title><?=$App->title?></title>

    <meta name="description" content="<?=$App->description?>">
    <meta name="author" content="<?=$App->author?>">
    
    <meta name="viewport" content="width=device-width">

    <link rel="shortcut icon" href="<?=$App->Favicon()?>">

    <!-- Place favicon.ico and apple-touch-icon.png in the root of your domain and delete these references -->
    <link rel="apple-touch-icon" href="<?=$App->AppleFavicon()?>">

    <!--
        Includes Internally Compiled Styles ({Yui, GCompile} => JAVA = true, {PhpMinify} => JAVA = false)
                                    OR
        Includes Externally Compiled Styles usgin BUILD SCRIPT
    -->
    <link href="<?=$App->GetCompiledStyleIncludePath()?>" rel="stylesheet">

    <!-- Excluded styles from compressing -->
    <? foreach($App->standaloneStyles as $standaloneStyle): ?>
        <link href="<?=$standaloneStyle?>" rel="stylesheet">
    <? endforeach ?>

    <? if ( ! $App->IsProduction()): ?>

        <!-- Uncomment if not using build script to compile LESS -->
        <link rel="stylesheet/less" href="<?=$App->webLess?>style.less">
        <script src="<?=$App->webJs?>libs/less-1.3.0.min.js"></script>

    <? endif ?>

    <script src="<?=$App->webJs?>libs/modernizr-2.5.3-respond-1.1.0.min.js"></script>

</head>

<body>
<!--[if lt IE 7]><p class=chromeframe>Your browser is <em>ancient!</em> <a href="http://browsehappy.com/">Upgrade to a different browser</a> or <a href="http://www.google.com/chromeframe/?redirect=true">install Google Chrome Frame</a> to experience this site.</p><![endif]-->

<?=$App->header?>
<?=$App->content?>

<!-- Back Compatibility to Run Old Way Compessed JS -->

<!--    <script src="--><?//=$App->GetCompiledScriptIncludePath()?><!--"></script>-->

<!--    --><?// foreach($App->standaloneScripts as $standaloneScript): ?>
<!--    <script src="--><?//=$standaloneScript?><!--"></script>-->
<!--    --><?// endforeach ?>

</body>

<script src="//ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
<script>window.jQuery || document.write('<script src="js/libs/jquery-1.7.2.min.js"><\/script>')</script>

<script src="<?=$App->GetCompiledScriptIncludePath()?>"></script>

<? foreach($App->standaloneScripts as $standaloneScript): ?>
    <script src="<?=$standaloneScript?>"></script>
<? endforeach ?>

<!-- scripts concatenated and minified via ant build script-->
<!--<script src="--><?//=$App->webJs?><!--libs/bootstrap/bootstrap.min.js"></script>-->
<!--<script src="--><?//=$App->webJs?><!--plugins.js"></script>-->
<!--<script src="--><?//=$App->webJs?><!--script.js"></script>-->
<!-- end scripts-->

</html>