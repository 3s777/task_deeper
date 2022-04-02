<?php $this->layout('layout_users', ['title' => 'Профиль пользователя']) ?>
        <nav class="navbar navbar-expand-lg navbar-dark bg-primary bg-primary-gradient">
            <a class="navbar-brand d-flex align-items-center fw-500" href="#"><img alt="logo" class="d-inline-block align-top mr-2" src="/assets/img/logo.png"> Учебный проект</a> <button aria-controls="navbarColor02" aria-expanded="false" aria-label="Toggle navigation" class="navbar-toggler" data-target="#navbarColor02" data-toggle="collapse" type="button"><span class="navbar-toggler-icon"></span></button>
            <div class="collapse navbar-collapse" id="navbarColor02">
                <ul class="navbar-nav mr-auto">
                    <li class="nav-item ">
                        <a class="nav-link" href="#">Главная</a>
                    </li>
                </ul>
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="/logout">Выйти</a>
                    </li>
                </ul>
            </div>
        </nav>
        <main id="js-page-content" role="main" class="page-content mt-3">
            <div class="subheader">
                <h1 class="subheader-title">
                    <i class='subheader-icon fal fa-user'></i> <?php echo $user['username']; ?>
                </h1>
            </div>
            <div class="row">
              <div class="col-lg-6 col-xl-6 m-auto">
                    <!-- profile summary -->
                    <div class="card mb-g rounded-top">
                        <div class="row no-gutters row-grid">
                            <div class="col-12">
                                <div class="d-flex flex-column align-items-center justify-content-center p-4">
                                    <img src="<?php echo $path; ?>" class="rounded-circle shadow-2 img-thumbnail" style="max-width: 200px" alt="">
                                    <h5 class="mb-0 fw-700 text-center mt-3">
                                        <?php echo $user['username']; ?>
                                        <small class="text-muted mb-0"><?php echo $user['job']; ?></small>
                                    </h5>
                                    <div class="mt-4 text-center demo">
                                        <a href="<?php echo $user['instagram']; ?>" class="fs-xl" style="color:#C13584">
                                            <i class="fab fa-instagram"></i>
                                        </a>
                                        <a href="<?php echo $user['vk']; ?>" class="fs-xl" style="color:#4680C2">
                                            <i class="fab fa-vk"></i>
                                        </a>
                                        <a href="<?php echo $user['telegram']; ?>" class="fs-xl" style="color:#0088cc">
                                            <i class="fab fa-telegram"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="p-3 text-center">
                                    <a href="tel:+13174562564" class="mt-1 d-block fs-sm fw-400 text-dark">
                                        <i class="fas fa-mobile-alt text-muted mr-2"></i> <?php echo $user['phone']; ?></a>
                                    <a href="mailto:oliver.kopyov@marlin.ru" class="mt-1 d-block fs-sm fw-400 text-dark">
                                        <i class="fas fa-mouse-pointer text-muted mr-2"></i> <?php echo $user['email']; ?></a>
                                    <address class="fs-sm fw-400 mt-4 text-muted">
                                        <i class="fas fa-map-pin mr-2"></i> <?php echo $user['address']; ?>
                                    </address>
                                </div>
                            </div>
                        </div>
                    </div>
               </div>
            </div>
        </main>
    </body>

    <script src="/assets/js/vendors.bundle.js"></script>
    <script src="/assets/js/app.bundle.js"></script>
    <script>

        $(document).ready(function()
        {

        });

    </script>
