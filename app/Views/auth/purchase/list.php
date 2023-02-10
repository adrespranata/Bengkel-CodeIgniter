<hr>
<table id="listpurchase" class="table table-striped dt-responsive" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
    <thead>
        <tr>
            <th>#</th>
            <th>No Faktur</th>
            <th>Tanggal</th>
            <th>Supplier</th>
            <th>Jumlah Item</th>
            <th>Total Harga</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <?php $nomor = 0;
    foreach ($list as $data) :
        $nomor++; ?>
        <tr>
            <td><?= $nomor ?></td>
            <td><?= $data['beli_faktur'] ?></td>
            <td><?= date('d-m-Y', strtotime($data['beli_date'])) ?></td>
            <td><?= $data['nama_supplier'] ?></td>
            <td>
                <?php
                $db = \Config\Database::connect();

                $jumlahitem = $db->table('purchase_detail')->where('det_belifaktur', $data['beli_faktur'])->countAllResults();
                ?>
                <span style="cursor: pointer; color:blue; font-weight: bold;" title="list data" onclick="detailItem('<?= $data['beli_faktur'] ?>')"><?= $jumlahitem ?></span>
            </td>
            <td><?= number_format($data['beli_total'], 0, ',', '.')  ?></td>
            <td>
                <button type="button" class="btn btn-primary btn-sm" title="Edit Purchase" onclick="edit('<?= sha1($data['beli_faktur'])  ?>')">
                    <i class="fa fa-edit"></i>
                </button>&nbsp;
                <button type="button" class="btn btn-danger btn-sm" title="Hapus Purchase" onclick="hapus('<?= $data['beli_faktur'] ?>')">
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
    function detailItem(beli_faktur) {
        $.ajax({
            type: "post",
            url: "<?= site_url('purchase/detailItem') ?>",
            dataType: "json",
            data: {
                beli_faktur: beli_faktur
            },
            success: function(response) {
                if (response.data) {
                    $('.viewmodal').html(response.data).show();
                    $('#modalitem').modal('show');
                }
            }
        });
    }

    function edit(faktur) {
        window.location.href = ('<?= site_url('purchase/edit/') ?>') + faktur;
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
                    url: "<?= site_url('purchase/hapus') ?>",
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


    $(document).ready(function() {
        $('#listpurchase').DataTable();
    });
</script>