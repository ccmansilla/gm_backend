<?php

namespace App\Models;

use CodeIgniter\Model;

class VolanteModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'volantes';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['estado', 'fecha', 'number', 'year', 'origen', 'destino', 'asunto', 'enlace_archivo', 'enlace_adjunto'];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [
        'fecha' => 'required|valid_date',
        'number' => 'required|numeric|min_length[1]|max_length[11]',
        'year' => 'required|numeric|min_length[2]|max_length[2]',
        'origen' => 'required|numeric|min_length[1]|max_length[11]',
        'destino' => 'required|numeric|min_length[1]|max_length[11]',
        'asunto' => 'required|min_length[2]',
        'enlace_archivo' => 'required'
    ];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    public function enviados($user_id) {
        $builder = $this->builder();
        $builder->getTable('volantes');
        $builder->select('*');
        $builder->join('users', 'users.id = volantes.destino');
        $builder->where("volantes.origen = $user_id");
        $builder->orderBy('year DESC, number DESC');
        //$volantes = $builder->get($limit, $start)->getResult('array');
        $volantes = $builder->get()->getResult('array');
        return $volantes;
    }

    public function recibidos($user_id) {
        $builder = $this->builder();
        $builder->getTable('volantes');
        $builder->select('*');
        $builder->join('users', 'users.id = volantes.origen');
        $builder->where("destino = $user_id");
        $builder->orderBy('year DESC, number DESC');
        //$volantes = $builder->get($limit, $start)->getResult('array');
        $volantes = $builder->get()->getResult('array');
        return $volantes;
    }

    public function next_number($user_id, $year){
        $builder = $this->builder();
        $builder->getTable('volantes');
        $builder->select('max(number)');
        $builder->where("origen = $user_id and year = $year");
        $volante = $builder->get()->getResult('array');
        $number = 1;
        if(count($volante) > 0){
            $number = $volante[0]['max(number)'] + 1;
        }
        return $number;
    }
    
}