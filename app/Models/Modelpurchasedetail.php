<?php

namespace App\Models;

use CodeIgniter\Model;

class Modelpurchasedetail extends Model
{
    protected $table      = 'purchase_detail';
    protected $primaryKey = 'detail_purchase';
    protected $allowedFields = ['det_belifaktur', 'det_belibarcode',  'det_hargabeli', 'det_beliqty', 'det_belitotal'];

    public function detailItem($nofaktur)
    {
        return $this->table('purchase_detail')
            ->select('detail_purchase as id, det_belikodebarcode as kode,nama_sparepart ,det_hargabeli as hargabeli,det_beliqty as qty,det_belitotal as total')
            ->join('sparepart', 'kodebarcode=det_belikodebarcode')->where('det_belifaktur', $nofaktur);
    }

    public function dataDetailPurchase($nofaktur)
    {
        return $this->table('purchase_detail')
            ->select('detail_purchase as id, det_belikodebarcode as kode,nama_sparepart ,det_hargabeli as hargabeli,det_beliqty as qty,det_belitotal as subtotal')
            ->join('sparepart', 'kodebarcode=det_belikodebarcode')->where('det_belifaktur', $nofaktur)
            ->orderBy('detail_purchase', 'asc')->get();
    }

    public function ambilTotalHarga($nofaktur)
    {
        $query = $this->table('purchase_detail')
            ->getWhere([
                'det_belifaktur' => $nofaktur
            ]);
        $totalharga = 0;
        foreach ($query->getResultArray() as $r) {
            $totalharga += $r['det_belitotal'];
        }
        return $totalharga;
    }

    public function ambilDetailBerdasarkanID($iddetail)
    {
        return $this->table('purchase_detail')
            ->join('sparepart', 'kodebarcode=det_belikodebarcode')->where('detail_purchase', $iddetail)
            ->orderBy('detail_purchase', 'asc')->get();
    }
}
