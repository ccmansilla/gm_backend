<?php

namespace App\Controllers\API;

use App\Models\UserModel;
use CodeIgniter\RESTful\ResourceController;

class Users extends ResourceController
{
    protected $modelName = 'App\Models\UserModel';
    protected $format    = 'json';

    public function index()
    {
        try{  
            if($this->adminSession()){
                return $this->respond($this->model->findAll());
            } else {
                return $this->failUnauthorized('Acceso no autorizado');
            }
        } catch (\Exception $e) {
            return $this->failServerError('Ha ocurrido un error en el servidor');
        }
    }

    public function create()
    {
        try {
            if($this->adminSession()){
                $user = $this->request->getJSON();
                if($this->model->insert($user)){
                    $user->id = $this->model->insertID();
                    return $this->respondCreated($user);
                } else {
                    return $this->failValidationErrors($this->model->validation->listErrors());
                }
            } else {
                return $this->failUnauthorized('Acceso no autorizado');
            }
        } catch (\Exception $e) {
            return $this->failServerError('Ha ocurrido un error en el servidor');
        }
    }

    public function edit($id = NULL)
    {
        try {
            if($this->adminSession()){
                if($id == NULL){
                    return $this->failValidationErrors('No se ha pasado un ID valido');
                }
                $user = $this->model->find($id);
                if($user == NULL){
                    return $this->failNotFound('No se ha encontrado el usuario con el ID: '.$id);
                }
                return $this->respond($user);
            } else {
                return $this->failUnauthorized('Acceso no autorizado');
            }
        } catch (\Exception $e) {
            return $this->failServerError('Ha ocurrido un error en el servidor');
        }
    }

    public function update($id = NULL)
    {
        try {
            if($this->adminSession()){
                if($id == NULL){
                    return $this->failValidationErrors('No se ha pasado un ID valido');
                }
                $userCheck = $this->model->find($id);
                if($userCheck == NULL){
                    return $this->failNotFound('No se ha encontrado el usuario con el ID: '.$id);
                }
                $user = $this->request->getJSON();
                if($this->model->update($id,$user)){
                    $user->id = $id;
                    return $this->respondUpdated($user);
                } else {
                    return $this->failValidationErrors($this->model->validation->listErrors());
                }
            } else {
                return $this->failUnauthorized('Acceso no autorizado');
            }
        } catch (\Exception $e) {
            return $this->failServerError('Ha ocurrido un error en el servidor');
        }
    }

    public function delete($id = NULL)
    {
        try {
            if($this->adminSession()){
                if($id == NULL){
                    return $this->failValidationErrors('No se ha pasado un ID valido');
                }
                $user = $this->model->find($id);
                if($user == NULL){
                    return $this->failNotFound('No se ha encontrado el usuario con el ID: '.$id);
                }
                if($this->model->delete($id)){
                    return $this->respondDeleted($user);
                } else {
                    return $this->failServerError('No se ha podido eliminar el registro');
                }
            } else {
                return $this->failUnauthorized('Acceso no autorizado');
            }   
        } catch (\Exception $e) {
            return $this->failServerError('Ha ocurrido un error en el servidor');
        }
    }

    public function login(){;
        $data = $this->request->getJSON();
        $nick = (isset($data->nick))? $data->nick : NULL;
        $pass = (isset($data->pass))? $data->pass : NULL;
        if($nick == NULL || $pass == NULL){
            return $this->failValidationErrors('No se ha pasado un nick o pass en formato json');
        }

        try{
            $user = $this->model->check($nick, $pass);
            if($user){
                $session = session();
                $session->set('user_id', $user['id']);
                $session->set('user_role', $user['role']);
                $session->set('user_name', $user['name']);

                return $this->respond($user);
            } else {
                return $this->failNotFound('No se ha encontrado el usuario');
            }
        } catch (\Exception $e) {
            return $this->failServerError('Ha ocurrido un error en el servidor');
        }
    }

    public function logout(){
        try{

            $session = session();
            $session->remove('user_id');
            $session->remove('user_role');
            $session->remove('user_name');
            $session->destroy();

            return $this->respond([]);
        } catch (\Exception $e) {
            return $this->failServerError('Ha ocurrido un error en el servidor');
        }

    }

    private function adminSession(){
        $session = session();
        return ($session->get('user_id')) != null;
    }
}
