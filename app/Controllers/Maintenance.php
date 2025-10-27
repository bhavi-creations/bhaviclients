<?php

namespace App\Controllers;

use App\Models\MaintenanceModel;
use App\Models\ClientModel;

class Maintenance extends BaseController
{
    protected $maintenanceModel;
    protected $clientModel;

    public function __construct()
    {
        $this->maintenanceModel = new MaintenanceModel();
        $this->clientModel = new ClientModel();
        helper(['form', 'url']);
    }

    // Admin: List all, Client: List their own
    public function index()
    {
        $roleId = session()->get('role_id');
        if ($roleId != 1) {
            return redirect()->to(base_url('dashboard'))->with('error', 'Unauthorized.');
        }

        // Get only clients who have at least one maintenance record
        $builder = $this->maintenanceModel
            ->select('clients.id, clients.name, clients.owner_first_name, clients.owner_last_name, clients.email, clients.phone, COUNT(maintenance.id) as record_count')
            ->join('clients', 'clients.id = maintenance.client_id')
            ->groupBy('clients.id')
            ->orderBy('clients.name', 'ASC');

        $clients = $builder->findAll();

        return view('maintenance/index', [
            'title' => 'Maintenance',
            'clients' => $clients
        ]);
    }


    public function create()
    {
        $this->restrictAdmin();
        $clients = $this->clientModel->findAll();
        return view('maintenance/create', [
            'title' => 'Create Maintenance Record',
            'clients' => $clients
        ]);
    }

    public function store()
    {
        $this->restrictAdmin();
        $data = $this->request->getPost();
        if (!$this->validate([
            'client_id' => 'required|integer',
            'title' => 'required|min_length[2]|max_length[255]',
            'description' => 'permit_empty'
        ])) {
            return redirect()->back()->withInput()->with('error', implode(', ', $this->validator->getErrors()));
        }
        $this->maintenanceModel->save([
            'client_id' => $data['client_id'],
            'title' => $data['title'],
            'description' => $data['description'],
        ]);
        return redirect()->to(base_url('maintenance'))->with('success', 'Record added!');
    }

    public function edit($id)
    {
        $this->restrictAdmin();
        $record = $this->maintenanceModel->find($id);
        if (!$record) return redirect()->to(base_url('maintenance'))->with('error', 'Record not found');
        $clients = $this->clientModel->findAll();
        return view('maintenance/edit', [
            'title' => 'Edit Maintenance',
            'record' => $record,
            'clients' => $clients
        ]);
    }

    public function update($id)
    {
        $this->restrictAdmin();
        $data = $this->request->getPost();
        if (!$this->validate([
            'client_id' => 'required|integer',
            'title' => 'required|min_length[2]|max_length[255]',
            'description' => 'permit_empty'
        ])) {
            return redirect()->back()->withInput()->with('error', implode(', ', $this->validator->getErrors()));
        }
        $this->maintenanceModel->update($id, [
            'client_id' => $data['client_id'],
            'title' => $data['title'],
            'description' => $data['description'],
        ]);
        return redirect()->to(base_url('maintenance'))->with('success', 'Record updated!');
    }

    public function delete($id)
    {
        $this->restrictAdmin();
        $this->maintenanceModel->delete($id);
        return redirect()->to(base_url('maintenance'))->with('success', 'Deleted!');
    }

    private function restrictAdmin()
    {
        if (session()->get('role_id') != 1) {
            exit('Unauthorized.');
        }
    }

    public function view($id)
    {
        $roleId = session()->get('role_id');
        $user = session()->get();
        $record = $this->maintenanceModel->find($id);
        if (!$record) return redirect()->to(base_url('maintenance'))->with('error', 'Not found');
        if (
            $roleId == 1 ||
            ($roleId == 3 && $user['client_id'] == $record['client_id']) ||
            ($roleId == 4 && $user['client_id'] == $record['client_id'])
        ) {
            return view('maintenance/view', [
                'title' => 'Maintenance Details',
                'record' => $record
            ]);
        } else {
            return redirect()->to(base_url('dashboard'))->with('error', 'Unauthorized.');
        }
    }
    public function client($clientId)
    {
        $roleId = session()->get('role_id');
        if ($roleId != 1) {
            return redirect()->to(base_url('dashboard'))->with('error', 'Unauthorized.');
        }

        $client = $this->clientModel->find($clientId);
        if (!$client) return redirect()->to(base_url('maintenance'))->with('error', 'Client not found');

        $records = $this->maintenanceModel
            ->where('client_id', $clientId)
            ->orderBy('id', 'DESC')
            ->findAll();

        return view('maintenance/client_detail', [
            'title' => 'Maintenance for ' . $client['name'],
            'client' => $client,
            'records' => $records
        ]);
    }

    public function clientView()
    {
        $roleId = session()->get('role_id');
        $user = session()->get();

        // Get the correct client id
        if ($roleId == 3 || $roleId == 4) {
            $clientId = $user['client_id'];
            $client = $this->clientModel->find($clientId);
            if (!$client) {
                return redirect()->to(base_url('dashboard'))->with('error', 'Client not found!');
            }
            $records = $this->maintenanceModel
                ->where('client_id', $clientId)
                ->orderBy('id', 'DESC')
                ->findAll();

            return view('maintenance/client_panel', [
                'title' => 'My Maintenance Records',
                'client' => $client,
                'records' => $records
            ]);
        }
        return redirect()->to(base_url('dashboard'))->with('error', 'Unauthorized!');
    }
}
