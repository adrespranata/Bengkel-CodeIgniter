<?php

namespace App\Models;

use CodeIgniter\Model;

class Modelpurchasetemp extends Model
{
    protected $table      = 'purchase_temp';
    protected $primaryKey = 'purchase_det';
    protected $allowedFields = ['det_belifaktur', 'det_belibarcode',  'det_hargabeli', 'det_beliqty', 'det_belitotal'];

    //backend
    public function dataDetail($nofaktur)
    {
        return $this->table('purchase_temp')
            ->select('purchase_det as id, det_belikodebarcode as kode,nama_sparepart ,det_hargabeli as hargabeli,det_beliqty as qty,det_belitotal as subtotal')
            ->join('sparepart', 'kodebarcode=det_belikodebarcode')->where('det_belifaktur', $nofaktur)
            ->orderBy('purchase_det', 'asc');
    }

    public function hitungTotalBayar($nofaktur)
    {
        return $this->table('purchase_temp')
            ->select('SUM(det_belitotal) as totalbayar')
            ->where('det_belifaktur', $nofaktur);
    }
}
