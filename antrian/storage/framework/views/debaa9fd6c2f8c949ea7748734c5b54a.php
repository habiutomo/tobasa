<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title><?php echo $__env->yieldContent('title', 'Antrian Puskesmas'); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <?php echo $__env->yieldPushContent('styles'); ?>
    <style>
        body { background: #f4f6f9; }
        .sidebar {
            min-height: 100vh;
            background: #0d6efd;
            color: #fff;
        }
        .sidebar .nav-link { color: rgba(255,255,255,.8); }
        .sidebar .nav-link:hover, .sidebar .nav-link.active { color: #fff; }
        .sidebar .brand {
            display: block;
            padding: 1rem;
            font-weight: 700;
            font-size: 1.1rem;
            background: rgba(0,0,0,.1);
            text-decoration: none;
            color: #fff;
        }
        .user-avatar {
            width: 36px; height: 36px;
            border-radius: 50%;
            background: #fff;
            color: #0d6efd;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
        }
    </style>
</head>
<body>
<div class="row g-0">
    <div class="col-auto">
        <div class="sidebar d-flex flex-column">
            <a href="<?php echo e(route('admin.dashboard')); ?>" class="brand">
                <i class="bi bi-hospital"></i> Antrian Puskesmas
            </a>
            <?php if(auth()->user()->isAdmin()): ?>
            <ul class="nav nav-pills flex-column px-2 py-3">
                <li class="nav-item">
                    <a class="nav-link <?php echo e(request()->routeIs('admin.dashboard') ? 'active' : ''); ?>" href="<?php echo e(route('admin.dashboard')); ?>">
                        <i class="bi bi-speedometer2"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo e(request()->routeIs('admin.klaster*') ? 'active' : ''); ?>" href="<?php echo e(route('admin.klaster')); ?>">
                        <i class="bi bi-diagram-3"></i> Klaster
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo e(request()->routeIs('admin.polis*') ? 'active' : ''); ?>" href="<?php echo e(route('admin.polis')); ?>">
                        <i class="bi bi-clipboard2-pulse"></i> Layanan
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo e(request()->routeIs('admin.loket*') ? 'active' : ''); ?>" href="<?php echo e(route('admin.loket')); ?>">
                        <i class="bi bi-box2"></i> Loket
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo e(request()->routeIs('admin.user*') ? 'active' : ''); ?>" href="<?php echo e(route('admin.user')); ?>">
                        <i class="bi bi-people"></i> Petugas
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo e(request()->routeIs('admin.setting*') ? 'active' : ''); ?>" href="<?php echo e(route('admin.setting')); ?>">
                        <i class="bi bi-gear"></i> Pengaturan
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo e(request()->routeIs('admin.laporan') ? 'active' : ''); ?>" href="<?php echo e(route('admin.laporan')); ?>">
                        <i class="bi bi-file-earmark-bar-graph"></i> Laporan
                    </a>
                </li>
            </ul>
            <?php endif; ?>
            <div class="mt-auto p-3">
                <a href="<?php echo e(route('logout')); ?>"
                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                   class="btn btn-sm btn-light w-100">
                    <i class="bi bi-box-arrow-right"></i> Logout
                </a>
                <form id="logout-form" action="<?php echo e(route('logout')); ?>" method="POST" class="d-none">
                    <?php echo csrf_field(); ?>
                </form>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="p-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h1 class="h4 mb-0"><?php echo $__env->yieldContent('pageTitle', 'Panel'); ?></h1>
                    <small class="text-muted"><?php echo $__env->yieldContent('pageSubtitle', ''); ?></small>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="text-muted small"><?php echo e(auth()->user()->name); ?></span>
                    <span class="user-avatar"><?php echo e(strtoupper(substr(auth()->user()->name, 0, 1))); ?></span>
                </div>
            </div>

            <?php if(session('success')): ?>
                <div class="alert alert-success alert-dismissible fade show">
                    <?php echo e(session('success')); ?>

                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            <?php if(session('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show">
                    <?php echo e(session('error')); ?>

                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php echo $__env->yieldContent('content'); ?>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html><?php /**PATH D:\tobasa.elnusaintegrated.cloud\antrian\resources\views/layouts/app.blade.php ENDPATH**/ ?>