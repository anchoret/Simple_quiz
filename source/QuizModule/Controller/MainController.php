<?php
namespace QuizModule\Controller;

use Kernel\Controller\AbstractController;
use Kernel\HTTP\Response;

class MainController extends AbstractController
{
    public function indexAction()
    {
        return new Response('Привет, Мир!!');
    }
}