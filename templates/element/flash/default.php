<?php
/**
 * @var \App\View\AppView $this
 * @var array $params
 * @var string $message
 */
$class = 'message';
if (!empty($params['class'])) {
    $class .= ' ' . $params['class'];
}
if (!isset($params['escape']) || $params['escape'] !== false) {
    $message = h($message);
}
?>
<script>
    bootstrap.showToast({
        body: "<?= $message ?>"
        // toastClass: "text-bg-success",
        // closeButtonClass: "btn-close-white"
    })
</script>
