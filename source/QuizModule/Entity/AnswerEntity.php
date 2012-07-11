<?php
namespace QuizModule\Entity;

use Kernel\Entity\AbstractEntity;

/**
 * Description of AnswerEntity
 * @@TableName = answers
 * @@TableComment = Table for questions' answer
 * @@TableType = InnoDB
 *
 * @author Alexey Korolev <koroleffas@gmail.com>
 */
class AnswerEntity extends AbstractEntity {
    protected $text;

    public function __construct(array $properties = array())
    {
        $this->load($properties,__CLASS__,__FILE__);

    }
    public function getText() {
        return $this->text;
    }

    public function setText($text) {
        $this->text = $text;

        return $this;
    }


}
