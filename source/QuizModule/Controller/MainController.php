<?php
namespace QuizModule\Controller;

use Kernel\Controller\AbstractController;
use Kernel\HTTP\Response;

class MainController extends AbstractController
{
    public function indexAction()
    {
        $this->view->name = 'Алексей Сергеевич';
        return "quiz::main::index";
    }
}