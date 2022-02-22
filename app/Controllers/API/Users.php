<?php

namespace App\Controllers\API;

use App\Models\UserModel;
use CodeIgniter\RESTful\ResourceController;

class Users extends ResourceController
{
    protected $modelName = 'App\Models\UserModel';
    protected $format    = 'json';

    /**
    * Devuelve el Listado de usuarios
    * @return json con el listado de usuarios
    */
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

    /**
    * Para crear un usuario
    * @return json con el usuario creado
    * @var request json = {name, role, nick, pass}
    */
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

    
    /**
    * Devuelve el usuario a editar
    * @return json con el usuario
    * @param int $id
    */
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

    
    /**
    * Actualiza cambios del usuario editado
    * @return json con el usuario creado
    * @param int $id 
    * @var json = {name, role, nick, pass}
    */
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

    /**
    * Elimina un usuario
    * @return json con el usuario eliminado
    * @param int $id del usuario
    */
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

    /**
    * Abre una session del usuario logeado
    * @return json con el usuario
    * @var json = {nick, pass}
    */
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

    /**
    * Cierra la session del usuario
    */
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

    /**
    * Verifica que el usuario con session abierta tiene rol administrador
    * @return boolean true si es un usuario con rol jefatura
    */
    private function adminSession(){
        $session = session();
        return ($session->get('user_role')) == 'admin';
    }
}
