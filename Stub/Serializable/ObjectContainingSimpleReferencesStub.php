<?php
namespace jvdh\Serialization\Stub\Serializable;

class ObjectContainingSimpleReferencesStub
{
    public $publicFirstValue;
    public $publicSecondValue;
    public $publicThirdValue;
    public $publicFourthValue;
    public $publicFifthValue;

    public function __construct()
    {
        $this->publicFirstValue = 'lorem ipsum dolem';
        $this->publicSecondValue = 2;
        $this->publicThirdValue = &$this->publicFirstValue;
        $this->publicFourthValue = 4.0;
        $this->publicFifthValue = &$this->publicSecondValue;
    }
}
