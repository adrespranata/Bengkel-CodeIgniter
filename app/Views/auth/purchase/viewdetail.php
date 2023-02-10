<hr>
<table id="listsparepart" class="table table-striped dt-responsive" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
    <thead>
        <tr>
            <th>#</th>
            <th>Barcode</th>
            <th>Nama Sparepart</th>
            <th>Harga Beli</th>
            <th>Qty</th>
            <th>Sub Total</th>
            <th>Aksi</th>
        </tr>
    </thead>


    <tbody>
        <?php $nomor = 0;
        foreach ($datadetail->getResultArray() as $r) :
            $nomor++;; ?>
            <tr>
                <td><?= $nomor  ?></td>
                <td><?= $r['kode'] ?></td>
                <td><?= $r['nama_sparepart'] ?></td>
                <td><?= number_format($r['hargabeli'], 2, ",", ".") ?></td>
                <td><?= $r['qty']; ?></td>
                <td><?= number_format($r['subtotal'], 0, ",", ".") ?></td>

                <td>
                    <button type="button" class="btn btn-danger btn-sm btn-del" onclick="hapusitem('<?= $r['id'] ?>','<?= $r['nama_sparepart'] ?>')">
                        <i class="fa fa-trash"></i>
                    </button>
                </td>
            </tr>
            
        <?php endforeach; ?>
    </tbody>
</table>
<script>
    $(document).ready(function() {
        $('#listsparepart').DataTable({});
    });

    function hapusitem(id, nama_sparepart) {
        Swal.fire({
            title: 'Hapus data?',
            html: `Apakah anda yakin ingin menghapus data produk <strong>${nama_sparepart}</strong> ?`,
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
                    url: "<?= site_url('purchase/hapusItem') ?>",
                    dataType: "json",
                    data: {
                        id: id
                    },
                    success: function(response) {
                        if (response.sukses) {
                            Swal.fire({
                                title: "Berhasil!",
                                icon: "success",
                                text: response.sukses,
                            });
                            dataPurchaseDetail();
                            kosong();
                        }
                    },
                    error: function(xhr, ajaxOptions, thrownError) {
                        alert(xhr.status + "\n" + xhr.responseText + "\n" + thrownError);
                    }
                });
            }
        })
    }
</script>