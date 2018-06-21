<?php
require_once __DIR__ . '/vendor/autoload.php';

$session = new Laemmi\YoutubeDownload\Session();

$url      = isset($_POST['url'])      ? filter_var($_POST['url'], FILTER_SANITIZE_STRING)      : null;
$user     = isset($_POST['user'])     ? filter_var($_POST['user'], FILTER_SANITIZE_STRING)     : null;
$password = isset($_POST['password']) ? filter_var($_POST['password'], FILTER_SANITIZE_STRING) : null;

$error = '';
try {
    $options = null;
    if ($user && $password) {
        $options = new \Laemmi\YoutubeDownload\Service\VimeoOptions();
        $options->setAuthenticate(true);
        $options->setAuthenticateCredentials([
            'email'    => $user,
            'password' => $password,
        ]);
    }
    $service = Laemmi\YoutubeDownload\Service::factory($url, $options);
    $session->data = $service->getData();
} catch (Exception $e) {
    $error = $e->getMessage();
}
?>
<a href="#" class="btn btn-primary button_new">Andere Video URL eingeben</a>
<?php if($error): ?>
    <div class="alert alert-danger" style="margin-top: 20px" role="alert">Ups... Du hast eine ungültige Video Url eingegeben (<?php echo $error; ?>)</div>
<?php else: ?>
    <div class="media">
        <div class="media-left">
            <img src="preview.php" alt="<?php echo $session->data->getTitle(); ?>">
        </div>
        <div class="media-body">
            <h4 class="media-heading"><?php echo $session->data->getTitle(); ?></h4>
        </div>
    </div>
    <h5>Wähle bitte Dein bevorzugtes Format</h5>
    <div class="list-group">
        <?php
        foreach($session->data as $key => $stream) {
            $filter = new Laemmi\YoutubeDownload\Filter\FormatBytes();
            echo '<a href="download.php?k='.$key.'" class="list-group-item">'.$stream->getFormat().'  '.$stream->getQuality().' (' . $filter($stream->getSize()) . ')</a>';
        }
        ?>
    </div>
<?php endif; ?>