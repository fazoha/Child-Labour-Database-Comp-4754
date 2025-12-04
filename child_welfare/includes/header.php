<?php
// includes/header.php

// simple helpers for "active" nav highlighting
$currentFile = basename($_SERVER['PHP_SELF']);
$currentTable = $_GET['table'] ?? '';
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Child Welfare System</title>
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
      rel="stylesheet">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container">
    <a class="navbar-brand" href="home.php">Child Welfare System</a>

    <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
            data-bs-target="#mainNav" aria-controls="mainNav"
            aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="mainNav">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item">
          <a class="nav-link <?= ($currentTable === 'child' || $currentFile === 'view_child.php') ? 'active' : '' ?>"
             href="index.php?table=child">
            Children
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?= ($currentTable === 'school') ? 'active' : '' ?>"
             href="index.php?table=school">
            Schools
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?= ($currentTable === 'district') ? 'active' : '' ?>"
             href="index.php?table=district">
            Districts
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?= ($currentTable === 'ngo') ? 'active' : '' ?>"
             href="index.php?table=ngo">
            NGOs
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?= ($currentTable === 'labour') ? 'active' : '' ?>"
             href="index.php?table=labour">
            Labour
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?= ($currentTable === 'local_crisis') ? 'active' : '' ?>"
             href="index.php?table=local_crisis">
            Crises
          </a>
        </li>
      </ul>
    </div>
  </div>
</nav>

<!-- Secondary menu for link tables -->
<div class="bg-light border-bottom mb-4">
  <div class="container py-2">
    <div class="d-flex flex-wrap align-items-center">
      <span class="me-3 fw-semibold text-muted">Relationships:</span>

      <a href="links_child_works_in.php"
         class="btn btn-sm me-2 mb-1 <?= $currentFile === 'links_child_works_in.php' ? 'btn-primary' : 'btn-outline-secondary' ?>">
        Child <> Labour
      </a>

      <a href="links_crisis_effects.php"
         class="btn btn-sm me-2 mb-1 <?= $currentFile === 'links_crisis_effects.php' ? 'btn-primary' : 'btn-outline-secondary' ?>">
        Crisis <> District
      </a>

      <a href="links_ngo_operates_in.php"
         class="btn btn-sm me-2 mb-1 <?= $currentFile === 'links_ngo_operates_in.php' ? 'btn-primary' : 'btn-outline-secondary' ?>">
        NGO <> District
      </a>

      <a href="links_support.php"
         class="btn btn-sm me-2 mb-1 <?= $currentFile === 'links_support.php' ? 'btn-primary' : 'btn-outline-secondary' ?>">
        NGO <> School (Support)
      </a>
    </div>
  </div>
</div>

<div class="container mb-5">
