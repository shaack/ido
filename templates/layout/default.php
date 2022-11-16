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

    <link href="/assets/styles/screen.css" rel="stylesheet">

    <?= $this->fetch('meta') ?>
    <?= $this->fetch('css') ?>
    <?= $this->fetch('script') ?>
</head>
<body>
<header>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container-fluid">
            <a class="navbar-brand" href="<?= $this->Url->build('/projects?current=1') ?>"><span class="char-1">I</span><span
                    class="char-2">d</span><span class="char-3">o</span></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                    data-bs-target="#navbarSupportedContent"
                    aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link <?= $controller != "Customers" ?: "active" ?>" href="/customers?current=1">Customers</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= $controller != "Projects" ?: "active" ?>" href="/projects?current=1">Projects</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= $controller != "Services" ?: "active" ?>" href="/services">Services</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= $controller != "Tasks" ?: "active" ?>" href="/tasks">Tasks</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= $controller != "TimeTrackings" ?: "active" ?>" href="/time-trackings">Time Trackings</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
</header>
<main class="main">
    <div class="container-fluid max-width-xxl">
        <?= $this->Flash->render() ?>
        <?= $this->fetch('content') ?>
    </div>
</main>
<footer>
</footer>
<script src="/node_modules/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
