<?php
namespace Controllers;

class AccueilController extends BaseController {
    
    public function __construct() {
        parent::__construct();
        $this->checkAuth();
    }
    
    public function index() {
        $this->renderWithLayout('accueil/index');
    }
}