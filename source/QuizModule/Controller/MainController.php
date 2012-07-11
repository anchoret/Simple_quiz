<?php
namespace QuizModule\Controller;

use Kernel\Controller\AbstractController;
use Kernel\HTTP\Response;

class MainController extends AbstractController
{
    public function indexAction()
    {
        $cont = \Kernel\ServiceContainer\Container::getInstance();
        $connector = $cont->get('db.connector');
        $answer = new \QuizModule\Entity\AnswerEntity(array('id'=>7,'text'=>'Первый ответ'));
        die(var_dump($answer));
        return new Response('Привет, Мир!!');
    }
}