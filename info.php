<?php
require 'App/Bootstrap.php';

$obj = new Laemmi_YoutubeDownload_YoutubeDownload(Laemmi_YoutubeDownload_Http_Client::factory('Curl'));
$error = false;

$id = isset($_POST['id'])?$_POST['id']:null;

try {
    $session = new Laemmi_YoutubeDownload_Session();
    $session->data = $obj->info($id);
} catch (Laemmi_YoutubeDownloadException $e) {
    $error = true;
}
?>
<a href="#" class="btn btn-primary button_new">Andere Video ID eingeben</a>
<?php if($error): ?>
    <div class="alert alert-danger" style="margin-top: 20px" role="alert">Ups... Du hast eine ungültige Youtube ID eingegeben </div>
<?php else: ?>
    <div class="media">
        <div class="media-left">
            <img src="preview.php?id=<?php echo $id; ?>" alt="<?php echo $session->data['meta']['title']; ?>">
        </div>
        <div class="media-body">
            <h4 class="media-heading"><?php echo $session->data['meta']['title']; ?></h4>
        </div>
    </div>
    <h5>Wähle bitte Dein bevorzugtes Format</h5>
    <div class="list-group">
        <?php
        foreach($session->data['stream'] as $key => $val) {
            echo '<a href="download.php?k='.$key.'" class="list-group-item">'.$val['typename'].'  '.$val['quality'].' ('.$val['size_format'].')</a>';
        }
        ?>
    </div>
<?php endif; ?>