<?php

namespace App\Models;

use CodeIgniter\Model;

class VoitureModel extends Model
{
    protected $table = 'voitures';
    protected $primaryKey = 'id';
    protected $allowedFields = ['marque', 'modele', 'annee', 'prix', 'couleur', 'kilometrage', 'carburant', 'description', 'user_id', 'created_at', 'updated_at'];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    public function getVoituresByUser($userId)
    {
        return $this->where('user_id', $userId)->findAll();
    }
}