<?php

namespace App\Controllers\API;

use CodeIgniter\RESTful\ResourceController;

class Volantes extends ResourceController
{

    protected $modelName = 'App\Models\VolanteModel';
    protected $format    = 'json';
    private $upload_path = 'public/uploads/volantes/'; //path for upload
    private $allowed_types = 'pdf'; //restrict extension
    private $max_size = 2048;

    /**
     * Retorna la lista de los volantes enviados por el usuario
     * @return json con la lista de volantes
     */
    public function enviados()
    {
        return $this->respond($this->model->findAll());
    }

    /**
     * Retorna la lista de los volantes destinados al usuario 
     * @return json con la lista de volantes
     */
    public function recibidos()
    {
        return $this->respond($this->model->findAll());
    }

    
    /**
     * Crea un volante     
     * @var post year, number, file adjunto  
     * falta revisar
     */
    public function create()
    {   
        $archivo = $this->request->getFile('archivo');
        if ($archivo == NULL) {
            return $this->failServerError('No cargo un archivo');
        }

        $year = $this->request->getPost('year'); 

        // Hay que cambiar esto deben ser numero correlativos
        $number = $this->request->getPost('number');

        $session = session();
        $user = $session->get('user_id');

        // nombre del archivo vol y adj
        $name = $user.'_'.$year.'_'.$number.'.pdf';
        $archivo_ext = $archivo->getClientExtension();
        $archivo_size = $archivo->getSize() / 1024;

        if (!$archivo->isValid() || $archivo_ext != $this->allowed_types || $archivo_size > $this->max_size) {
            return $this->failServerError('No es un archivo valido debe ser pdf menor 2mb');
        }

        $adjunto = $this->request->getFile('adjunto');
        if ($adjunto != NULL) {
            $ext = $adjunto->getClientExtension();
            $size = $adjunto->getSize() / 1024;

            if ($size > 0) {
                if (!$adjunto->isValid() || $ext != $this->allowed_types || $size > $this->max_size) {
                    return $this->failServerError('No es un adjunto valido debe ser pdf menor 2mb');
                }    
            }
        }

        $fecha = $this->request->getPost('fecha');
        $destino = $this->request->getPost('destino');
        $asunto =  $this->request->getPost('asunto');
        $archivo_path = $this->upload_path . 'vol_'. $name;
        $adjunto_path = ($adjunto != NULL)? $this->upload_path . 'adj_'. $name : '';
        $data = [
            'estado' => 'emitido',
            'number' => $number,
            'year' => $year,
            'fecha' => $fecha, 
            'origen' => $user,
            'destino' => $destino,
            'asunto' => $asunto, 
            'enlace_archivo' => $archivo_path, 
            'enlace_adjunto' => $adjunto_path
        ];

        if ($this->model->insert($data)) {
            $data['id'] = $this->model->insertID();
            if ($archivo->move(ROOTPATH, $archivo_path)) {
                if ($adjunto_path != '') {
                    $adjunto->move(ROOTPATH, $adjunto_path);
                }
                return $this->respondCreated($data);
            } else {
                return $this->failServerError('No se pudo cargar el archivo ');
            }
        } else {
            return $this->failValidationErrors($this->model->validation->listErrors());
        }
    }

    /**
     * Devuelve el volante a editar
     * @return json volante
     * @param $id del volante  
     */
    public function edit($id = NULL)
    {
        //
    }

    /**
     * Actualiza los cambios de un volante editado
     * @param int $id del volante
     * @var post estado, year, number, file adjunto  
     */
    public function update($id = NULL)
    {
        //
    }

    /**
     * Elimina un volante
     * @param int $id del volante
     * 
     * falta revisar
     */
    public function delete($id = NULL)
    {
        if($id == NULL){
            return $this->failValidationErrors('No se ha pasado un ID valido');
        }

        $volante = $this->model->find($id);
        
        if($volante == NULL){
            return $this->failNotFound('No se ha encontrado el usuario con el ID: '.$id);
        }

        // solo usuario origen puede borrar el volante

        $session = session();
        $user = $session->get('user_id');

        if($user == $volante['origen']){
            if($this->model->delete($id)){
                $archivo = $volante['enlace_archivo'];
                unlink(ROOTPATH . $archivo);
                $adjunto = $volante['enlace_adjunto'];
                if($adjunto != ''){
                    unlink(ROOTPATH . $adjunto);
                }
                return $this->respondDeleted($volante);
            } else {
                return $this->failServerError('No se ha podido eliminar el registro');
            }
        } else {
            return $this->failValidationErrors('El volante no le pertenece al usuario');
        }
         
    }
}
