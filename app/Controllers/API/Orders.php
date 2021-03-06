<?php

namespace App\Controllers\API;

use App\Models\UserModel;
use CodeIgniter\RESTful\ResourceController;

class Orders extends ResourceController
{
    protected $modelName = 'App\Models\OrderModel';
    protected $format    = 'json';
    private $upload_path = 'public/uploads/orders/'; //path for upload
    private $allowed_types = 'pdf'; //restrict extension
    private $max_size = 2048;

    /**
    * Devuelve el Listado de ordenes de acuerdo al tipo
    * @return json con el listado de ordenes
    * @param string $type $page $about
    */
    public function index($type = NULL, $page = 0, $about = '')
    {
        $limit = 10;
        $start = 0;
        if($page > 0){
            $start = $page * $limit;
        }
        try{  
			if($this->jefaturaSession() || $this->dependenciaSession()){
				if($type == NULL || ($type != 'od' && $type != 'og' && $type != 'or')){
					return $this->failValidationErrors('No se ha pasado un tipo de orden valido');
				}
                $session = session();
                $user = $session->get('user_id');
				return $this->respond($this->model->obtener($type, $start, $limit, $about, $user));
			} else {
				return $this->failUnauthorized('Acceso no autorizado');
			}
        } catch (\Exception $e) {
            return $this->failServerError('Ha ocurrido un error en el servidor');
        }
    }


    /**
    * Para crear una orden
    * @return json con la orden creada
    * @var post type, number, year, date, about, file
    */
    public function create()
    {
        try {
            if($this->jefaturaSession()){
                
                $type = $this->request->getPost('type');
                $year = $this->request->getPost('year'); 
			    $number = $this->request->getPost('number');
			    $name = $type.'_'.$year.'_'.$number.'.pdf';

                $file = $this->request->getFile('file');
                if ($file == NULL) {
                    return $this->failServerError('No cargo un archivo');
                }

                $ext = $file->getClientExtension();
                $size = $file->getSize() / 1024;

                if (! $file->isValid() || $ext != $this->allowed_types || $size > $this->max_size) {
                    return $this->failServerError('No es un archivo valido debe ser pdf menor 2mb');
                }

                $date = $this->request->getPost('date');
                $about =  $this->request->getPost('about');
                $path = $this->upload_path . $name;
                $data = [
                    'type' => $type,
                    'number' => $number,
                    'year' => $year,
                    'date' => $date, 
                    'about' => $about, 
                    'file_url' => $path
                ];

                if($this->model->insert($data)){
                    $data['id'] = $this->model->insertID();
                    if ($file->move(ROOTPATH.$this->upload_path, $name)){
                        return $this->respondCreated($data);
                    } else {
                        return $this->failServerError('No se pudo cargar el archivo ');
                    }
                } else {
                    return $this->failValidationErrors($this->model->validation->listErrors());
                }

            } else {
                return $this->failUnauthorized('Acceso no autorizado');
            }
        } catch (\Exception $e) {
            return $this->failServerError('Ha ocurrido un error en el servidor '.$e);
        }
    }

    /**
    * Devuelve la orden para editar
    * @return json con la orden
    * @param int $id  de la orden
    */
    public function edit($id = NULL)
    {
        try {
            if($this->jefaturaSession()){
                if($id == NULL){
                    return $this->failValidationErrors('No se ha pasado un ID valido');
                }
                $order = $this->model->find($id);
                if($order == NULL){
                    return $this->failNotFound('No se ha encontrado la orden con el ID: '.$id);
                } else {
                    return $this->respond($order);
                }
            } else {
                return $this->failUnauthorized('Acceso no autorizado');
            }   
        } catch (\Exception $e) {
            return $this->failServerError('Ha ocurrido un error en el servidor');
        }
    }

    /**
    * Actualiza los cambios de una orden editada
    * @param int $id  de la orden
    * @var post type, number, year, date, about, file
    */
    public function update($id = NULL)
    {
        try {
            if($this->jefaturaSession()){
                if($id == NULL){
                    return $this->failValidationErrors('No se ha pasado un ID valido');
                }
                $order = $this->model->find($id);
                if($order == NULL){
                    return $this->failNotFound('No se ha encontrado el usuario con el ID: '.$id);
                } else {
                    $type = $this->request->getPost('type');
                    $year = $this->request->getPost('year'); 
                    $number = $this->request->getPost('number');
                    $name = $type.'_'.$year.'_'.$number.'.pdf';
                    
                    $file = $this->request->getFile('file');
                    $old_file = $order['file_url'];
                    $path = $old_file;

                    if ($file != NULL) {
                        $ext = $file->getClientExtension();
                        $size = $file->getSize() / 1024;
                        if ($file->isValid() && $ext == $this->allowed_types && $size < $this->max_size) {
                            $path = $this->upload_path . $name;
                        }
                    }
                    
                    $date = $this->request->getPost('date');
                    $about =  $this->request->getPost('about');
                    
                    $data = [
                        'type' => $type,
                        'number' => $number,
                        'year' => $year,
                        'date' => $date, 
                        'about' => $about, 
                        'file_url' => $path
                    ];

                    if($this->model->update($id, $data)){
                        if($file->getSize() > 0){
                            unlink(ROOTPATH . $old_file);
                            if ($file->move(ROOTPATH.$this->upload_path, $name)){
                                $data['id'] = $order['id'];
                                return $this->respond($data);
                            } else {
                                return $this->failServerError('No se pudo cargar el archivo ');
                            }
                        } else {
                            $data['id'] = $order['id'];
                            return $this->respond($data);
                        }
                    } else {
                        return $this->failValidationErrors($this->model->validation->listErrors());
                    }
                }
            } else {
                return $this->failUnauthorized('Acceso no autorizado');
            }   
        } catch (\Exception $e) {
            return $this->failServerError('Ha ocurrido un error en el servidor '.$e);
        }
    }

    /**
    * Elimina una orden
    * @param int $id  de la orden
    */
    public function delete($id = NULL)
    {
        try{
            if($this->jefaturaSession()){
                if($id == NULL){
                    return $this->failValidationErrors('No se ha pasado un ID valido');
                }
                $order = $this->model->find($id);
                if($order == NULL){
                    return $this->failNotFound('No se ha encontrado el usuario con el ID: '.$id);
                }
                if($this->model->delete($id)){
                    $path_to_file = $order['file_url'];
                    unlink(ROOTPATH . $path_to_file);
                    return $this->respondDeleted($order);
                } else {
                    return $this->failServerError('No se ha podido eliminar el registro');
                }
            } else {
                return $this->failUnauthorized('Acceso no autorizado ');
            }   
        } catch (\Exception $e) {
            return $this->failServerError('Ha ocurrido un error en el servidor');
        }
    }

    /**
    * Verifica que el usuario con session abierta tiene rol jefatura
    * @return boolean true si es un usuario con rol jefatura
    */
    private function jefaturaSession(){
        $session = session();
        return ($session->get('user_role')) == 'jefatura';
    }

    /**
    * Verifica que el usuario con session abierta tiene rol jefatura
    * @return boolean true si es un usuario con rol jefatura
    */
    private function dependenciaSession(){
        $session = session();
        return ($session->get('user_role')) == 'dependencia';
    }
}
