<?php

namespace App\Models;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\Model;

class Modelpurchase extends Model
{
    protected $table      = 'purchase';
    protected $primaryKey = 'beli_faktur';
    protected $allowedFields = ['supplier_id', 'beli_date', 'beli_total'];
    protected $column_order = array('beli_faktur', 'supplier_id', 'beli_date', 'beli_total', null);
    protected $order = array('beli_faktur' => 'asc');

    protected $useTimestamps = false;
    protected $updatedField  = 'beli_date';

    protected $request;
    protected $db;
    protected $dt;

    //backend
    function __construct(RequestInterface $request)
    {
        parent::__construct();
        $this->db = db_connect();
        $this->request = $request;

        $this->dt = $this->db->table($this->table);
    }
    private function _get_datatables_query()
    {
        $i = 0;
        foreach ($this->column_search as $item) {
            if (isset($_POST['search']['value'])) {
                if ($i === 0) {
                    $this->dt->groupStart();
                    $this->dt->like($item, $_POST['search']('value'));
                } else {
                    $this->dt->orLike($item, $_POST['search']['value']);
                }
                if (count($this->column_search) - 1 == $i)
                    $this->dt->groupEnd();
            }
            $i++;
        }

        if (isset($_POST['order'])) {
            $this->dt->orderBy($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
        } else if (isset($this->order)) {
            $order = $this->order;
            $this->dt->orderBy(key($order), $order[key($order)]);
        }
    }
    function get_datatables()
    {
        $this->_get_datatables_query();
        if (isset($_POST['length' != -1]))
            $this->dt->limit($_POST['length'], $_POST['start']);
        $query = $this->dt->get();
        return $query->getResult();
    }
    function count_filtered()
    {
        $this->_get_datatables_query();
        return $this->dt->countAllResults();
    }
    public function count_all()
    {
        $tbl_storage = $this->db->table($this->table);
        return $tbl_storage->countAllResults();
    }

    public function list()
    {
        return $this->table('purchase')
            ->join('supplier', 'supplier.supplier_id=purchase.supplier_id')
            ->orderBy('beli_faktur', 'ASC')
            ->get()->getResultArray();
    }

    public function cekFaktur($faktur)
    {
        return $this->table('purchase')->getWhere([
            'sha1(beli_faktur)' => $faktur
        ]);
    }

    public function laporanPerPeriode($tglawal, $tglakhir)
    {
        return $this->table('purchase')
            ->where('beli_date >=', $tglawal)->where('beli_date <=', $tglakhir)
            ->get();
    }
}
