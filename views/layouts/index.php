<!DOCTYPE html>
<html lang="ru">
<head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="<?=$this->pageDescription?>">
    <meta name="author" content="AndrewSirius">
    <link rel="shortcut icon" href="data:image/png;base64,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" type="image/png">

    <title><?=$this->pageTitle?></title>

    <link rel="stylesheet" href="<?=Tools::url('css/bootstrap.min.css')?>">
    <link rel="stylesheet" href="<?=Tools::url('css/cover.css')?>">

    <script src="<?=Tools::url('js/jquery-1.11.3.min.js')?>"></script>

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>
<body>

<div class="site-wrapper">
    <div class="site-wrapper-inner">
        <div class="cover-container">

            <?$this->template()?>

            <div class="mastfoot">
                <div class="inner">
                    <p>All rights reserved. © <?=date('Y') == '2015' ? '2015' : '2015 - ' . date('Y')?> &laquo;10k points&raquo;</p>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="message-box"></div>

<script src="<?=Tools::url('js/tools.js')?>"></script>
<script src="<?=Tools::url('js/index.js')?>"></script>
<script>
    var session_from_url = '';
    var session_redirect_message = '';
    $(document).ready(function() {
        session_from_url = '<?=empty($_SESSION['from_url']) ? '' : $_SESSION['from_url']?>';
        session_redirect_message = JSON.parse('<?=empty($_SESSION['redirect_message']) ? '{}' : $_SESSION['redirect_message']?>');

        if (session_redirect_message.message != undefined) {
            showMessage(session_redirect_message.message, session_redirect_message.type);
        }
    });
    <?unset($_SESSION['from_url']);?>
    <?unset($_SESSION['redirect_message']);?>
</script>
</body>
<!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
<script src="<?=Tools::url('js/ie10-viewport-bug-workaround.js')?>"></script>
<script src="<?=Tools::url('js/bootstrap.min.js')?>"></script>
</html>