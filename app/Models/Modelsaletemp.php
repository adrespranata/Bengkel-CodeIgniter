<?php

namespace App\Models;

use CodeIgniter\Model;

class Modelsaletemp extends Model
{
    protected $table      = 'sale_temp';
    protected $primaryKey = 'sale_det';
    protected $allowedFields = ['det_jualfaktur', 'det_jualbarcode',  'det_hargajual', 'det_jualqty', 'det_jualtotal'];

    //backend
    public function dataDetail($nofaktur)
    {
        return $this->table('sale_temp')
            ->select('sale_det as id, det_jualkodebarcode as kode,nama_sparepart ,det_hargajual as hargajual,det_jualqty as qty,det_jualtotal as subtotal')
            ->join('sparepart', 'kodebarcode=det_jualkodebarcode')->where('det_jualfaktur', $nofaktur)
            ->orderBy('sale_det', 'asc');
    }

    public function hitungTotalBayar($nofaktur)
    {
        return $this->table('sale_temp')
            ->select('SUM(det_jualtotal) as totalbayar')
            ->where('det_jualfaktur', $nofaktur);
    }
}
