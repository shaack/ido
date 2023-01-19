<?php
/**
 * @var \App\View\AppView $this
 * @var array $params
 * @var string $message
 */
if (!isset($params['escape']) || $params['escape'] !== false) {
    $message = h($message);
}
?>
<script>
    bootstrap.showToast({
        body: "<?= $message ?>",
        toastClass: "text-bg-danger",
        closeButtonClass: "btn-close-white",
        position: "top-0 start-0"
    })
</script>
