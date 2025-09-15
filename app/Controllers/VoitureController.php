<?php

namespace App\Controllers;

use App\Models\VoitureModel;
use Config\JWT;
use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;

class VoitureController extends ResourceController
{
    use ResponseTrait;

    protected $jwt;
    protected $voitureModel;

    public function __construct()
    {
        $this->jwt = new JWT();
        $this->voitureModel = new VoitureModel();
    }

    protected function getUserIdFromToken()
    {
        $header = $this->request->getHeader('Authorization');
        if (!$header) {
            return false;
        }

        $token = str_replace('Bearer ', '', $header->getValue());
        $decoded = $this->jwt->decode($token);

        return $decoded ? $decoded->uid : false;
    }

    public function index()
    {
        $userId = $this->getUserIdFromToken();
        if (!$userId) {
            return $this->failUnauthorized('Token invalide ou expiré');
        }

        $voitures = $this->voitureModel->getVoituresByUser($userId);
        return $this->respond($voitures);
    }

    public function show($id = null)
    {
        $userId = $this->getUserIdFromToken();
        if (!$userId) {
            return $this->failUnauthorized('Token invalide ou expiré');
        }

        $voiture = $this->voitureModel->find($id);
        if (!$voiture || $voiture['user_id'] != $userId) {
            return $this->failNotFound('Voiture non trouvée');
        }

        return $this->respond($voiture);
    }

    public function create()
    {
        $userId = $this->getUserIdFromToken();
        if (!$userId) {
            return $this->failUnauthorized('Token invalide ou expiré');
        }

        $data = [
            'marque' => $this->request->getVar('marque'),
            'modele' => $this->request->getVar('modele'),
            'annee' => $this->request->getVar('annee'),
            'prix' => $this->request->getVar('prix'),
            'couleur' => $this->request->getVar('couleur'),
            'kilometrage' => $this->request->getVar('kilometrage'),
            'carburant' => $this->request->getVar('carburant'),
            'description' => $this->request->getVar('description'),
            'user_id' => $userId
        ];

        if ($this->voitureModel->save($data)) {
            return $this->respondCreated([
                'status' => 201,
                'message' => 'Voiture créée avec succès',
                'data' => $data
            ]);
        } else {
            return $this->failServerError('Erreur lors de la création de la voiture');
        }
    }

    public function update($id = null)
    {
        $userId = $this->getUserIdFromToken();
        if (!$userId) {
            return $this->failUnauthorized('Token invalide ou expiré');
        }

        $voiture = $this->voitureModel->find($id);
        if (!$voiture || $voiture['user_id'] != $userId) {
            return $this->failNotFound('Voiture non trouvée');
        }

        $data = [
            'marque' => $this->request->getVar('marque') ?? $voiture['marque'],
            'modele' => $this->request->getVar('modele') ?? $voiture['modele'],
            'annee' => $this->request->getVar('annee') ?? $voiture['annee'],
            'prix' => $this->request->getVar('prix') ?? $voiture['prix'],
            'couleur' => $this->request->getVar('couleur') ?? $voiture['couleur'],
            'kilometrage' => $this->request->getVar('kilometrage') ?? $voiture['kilometrage'],
            'carburant' => $this->request->getVar('carburant') ?? $voiture['carburant'],
            'description' => $this->request->getVar('description') ?? $voiture['description']
        ];

        if ($this->voitureModel->update($id, $data)) {
            return $this->respond([
                'status' => 200,
                'message' => 'Voiture mise à jour avec succès'
            ]);
        } else {
            return $this->failServerError('Erreur lors de la mise à jour de la voiture');
        }
    }

    public function delete($id = null)
    {
        $userId = $this->getUserIdFromToken();
        if (!$userId) {
            return $this->failUnauthorized('Token invalide ou expiré');
        }

        $voiture = $this->voitureModel->find($id);
        if (!$voiture || $voiture['user_id'] != $userId) {
            return $this->failNotFound('Voiture non trouvée');
        }

        if ($this->voitureModel->delete($id)) {
            return $this->respond([
                'status' => 200,
                'message' => 'Voiture supprimée avec succès'
            ]);
        } else {
            return $this->failServerError('Erreur lors de la suppression de la voiture');
        }
    }
}