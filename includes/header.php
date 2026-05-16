<?php
session_start();
require_once 'db.php';

define('BASE_URL', '/cafe_website/');   // ← Change only this if folder name changes
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title><?= $page_title ?? 'Cafe Bastions' ?></title>
    <link rel="stylesheet" href="<?= BASE_URL ?>css/styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        #feet {
            background: #212529;
            color: #fff;
            padding: 20px 0;
            text-align: center;
            margin-top: 50px;
        }

        body.dark-mode {
            background: #121212;
            color: #fff;
        }

        body.dark-mode .navbar,
        body.dark-mode #feet {
            background: #1a1a1a !important;
        }
    </style>
</head>

<body onload="getCurrentYear()">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="index.php">Cafe Bastions ⋆☕︎ ˖</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarSupportedContent">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a id="butt" class="btn btn-dark ms-3" role="button">☼</a>
                    </li>
                    <li class="nav-item"><a class="nav-link" href="/cafe_website/index.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="/cafe_website/about.php">About</a></li>
                    <li class="nav-item"><a class="nav-link" href="/cafe_website/services.php">Menu</a></li>
                    <li class="nav-item"><a class="nav-link" href="/cafe_website/contact.php">Contact</a></li>

                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle text-warning d-flex align-items-center gap-2" href="#"
                                role="button" data-bs-toggle="dropdown" aria-expanded="false">

                                <!-- Profile Picture -->
                                <?php
                                $profile_pic = $_SESSION['profile_pic'] ?? '';
                                if (!empty($profile_pic) && file_exists($profile_pic)):
                                    ?>
                                    <img src="/cafe_website/<?= htmlspecialchars($profile_pic) ?>" alt="Profile"
                                        class="rounded-circle border border-light"
                                        style="width: 28px; height: 28px; object-fit: cover;">
                                <?php else: ?>
                                    <span style="font-size: 1.3rem;">👤</span>
                                <?php endif; ?>

                                <span><?= htmlspecialchars($_SESSION['username']) ?></span>
                            </a>

                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="/cafe_website/profile.php">My Profile</a></li>

                                <?php
                                // Show Dashboard only for Admin (role = 1)
                                if (isset($_SESSION['role']) && $_SESSION['role'] == 1):
                                    ?>
                                    <li><a class="dropdown-item" href="/cafe_website/admin/dashboard.php">Dashboard</a></li>
                                <?php endif; ?>

                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li><a class="dropdown-item text-danger" href="/cafe_website/logout.php">Logout</a></li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li class="nav-item"><a class="nav-link" href="/cafe_website/login.php">Login</a></li>
                        <li class="nav-item"><a class="nav-link" href="/cafe_website/register.php">Register</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>