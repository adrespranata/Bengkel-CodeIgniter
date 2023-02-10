<!-- Modal -->
<div class="modal fade" id="modalpembayaran" tabindex="-1" aria-labelledby="modalpembayaranLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalpembayaranLabel"><?= $title ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <?= form_open('purchase/simpanPurchase', ['class' => 'formpembayaran']) ?>
            <div class="modal-body">
                <div class="form-group">
                    <label for="">No Faktur</label>
                    <input type="text" name="nofaktur" id="nofaktur" class="form-control form-control-lg" value="<?= $nofaktur ?>" readonly>
                </div>
                <div class="form-group">
                    <label for="">Supplier</label>
                    <input type="hidden" name="supplier_id" value="<?= $supplier_id ?>">
                    <input type="text" name="nama_supplier" id="nama_supplier" class="form-control form-control-lg" value="<?= $nama_supplier ?>" readonly>
                </div>
                <div class="form-group">
                    <input type="hidden" name="totalbayar" id="totalbayar" value="<?= $totalbayar ?>">
                </div>
                <hr>
                <table id="listsparepart" class="table table-striped dt-responsive" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Barcode</th>
                            <th>Nama Sparepart</th>
                            <th>Harga beli</th>
                            <th>Qty</th>
                            <th>Sub Total</th>
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
                            </tr>

                        <?php endforeach; ?>
                    </tbody>
                </table>
                <hr>
                <div class="form-group">
                    <label for="">Total Pembayaran</label>
                    <input type="text" name="total" id="total" class="form-control form-control-lg" value="<?= $totalbayar ?>" style="font-weight: bold; text-align: right; color: blue; font-size: 30pt;" readonly>
                </div>

            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary tombolSimpan">Simpan</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
            <?= form_close() ?>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        $('#listsparepart').DataTable({});
    });

    $('#total').autoNumeric('init', {
        aSep: ',',
        aDec: '.',
        mDec: '0'
    });

    $('.formpembayaran').submit(function(e) {
        e.preventDefault();
        $.ajax({
            type: "post",
            url: $(this).attr('action'),
            data: $(this).serialize(),
            dataType: "json",
            beforeSend: function() {
                $('.tombolSimpan').prop('disable', true);
                $('.tombolSimpan').html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> <i>Loading...</i>');
            },
            complete: function() {
                $('.tombolSimpan').prop('disable', false);
                $('.tombolSimpan').html('Simpan');
            },
            success: function(response) {
                if (response.sukses == 'berhasil') {
                    Swal.fire({
                        title: "Transaksi Berhasil Disimpan!",
                        text: response.sukses,
                        icon: "success",
                        showConfirmButton: false,
                        timer: 1500
                    }).then((result) => {
                        window.location.reload();
                    })
                }
            },
            error: function(xhr, thrownError) {
                alert(xhr.status + "\n" + xhr.responseText + "\n" + thrownError);
            }
        });
        return false;
    });
</script>