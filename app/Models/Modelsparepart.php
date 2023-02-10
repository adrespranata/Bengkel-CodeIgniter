<?php

namespace App\Models;

use CodeIgniter\Model;

class Modelsparepart extends Model
{
    protected $table      = 'sparepart';
    protected $primaryKey = 'kodebarcode';
    protected $allowedFields = ['kodebarcode', 'nama_sparepart', 'harga_jual', 'harga_beli', 'stok'];

    //backend
    public function list()
    {
        return $this->table('sparepart')
            ->orderBy('nama_sparepart', 'ASC')
            ->get()->getResultArray();
    }

    //purchase
    public function tempPurchase($kodebarcode)
    {
        return $this->table('sparepart')
            ->like('kodebarcode', $kodebarcode)
            ->orLike('nama_sparepart', $kodebarcode);
    }

    public function detailPurchase($kodebarcode)
    {
        return $this->table('sparepart')
            ->like('kodebarcode', $kodebarcode)
            ->orLike('nama_sparepart', $kodebarcode);
    }

    //sale
    public function tempSale($kodebarcode)
    {
        return $this->table('sparepart')
            ->like('kodebarcode', $kodebarcode)
            ->orLike('nama_sparepart', $kodebarcode);
    }

    public function detailSale($kodebarcode)
    {
        return $this->table('sparepart')
            ->like('kodebarcode', $kodebarcode)
            ->orLike('nama_sparepart', $kodebarcode);
    }
}
