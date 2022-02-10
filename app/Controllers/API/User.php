<?php

namespace App\Controllers\API;

use CodeIgniter\RESTful\ResourceController;

class User extends ResourceController
{
    protected $modelName = 'App\Models\User';
    protected $format    = 'json';

    /**
     * Return an array of resource objects, themselves in array format
     *
     * @return mixed
     */
    public function index()
    {
        return $this->respond($this->model->findAll());
    }

    /**
     * Return the properties of a resource object
     *
     * @return mixed
     */
    public function show($id = null)
    {
        //
    }

    /**
     * Return a new resource object, with default properties
     *
     * @return mixed
     */
    public function new()
    {
        //
    }

    /**
     * Create a new resource object, from "posted" parameters
     *
     * @return mixed
     */
    public function create()
    {
        //
    }

    /**
     * Return the editable properties of a resource object
     *
     * @return mixed
     */
    public function edit($id = null)
    {
        //
    }

    /**
     * Add or update a model resource, from "posted" properties
     *
     * @return mixed
     */
    public function update($id = null)
    {
        //
    }

    /**
     * Delete the designated resource object from the model
     *
     * @return mixed
     */
    public function delete($id = null)
    {
        //
    }

    /**
     * Login user open session, json with nick and pass
     *
     * @return user 
     */
    public function login()
    {
        $data = $this->request->getJSON();
        if(isset($data->nick) && isset($data->pass)){
            $user = $this->model->check($data);
            if($user != null){
                $session = session();
                $session->set('user_id', $user['id']);
                $session->set('user_role', $user['role']);
                $session->set('user_name', $user['name']);
                $this->respond($user);
            } else {
                return $this->failValidationErrors('No existe el usuario');
            }         
        } else {
            return $this->failValidationErrors('No se ha pasado un nick o pass en formato json');
        }
    }

    /**
     * Logout user close session
     *
     * @return mixed
     */
    public function logout()
    {
        $session = session();
        $session->remove('user_id');
        $session->remove('user_role');
        $session->remove('user_name');
        $session->destroy();
        return $this->respond('exit');
    }

}
