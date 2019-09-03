<?php
namespace app\controller;

use jframe\controller\Controller;

class Index extends Controller
{

    public function index()
    {
        $req = json_decode($this->request->rawContent(), true);

        return $req;
    }

}
