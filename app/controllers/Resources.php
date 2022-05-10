<?php

namespace App\Controllers;

use Core\{Controller, Router};
use Core\Helpers\Response;

/**
 * Resources Controller
 *
 * @author Mohammed-Aymen Benadra
 * @package App\Controllers
 */
class Resources extends Controller
{
    private $model;
    /**
     * Set headers for JSON response
     *
     * @return void
     */
    public function __construct()
    {
        // Set default Model for this controller
        $this->model = $this->model('Resource');

        Response::headers();
        Response::code();
    }

    /**
     * Get all resources
     *
     * @return void
     */
    public function index()
    {
        $resources = $this->model->getAll();

        Response::send([
            'status' => 'success',
            'data' => $resources,
            'count' => count($resources)
        ]);
    }

    /**
     * Get a resource
     *
     * @param array $data
     * @return void
     */
    public function show($data = [])
    {
        Response::send([
            'status' => 'success',
            'data' => $this->model->get($data['resource_id'])
        ]);
    }

    /**
     * Store a resource
     *
     * @param array $data
     * @return void
     */
    public function store($data = [])
    {
        if (!$this->model->add($data)) {
            Router::abort(500, [
                'status' => 'error',
                'message' => 'Server error'
            ]);
        }

        Response::code(201);
        Response::send([
            'status' => 'Created successfully.'
        ]);
    }

    /**
     * Update an resource
     *
     * @param array $data
     * @return void
     */
    public function update($data = [])
    {
        $id = $data['resource_id'];
        unset($data['resource_id']);

        if (!$this->model->update($id, $data)) {
            Router::abort(500, [
                'status' => 'error',
                'message' => 'Server error'
            ]);
        }

        Response::send([
            'status' => 'Updated successfully.'
        ]);
    }

    /**
     * Toggle visited status
     * 
     * @param array $data
     * @return void
     */
    public function toggleVisited($data = [])
    {
        // check if resource exists
        $resource = $this->model->get($data['resource_id']);

        $resource->is_visited = !$resource->is_visited;

        if (!$this->model->update($data['resource_id'], ['is_visited' => $resource->is_visited])) {
            Router::abort(500, [
                'status' => 'error',
                'message' => 'Server error'
            ]);
        }

        Response::send([
            'status' => 'Toggled successfully.',
            'data' => [
                'visited' => $resource->is_visited
            ]
        ]);
    }

    /**
     * Delete a resource
     *
     * @param array $data
     * @return void
     */
    public function destroy($data = [])
    {
        if (!$this->model->delete($data['resource_id'])) {
            Router::abort(500, [
                'status' => 'error',
                'message' => 'Server error'
            ]);
        }

        Response::send([
            'status' => 'Deleted successfully.'
        ]);
    }
}
