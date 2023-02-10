<table id="listsale" class="table table-striped dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
    <thead>
        <tr>
            <th>#</th>
            <th>Faktur</th>
            <th>Tanggal</th>
            <th>Pelanggan</th>
            <th>Jumlah Item</th>
            <th>Total</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <?php $nomor = 0;
    foreach ($list as $data) :
        $nomor++; ?>
        <tr>
            <td><?= $nomor ?></td>
            <td><?= $data['jual_faktur'] ?></td>
            <td><?= date('d-m-Y', strtotime($data['jual_date'])) ?></td>
            <td><?= $data['nama_pelanggan'] ?></td>
            <td>
                <?php
                $db = \Config\Database::connect();

                $jumlahitem = $db->table('sale_detail')->where('det_jualfaktur', $data['jual_faktur'])->countAllResults();
                ?>
                <span style="cursor: pointer; color:blue; font-weight: bold;" onclick="detailItem('<?= $data['jual_faktur'] ?>')"><?= $jumlahitem ?></span>
            </td>
            <td><?= number_format($data['jual_total'], 0, ',', '.')  ?></td>

            <td>
                <button type="button" class="btn btn-success btn-sm" title="Print Sale" onclick="cetak('<?= $data['jual_faktur'] ?>')">
                    <i class="fa fa-print"></i>
                </button>&nbsp;
                <button type="button" class="btn btn-primary btn-sm" title="Edit Sale" onclick="edit('<?= sha1($data['jual_faktur'])  ?>')">
                    <i class="fa fa-edit"></i>
                </button>&nbsp;
                <button type="button" class="btn btn-danger btn-sm" title="Hapus Sale" onclick="hapus('<?= $data['jual_faktur'] ?>')">
                    <i class="fa fa-trash"></i>
                </button>&nbsp;

            </td>
        </tr>

    <?php endforeach; ?>

    <tbody>

    </tbody>
</table>
<div class="viewmodal" style="display: none;"></div>

<script>
    $(document).ready(function() {
        $('#listsale').DataTable();
    });

    function detailItem(jual_faktur) {
        $.ajax({
            type: "post",
            url: "<?= site_url('sale/detailItem') ?>",
            dataType: "json",
            data: {
                jual_faktur: jual_faktur
            },
            success: function(response) {
                if (response.data) {
                    $('.viewmodal').html(response.data).show();
                    $('#modalitem').modal('show');
                }
            }
        });
    }

    function cetak(faktur) {
        window.location.href = ('<?= site_url('sale/cetakfaktur/') ?>') + faktur;
    }

    function edit(faktur) {
        window.location.href = ('<?= site_url('sale/edit/') ?>') + faktur;
    }

    function hapus(faktur) {
        Swal.fire({
            title: 'Hapus data?',
            html: `Apakah anda yakin menghapus data <strong>${faktur}</strong>?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya!',
            cancelButtonText: 'Tidak'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    type: "post",
                    url: "<?= site_url('sale/hapus') ?>",
                    dataType: "json",
                    data: {
                        faktur: faktur
                    },
                    success: function(response) {
                        if (response.sukses) {
                            Swal.fire({
                                title: "Berhasil!",
                                html: response.sukses + `<strong>${faktur}</strong> Berhasil Dihapus`,
                                icon: "success",
                                showConfirmButton: false,
                                timer: 1500
                            }).then((result) => {
                                window.location.reload();
                            })
                        }
                    }
                });
            }
        })
    }
</script>