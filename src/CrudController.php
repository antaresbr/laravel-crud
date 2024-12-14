<?php

namespace Antares\Crud;

use Antares\Foundation\Obj;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CrudController extends Controller
{
    /**
     * Handler instance for this controller
     *
     * @var \Antares\Crud\CrudHandler
     */
    protected $handlerInstance;

    /**
     * Handler instance for this controller acessor
     *
     * @return \Antares\Crud\CrudHandler
     */
    protected function handler()
    {
        if (!isset($this->handlerInstance)) {
            $handlerClass = Obj::get($this, 'handlerClass');
            $this->handlerInstance = $handlerClass::make();
        }
        return $this->handlerInstance;
    }

    /**
     * Execute de authorization for the method, if aclAuthorize method is defined
     *
     * @param string $method
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function crudAuthorize($method)
    {
        if (method_exists($this, 'aclAuthorize')) {
            $sourceName = 'acl' . ucfirst(strtolower($method)) . 'Action';
            if (method_exists($this, $sourceName)) {
                $action = $this->{$sourceName}();
            } elseif (property_exists($this, $sourceName)) {
                $action = $this->{$sourceName};
            } else {
                $action = $method;
            }
            return $this->aclAuthorize($action);
        }
        return true;
    }

    /**
     * Get metadata of the the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function metadata(Request $request)
    {
        if (($acl = $this->crudAuthorize(__FUNCTION__)) !== true) {
            return $acl;
        }
        return $this->handler()->metadata($request);
    }

    /**
     * Crud search - index for datasourdes
     *
     * @return \Illuminate\Http\Response
     */
    public function search(Request $request)
    {
        if (($acl = $this->crudAuthorize(__FUNCTION__)) !== true) {
            return $acl;
        }
        return $this->handler()->search($request);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if (($acl = $this->crudAuthorize(__FUNCTION__)) !== true) {
            return $acl;
        }
        return $this->handler()->index($request);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (($acl = $this->crudAuthorize(__FUNCTION__)) !== true) {
            return $acl;
        }
        return $this->handler()->store($request);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (($acl = $this->crudAuthorize(__FUNCTION__)) !== true) {
            return $acl;
        }
        return $this->handler()->show($id);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if (($acl = $this->crudAuthorize(__FUNCTION__)) !== true) {
            return $acl;
        }
        return $this->handler()->update($request, $id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (($acl = $this->crudAuthorize(__FUNCTION__)) !== true) {
            return $acl;
        }
        return $this->handler()->destroy($id);
    }
}
