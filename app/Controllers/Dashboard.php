<?php

namespace App\Controllers;

class Dashboard extends BaseController
{
    public function index()
    {
        if (!session()->get('user_id')) {
            return redirect()->to('login');
        }
        $staf = $this->staf->selectCount('staf_id')->first();
        $supplier = $this->supplier->selectCount('supplier_id')->first();
        $pelanggan = $this->pelanggan->selectCount('pelanggan_id')->first();
        $sparepart = $this->sparepart->selectCount('kodebarcode')->first();
        $purchase = $this->purchase->selectCount('beli_faktur')->first();
        $sale = $this->sale->selectCount('jual_faktur')->first();
        $data = [
            'title' => 'Admin - Dashboard',
            'staf' => $staf,
            'supplier' => $supplier,
            'pelanggan' => $pelanggan,
            'sparepart' => $sparepart,
            'purchase' => $purchase,
            'sale' => $sale
        ];
        return view('auth/dashboard', $data);
    }
}
