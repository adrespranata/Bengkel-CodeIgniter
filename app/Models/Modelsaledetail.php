<?php

namespace App\Models;

use CodeIgniter\Model;

class Modelsaledetail extends Model
{
    protected $table      = 'sale_detail';
    protected $primaryKey = 'detail_sale';
    protected $allowedFields = ['det_jualfaktur', 'det_jualbarcode',  'det_hargajual', 'det_jualqty', 'det_jualtotal'];

    public function detailItem($nofaktur)
    {
        return $this->table('sale_detail')
            ->select('detail_sale as id, det_jualkodebarcode as kode,nama_sparepart ,det_hargajual as hargajual,det_jualqty as qty,det_jualtotal as total')
            ->join('sparepart', 'kodebarcode=det_jualkodebarcode')->where('det_jualfaktur', $nofaktur);
    }

    public function dataDetailSale($nofaktur)
    {
        return $this->table('sale_detail')
            ->select('detail_sale as id, det_jualkodebarcode as kode,nama_sparepart ,det_hargajual as hargajual,det_jualqty as qty,det_jualtotal as subtotal')
            ->join('sparepart', 'kodebarcode=det_jualkodebarcode')->where('det_jualfaktur', $nofaktur)
            ->orderBy('detail_sale', 'asc')->get();
    }

    public function ambilTotalHarga($nofaktur)
    {
        $query = $this->table('sale_detail')
            ->getWhere([
                'det_jualfaktur' => $nofaktur
            ]);
        $totalharga = 0;
        foreach ($query->getResultArray() as $r) {
            $totalharga += $r['det_jualtotal'];
        }
        return $totalharga;
    }

    public function ambilDetailBerdasarkanID($iddetail)
    {
        return $this->table('sale_detail')
            ->join('sparepart', 'kodebarcode=det_jualkodebarcode')->where('detail_sale', $iddetail)
            ->orderBy('detail_sale', 'asc')->get();
    }
}
