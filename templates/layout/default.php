<?php
/**
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link          https://cakephp.org CakePHP(tm) Project
 * @since         0.10.0
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 * @var \App\View\AppView $this
 */

$controller = $this->request->getParam('controller')
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?= $this->Html->charset() ?>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>
        <?= $this->fetch('title') ?>
    </title>
    <?= $this->Html->meta('icon') ?>

    <link href="/assets/fontawesome-subset/css/fontawesome.min.css" rel="stylesheet">
    <link href="/assets/fontawesome-subset/css/solid.min.css" rel="stylesheet">
    <link href="/assets/fontawesome-subset/css/regular.min.css" rel="stylesheet">
    <link href="/node_modules/simplemde/dist/simplemde.min.css" rel="stylesheet">
    <link href="/assets/styles/simplemde-theme-dark.css" rel="stylesheet">
    <!--
    <script defer src="/assets/fontawesome-subset/js/solid.min.js"></script>
    <script defer src="/assets/fontawesome-subset/js/regular.min.js"></script>
    -->
    <link href="/assets/styles/screen.css" rel="stylesheet">

    <script src="/node_modules/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
    <script src="/node_modules/bootstrap-show-toast/src/bootstrap-show-toast.js"></script>
    <script src="/node_modules/simplemde/dist/simplemde.min.js"></script>

    <?= $this->fetch('meta') ?>
    <?= $this->fetch('css') ?>
    <?= $this->fetch('script') ?>
</head>
<body>
<header>
    <?php if (!$this->get('hideNavigation')) : ?>
        <nav class="navbar navbar-expand-sm navbar-light bg-light">
            <div class="container-fluid">
                <a class="navbar-brand" href="<?= $this->Url->build('/tasks?filter=customers') ?>"><span class="char-1">I</span><span
                        class="char-2">d</span><span class="char-3">o</span></a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                        data-bs-target="#navbarSupportedContent"
                        aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav ms-auto mb-lg-0">
                        <li class="nav-item">
                            <a class="nav-link <?= $controller != "Customers" ?: "active" ?>"
                               href="/customers?current=1">Customers</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= $controller != "Projects" ?: "active" ?>" href="/projects?current=1">Projects</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= $controller != "Services" ?: "active" ?>"
                               href="/services">Services</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= $controller != "Tasks" ?: "active" ?>" href="/tasks?filter=customers">Tasks</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= $controller != "TimeTrackings" ?: "active" ?>"
                               href="/time-trackings">Time Trackings</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    <?php endif ?>
</header>
<main class="main">
    <div class="container-fluid">
        <?= $this->Flash->render() ?>
        <?= $this->fetch('content') ?>
    </div>
</main>
<footer>
</footer>
<!-- <script src="/node_modules/bootstrap-input-spinner/src/bootstrap-input-spinner.js"></script> -->
<script>
    let editors = document.querySelectorAll("textarea.markdown")
    for (const editor of editors) {
        editor.simpleMDE = new SimpleMDE({element: editor, promptURLs: true, spellChecker: false})
    }
</script>
</body>
</html>
