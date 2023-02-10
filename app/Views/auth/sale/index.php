<?= $this->extend('layout/script') ?>

<?= $this->section('judul') ?>
<div class="col-sm-6">
    <h4 class="page-title"><?= $title ?></h4>
</div>
<div class="col-sm-6">
    <ol class="breadcrumb float-right">
        <li class="breadcrumb-item"><a href="javascript:void(0);">Sale</a></li>
        <li class="breadcrumb-item active">List Sale</li>
    </ol>
</div>
<?= $this->endSection('judul') ?>

<?= $this->section('isi') ?>
<p class="sub-title">
    <button type="button" class="btn btn-primary btn-sm" onclick="window.location='<?= site_url('sale/formtambah') ?>'"><i class=" fa fa-plus-circle"></i> Tambah transaksi</button>
</p>
<div class="viewdata">
</div>

<script>
    function listsale() {
        $.ajax({
            url: "<?= site_url('sale/getdata') ?>",
            dataType: "json",
            success: function(response) {
                $('.viewdata').html(response.data);
            }
        });
    }

    $(document).ready(function() {
        listsale();
    });
</script>
<?= $this->endSection('isi') ?>