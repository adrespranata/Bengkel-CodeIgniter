<div class="modal fade" id="modalitem" tabindex="-1" aria-labelledby="modalitemLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalitemLabel"><?= $title ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <table class="table table-striped dt-responsive" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
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
                        foreach ($tampildetitem->getResultArray() as $r) :
                            $nomor++;; ?>
                            <tr>
                                <td><?= $nomor  ?></td>
                                <td><?= $r['kode'] ?></td>
                                <td><?= $r['nama_sparepart'] ?></td>
                                <td><?= number_format($r['hargabeli'], 0, ",", ".") ?></td>
                                <td><?= $r['qty']; ?></td>
                                <td><?= number_format($r['total'], 0, ",", ".") ?></td>
                            </tr>

                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>