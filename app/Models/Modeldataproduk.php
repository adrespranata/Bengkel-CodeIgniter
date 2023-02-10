<?php

namespace App\Models;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\Model;

class Modeldataproduk extends Model
{
    protected $table      = 'sparepart';
    protected $column_order = array(null, 'kodebarcode', 'nama_sparepart', 'date');
    protected $column_search = array('kodebarcode', 'nama_sparepart');
    protected $order = array('kodebarcode' => 'asc');
    protected $request;
    protected $db;
    protected $dt;

    protected $useTimestamps = false;
    protected $updatedField  = 'date';

    //backend
    function __construct(RequestInterface $request)
    {
        parent::__construct();
        $this->db = db_connect();
        $this->request = $request;
    }
    private function _get_datatables_query($keyData)
    {
        // $this->dt = $this->db->table($this->table);
        if (strlen($keyData) == 0) {
            $this->dt = $this->db->table($this->table);
        } else {
            $this->dt = $this->db->table($this->table)->like('kodebarcode', $keyData)->orLike('nama_sparepart', $keyData);
        }

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
    function get_datatables($keyData)
    {
        $this->_get_datatables_query($keyData);
        if (isset($_POST['length' != -1]))
            $this->dt->limit($_POST['length'], $_POST['start']);
        $query = $this->dt->get();
        return $query->getResult();
    }
    function count_filtered($keyData)
    {
        $this->_get_datatables_query($keyData);
        return $this->dt->countAllResults();
    }
    public function count_all($keyData)
    {
        //$tbl_storage = $this->db->table($this->table);
        if (strlen($keyData) == 0) {
            $tbl_storage = $this->db->table($this->table);
        } else {
            $tbl_storage = $this->db->table($this->table)->like('kodebarcode', $keyData)->orLike('nama_sparepart', $keyData);
        }

        return $tbl_storage->countAllResults();
    }
}
