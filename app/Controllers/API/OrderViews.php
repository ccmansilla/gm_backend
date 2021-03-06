<?php

namespace App\Controllers\API;
use App\Models\UserModel;
use CodeIgniter\RESTful\ResourceController;

class OrderViews extends ResourceController
{
    protected $modelName = 'App\Models\OrderViewModel';
    protected $format    = 'json';
    
    /**
     * Devuelve la lista de usuarios que tienen visto de la orden
     * @return json con la lista de usuarios
     * @param $order_id de la orden  
     */
    public function index($order_id = NULL)
    {
        try {
            if($order_id == NULL){
                return $this->failValidationErrors('No se ha pasado un ID valido');
            }
            $users = $this->model->users($order_id);
            return $this->respond($users);
        } catch (\Exception $e) {
            return $this->failServerError('Ha ocurrido un error en el servidor');
        }
    }

    /**
     * Crea un visto de una orden
     * @var json {'order_id', 'user_id'}
     */
    public function create()
    {
        try {
            $view = $this->request->getJSON();
            if($this->model->insert($view)){
                return $this->respondCreated($view);
            } else {
                return $this->failValidationErrors($this->model->validation->listErrors());
            }
        } catch (\Exception $e) {
            return $this->failServerError('Ha ocurrido un error en el servidor');
        }
    }

    /**
     * Elimina un visto de una orden
     * @param $id del visto
     */
    public function delete($id = NULL)
    {
        try {
            if($id == NULL){
                return $this->failValidationErrors('No se ha pasado un ID valido');
            }
            $view = $this->model->find($id);
            if($view == NULL){
                return $this->failNotFound('No se ha encontrado el usuario con el ID: '.$id);
            }
            if($this->model->delete($id)){
                return $this->respondDeleted($view);
            } else {
                return $this->failServerError('No se ha podido eliminar el registro');
            }
        } catch (\Exception $e) {
            return $this->failServerError('Ha ocurrido un error en el servidor');
        }
    }
}
