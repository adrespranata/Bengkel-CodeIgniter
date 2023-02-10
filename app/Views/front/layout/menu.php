<?= $this->extend('front/layout/main') ?>
<?= $this->section('navbar') ?>
<nav class="nav-menu d-none d-lg-block">
    <ul>
        <li class="active"><a href="#hero">Home</a></li>
        <li><a href="#visimisi">Visi Misi</a></li>
        <li><a href="#staf">Staf</a></li>
        <li><a href="#footer">Contact</a></li>
        <li><a href="<?= base_url('auth/login') ?>">Login</a></li>
    </ul>
</nav><!-- .nav-menu -->

<?= $this->endSection('navbar') ?>
<?= $this->section('isi') ?>

<!-- ======= About Section ======= -->
<section id="visimisi" class="about">
    <div class="container" data-aos="fade-up">

        <div class="section-title">
            <h2>Visi & Misi</h2>
        </div>

        <div class="row">

            <div class="col-lg-6 pt-4 pt-lg-0 order-2 order-lg-1 content">
                <h5>Visi</h5>
                <p class="font-italic">
                    <?= $konfigurasi['visi'] ?>
                </p>
                <h5>Misi</h5>
                <p class="font-italic">
                    <?= $konfigurasi['misi'] ?>
                </p>
            </div>
        </div>

    </div>
</section><!-- End About Section -->


<section id="staf" class="trainers">
    <div class="container" data-aos="fade-up">

        <div class="section-title">
            <h2>Staf</h2>
        </div>
        <div class="row" data-aos="zoom-in" data-aos-delay="100">
            <?php
            foreach ($list_staf as $data) :
            ?>
                <div class="col-lg-4 col-md-6 d-flex align-items-stretch">
                    <div class="member">
                        <img src="<?= base_url('img/staf/thumb/' . 'thumb_' . $data['foto']) ?>" width="100%" class="img-fluid" alt="">
                        <div class="member-content">
                            <h4><?= $data['nama_staf'] ?></h4>
                            <span><?= $data['jabatan'] ?></span>
                            <p>
                                <?= $data['alamat'] ?>
                            </p>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

    </div>
</section>

<?= $this->endSection('isi') ?>