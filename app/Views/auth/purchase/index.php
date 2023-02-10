<?= $this->extend('layout/script') ?>

<?= $this->section('judul') ?>
<div class="col-sm-6">
    <h4 class="page-title"><?= $title ?></h4>
</div>
<div class="col-sm-6">
    <ol class="breadcrumb float-right">
        <li class="breadcrumb-item"><a href="javascript:void(0);">Purchase</a></li>
        <li class="breadcrumb-item active">List Purchase</li>
    </ol>
</div>
<?= $this->endSection('judul') ?>

<?= $this->section('isi') ?>
<p class="sub-title">
    <button type="button" class="btn btn-primary btn-sm" onclick="window.location='<?= site_url('purchase/formtambah') ?>'">
        <i class=" fa fa-plus-circle"></i> Tambah Purchase
    </button>
</p>

<div class="viewdata">
</div>

<script>
    function listpurchase() {
        $.ajax({
            url: "<?= site_url('purchase/getdata') ?>",
            dataType: "json",
            success: function(response) {
                $('.viewdata').html(response.data);
            }
        });
    }

    $(document).ready(function() {
        listpurchase();
    });
</script>
<?= $this->endSection('isi') ?>