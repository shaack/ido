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
        toastClass: "text-bg-success",
        closeButtonClass: "btn-close-white",
        delay: 1000,
        position: "top-0 start-0"
    })
</script>
