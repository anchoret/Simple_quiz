<?php
namespace QuizModule\Controller;

use Kernel\Controller\AbstractController;

class MainController extends AbstractController
{
    public function indexAction($id, $opt, $count, $view = 555)
    {
        return 'Привет, Мир!!';
    }
}